<?php
require_once "./aws/aws-autoloader.php";

use Aws\DynamoDb\DynamoDbClient;
use Aws\S3\S3Client;

set_time_limit(0);
$client = DynamoDbClient::factory(array(
	'profile' => 'default',
	'region' => 'cn-north-1',
	'version' => 'latest',
));

date_default_timezone_set("UTC");
$tableName = 'vod_auth';		//hash及范围

$row = 1;
$handle = fopen("222.csv","r");
$start = '1463039592';
// $end = time() + 60*60*24*30;
$count = 0;
while ($data = fgetcsv($handle, 1000, ",")) {
	$vid = $data[1];
	$end = '4618713192';
	var_dump($vid . '-' . $end). PHP_EOL;
	$count++;
// 	$response = $client->putItem(array(
// 			'TableName' => $tableName,
// 			'Item' => array(
// 					'vid' => array('N' => (string)$vid),
// 					'start_time' => array('N' => (string)$start),
// 					'end_time' => array('N' => (string)$end),
// 			)
// 	));
}
fclose($handle);
echo $count.PHP_EOL;
echo 'success';