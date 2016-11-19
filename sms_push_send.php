<?php
require_once "./aws/aws-autoloader.php";
require_once 'function.php';

use Aws\DynamoDb\DynamoDbClient;
use Aws\S3\S3Client;

set_time_limit(0);

function sendMsg($cellphone, $content) {
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL, "http://11.123.61.157/mas-dataface/sendmessage.php");
	$data = array('content' => $content, 'phone' => $cellphone);
	$data = http_build_query($data);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
	$response = curl_exec($ch);
	if (strpos($response, "success")) {      //验证码发送成功
		$errorcode = 0;
	} else {
		$errorcode = 111;
	}
	return $errorcode;
}


$client = DynamoDbClient::factory(array(
// 	'profile' => 'default',
	'region' => 'cn-north-1',
	'version' => 'latest',
	'credentials' => [
			'key'    => '',
			'secret' => '',
	],
));

date_default_timezone_set("UTC");
$tableName = 'sms_push_test';		//hash及范围
set_time_limit(0);
$count = 0;
$data = array();

$sqlhead = "insert into t_mt_queue (mtq_mobile,mtq_channel_id,mtq_content,mtq_level,mtq_plan_time,mtq_create_time,mtq_status) values";
do {
	$request = array(
			'TableName' => $tableName,
			'ProjectionExpression' => 'cellphone, pushed',
			'ScanIndexForward' => false,
			'Limit' => 1000,
	);
	if(isset($response) && isset($response['LastEvaluatedKey'])) {
		$request['ExclusiveStartKey'] = $response['LastEvaluatedKey'];
	}
	$response = $client->scan($request);
	$now = time();
	$content = "3个月仅需30元，即日起限时四天会员大促，年费会员118元尽享NBA精彩直播赛事一整季！详情请戳qr28.cn/DQ5iId";
// 	$fp = fopen('./sms_current.csv', 'w');
	$sql = "";
	foreach ($response['Items'] as $v) {
		if (isset($v['cellphone']['S'])) {
			$cellphone = trim($v['cellphone']['S']);
			$pushed = trim($v['pushed']['N']);
			if ($pushed == 1) {
				//发送短信
				$pt=substr($cellphone,0,3);
				$channel_id=1;//默认是移动
				require_once 'leftphone.php';
				if(in_array($pt,$unicomNumber)){//联通？
					$channel_id=2;
				}
				if(in_array($pt,$steNumber)){//电信？
					$channel_id=3;
				}
				$sdate=date('Y-m-d H:i:s');
				if (empty($sql)) {
					$sql = " ('$cellphone',$channel_id,'$content',100,'$sdate','$sdate','N')";
				} else {
					$sql = $sql . ",('$cellphone',$channel_id,'$content',100,'$sdate','$sdate','N')";
				}
				$resCode = 0;
				if ($resCode == 0) {			//发送成功写入
					$res = $client->updateItem(array(
										'TableName' => $tableName,
										'Key' => array(
												'cellphone' => array('S' => (string)$cellphone),
										),
										'ExpressionAttributeValues' => array(
												':param1' => array('N' => '2'),
										),
										'UpdateExpression' => 'set pushed = :param1',
								));
				}
				$count++;
			}
		}
	}
	$sql = $sqlhead . $sql;
	$conn=connectDB();
	$stmt = sqlsrv_query($conn,$sql);
	
} while (isset($response['LastEvaluatedKey']));
// fclose($fp);
echo "count:=" . $count . PHP_EOL;