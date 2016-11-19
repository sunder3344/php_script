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

date_default_timezone_set("UTC");
$tableName = 't_user';		//hash及范围
set_time_limit(0);
$count = 0;
$data = array();


$filename = "./cellphone_back.csv";
$handler = fopen($filename, "r");

$fp = fopen('./cellphone.csv', 'w');
$fp2 = fopen('./cellphone_order.csv', 'w');
while(!feof($handler)) {
	$m = fgets($handler,4096); //fgets逐行读取，4096最大长度，默认为1024
	$arr = explode(",", $m);
	$phone = trim($arr[0]);
	$content = trim($arr[1]);
	$cellphone = substr($phone, 2);
	//根据手机获取用户订单
	$response = $client->query(array(
	    			'TableName' => $tableName,
	    			'IndexName' => 'cellphone-user_uuid-index',
	    			'KeyConditionExpression' => 'cellphone = :cellphone',
	    			'ExpressionAttributeValues' => array(
	    					':cellphone' => array('S' => (string)$cellphone),
	    			),
	    			'ProjectionExpression' => 'user_uuid, gender',
	    			'ConsistantRead' => true,
					'Limit' => 1,
		    	));
	if (isset($response['Items'][0]['user_uuid']['S'])) {
		$uuid = $response['Items'][0]['user_uuid']['S'];
		$gender_val = isset($response['Items'][0]['gender']['N'])?$response['Items'][0]['gender']['N']:'0';
		$gender = 'male';
		if ($gender == 2) {
			$gender = 'female';
		}
		fputcsv($fp, array($cellphone, md5($cellphone), $gender));
		//获取用户订单
		$request = array(
				'TableName' => 'order_info',
				'IndexName' => 'user_id-pay_status-index',
				'KeyConditionExpression' => 'user_id = :userId and pay_status > :pay_status',
				'ExpressionAttributeValues' => array(
						':userId' => array('S' => (string)$uuid),
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
fclose($handler);
fclose($fp);
fclose($fp2);
echo "success";