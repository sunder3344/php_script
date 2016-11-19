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
$tableName = 'sms_push';		//hash及范围
set_time_limit(0);
$filename = "./sms_push.txt";
$handler = fopen($filename, "r");
$count = 0;
while(!feof($handler)) {
	$m = fgets($handler,4096); //fgets逐行读取，4096最大长度，默认为1024
	$arr = explode("#", $m);
	$cellphone = trim($arr[0]);
	//判断sms_push表中是否存在
	$res = $client->getItem(array(
					'TableName' => $tableName,
					'Key' => array(
							'cellphone' => array('S' => (string)$cellphone),
					),
					'ProjectionExpression' => 'cellphone',
					'ConsistentRead' => true
			));
	if (!isset($res['Item']['cellphone']['S'])) {
		$response = $client->putItem(array(
							'TableName' => $tableName,
							'Item' => array(
									'cellphone' => array('S' => (string)$cellphone),
									'pushed' => array('N' => '0'),
							),
							'ReturnConsumedCapacity' => 'TOTAL'
					));
		++$count;
	}
}
fclose($handler);
echo 'success ' . $count;