<?php
require_once "./aws/aws-autoloader.php";

use Aws\DynamoDb\DynamoDbClient;
use Aws\S3\S3Client;


$client = DynamoDbClient::factory(array(
// 	'profile' => 'default',
	'region' => 'cn-north-1',
	'version' => 'latest',
	'credentials' => [
			'key'    => '',
			'secret' => '',
	],
));

function curlProtect($url, $launch = 'post',
		$contentType = 'text/html', $postData = array(),$header = array()) {
	$result = "";
	try {
		if(empty($header)){
			$header = array("Content-Type:" . $contentType . ";charset=utf-8");
		}
		//print_r($header);
		if (!empty($_SERVER['HTTP_USER_AGENT'])) {		//是否有user_agent信息
			$user_agent = $_SERVER['HTTP_USER_AGENT'];
		}
		$cur = curl_init();
		//优先使用ipv4
		if(defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')){
			curl_setopt($cur, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		}
		curl_setopt($cur, CURLOPT_URL, $url);
		curl_setopt($cur, CURLOPT_HEADER, 0);
		curl_setopt($cur, CURLOPT_HTTPHEADER, $header);
		curl_setopt($cur, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($cur, CURLOPT_TIMEOUT, 5);
		//https
		curl_setopt($cur, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($cur, CURLOPT_SSL_VERIFYHOST, FALSE);
		if (isset($user_agent)) {
			curl_setopt($cur, CURLOPT_USERAGENT, $user_agent);
		}
		curl_setopt($cur, CURLOPT_ENCODING, 'gzip');
		if (is_array($postData)) {
			if ($postData && count($postData) > 0) {
				$params = http_build_query($postData);
				if ($launch=='get') {		//发送方式选择
					curl_setopt($cur, CURLOPT_HTTPGET, $params);
				} else {
					curl_setopt($cur, CURLOPT_POST, true);
					curl_setopt($cur, CURLOPT_POSTFIELDS, $params);
				}
			}
		} else {
			if (!empty($postData)) {
				$params = $postData;
				if ($launch=='post') {
					curl_setopt($cur, CURLOPT_POST, true);
					curl_setopt($cur, CURLOPT_POSTFIELDS, $params);
				}
			}
		}
		$result = curl_exec($cur);
		curl_close($cur);
	} catch (Exception $e) {
			
	}
	return $result;
}

date_default_timezone_set("UTC");
$tableName = 't_user';		//hash及范围
set_time_limit(0);
$count = 0;
$data = array();


$fp = fopen('./cellphone_sh.csv', 'w');
$fp2 = fopen('./cellphone_order_sh.csv', 'w');
do {
	$request = array(
			'TableName' => $tableName,
			'ProjectionExpression' => 'user_uuid, cellphone, gender',			//赠送原则
			'ScanIndexForward' => false,
			'Limit' => 100,
	);
	if(isset($response) && isset($response['LastEvaluatedKey'])) {
		$request['ExclusiveStartKey'] = $response['LastEvaluatedKey'];
	}
	$response = $client->scan($request);
	$now = time();
	$url = "https://www.baifubao.com/callback?cmd=1059&callback=phone&phone=";
	foreach ($response['Items'] as $v) {
		if (isset($v['user_uuid']['S'])) {
			$phone = '';
			if (isset($v['cellphone']['S']) && isset($v['gender']['N']) && !empty($v['gender']['N'])) {
				$cellphone = $v['cellphone']['S'];		//需要判断手机用户是上海地区的
				$res = curlProtect($url . $cellphone, 'get');
				preg_match_all ("/\/\*fgg_again\*\/phone\((.*)\)/", $res, $matches);
				$json = $matches[1][0];
				$arr = json_decode($json, true);
				$city = $arr['data']['area'];
				if (isset($city) && $city == '上海') {
					$phone = '86' . $v['cellphone']['S'];
					//md5加密
					$content = strtoupper(md5($phone));
					//导出csv
					$gender_val = isset($v['gender']['N'])?$v['gender']['N']:'1';
					$gender = 'male';
					if ($gender_val == 2) {
						$gender = 'female';
					}
					fputcsv($fp, array($phone, $content, $gender));
					//获取用户订单
					$request = array(
									'TableName' => 'order_info',
									'IndexName' => 'user_id-pay_status-index',
									'KeyConditionExpression' => 'user_id = :userId and pay_status > :pay_status',
									'ExpressionAttributeValues' => array(
											':userId' => array('S' => (string)$v['user_uuid']['S']),
											':pay_status' => array('N' => '0'),
									),
									'ProjectionExpression' => 'order_id, subject, body, create_time, pay_status, pay_type, total_fee, product_id, expire_date',
									'ConsistantRead' => true,
									'ScanIndexForward' => false,
									'Limit' => 30,
								);
					$res = $client->query($request);
					foreach ($res['Items'] as $vv) {
						$order_id = isset($vv['order_id']['N'])?$vv['order_id']['N']:'';
						$subject = isset($vv['subject']['S'])?$vv['subject']['S']:'';
						$body = isset($vv['body']['S'])?$vv['body']['S']:'';
						$create_time = isset($vv['create_time']['N'])?$vv['create_time']['N']:'';
						$pay_status = isset($vv['pay_status']['N'])?$vv['pay_status']['N']:'';
						$pay_type = isset($vv['pay_type']['N'])?$vv['pay_type']['N']:'';
						$total_fee = isset($vv['total_fee']['N'])?$vv['total_fee']['N']:'0';
						$product_id = isset($vv['product_id']['N'])?$vv['product_id']['N']:'';
						$expire_date = isset($vv['expire_date']['N'])?$vv['expire_date']['N']:'';
						
						fputcsv($fp2, array($phone, $order_id, $subject, $body, $create_time, 
						$pay_status, $pay_type, $total_fee, $product_id, $expire_date));
					}
					++$count;
				}
			}
		}
	}
} while (isset($response['LastEvaluatedKey']));
fclose($fp);
fclose($fp2);
echo "success";