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
$tableName = 't_card';		//hash及范围
set_time_limit(0);
$filename = "./card_check.txt";
$handler = fopen($filename, "r");
$fp = fopen('./card_check.csv', 'w');
$count = 1;
while(!feof($handler)) {
	$m = fgets($handler,4096); //fgets逐行读取，4096最大长度，默认为1024
	$arr = explode("#", $m);
	$card_no = trim($arr[0]);
	$card_pwd = trim($arr[1]);
	$response = $client->query(array(
						'TableName' => $tableName,
						'IndexName' => 'card_no-index',
						'KeyConditionExpression' => 'card_no = :card_no',
						'ExpressionAttributeValues' => array(
							':card_no' => array('S' => (string)$card_no),
						),
						'ProjectionExpression' => 'card_no, end_time, is_used',
						'ConsistantRead' => true,
						'Limit' => 1,
				));
	if (isset($response['Items'][0]['card_no']['S'])) {
		$end_time = $response['Items'][0]['end_time']['N'];
		$is_used = $response['Items'][0]['is_used']['N'];
		$time = date("Y-m-d H:i:s", $end_time);
		fputcsv($fp, array($card_no, $card_pwd, $time, $is_used));
	}
	++$count;
}
fclose($handler);
echo 'success';