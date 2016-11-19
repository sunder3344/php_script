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

// date_default_timezone_set("UTC");
date_default_timezone_set("Asia/Shanghai");
$tableName = 't_user';		//hash及范围
set_time_limit(0);
$count = 0;
$data = array();

$fp = fopen('sh_unicom_lost.csv', 'w');
$endDate = date("Y-m-d");
$filename = "./lost_phone.csv";
$handler = fopen($filename, "r");

while(!feof($handler)) {
	$phone = trim(fgets($handler,4096)); //fgets逐行读取，4096最大长度，默认为1024
	echo $phone . PHP_EOL;
	//去库中判断是否存在
	$res = $client->query(array(
			'TableName' => $tableName,
			'IndexName' => 'cellphone-user_uuid-index',
			'KeyConditionExpression' => 'cellphone = :cellphone',
			'ExpressionAttributeValues' => array(
					':cellphone' => array('S' => (string)$phone),
			),
			'ProjectionExpression' => 'user_uuid',
			'ConsistantRead' => true,
	));
	$uuid = "";
	if (isset($res['Items'][0]['user_uuid']['S'])) {
		$uuid = $res['Items'][0]['user_uuid']['S'];
	}
	if (empty($uuid)) {
		fputcsv($fp, array($phone));
	}
}
fclose($handler);
fclose($fp);
echo "success";