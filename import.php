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
$filename = "./20.txt";
$handler = fopen($filename, "r");
$end_time = strtotime('2018-01-01 00:00:00');
$count = 1;
while(!feof($handler)) {
	$m = fgets($handler,4096); //fgets逐行读取，4096最大长度，默认为1024
	$arr = explode("#", $m);
	$card_no = trim($arr[0]);
	$card_pwd = trim($arr[1]);
	echo $count . ': ' . $card_no . '-' . $card_pwd . PHP_EOL;
	$response = $client->putItem(array(
						'TableName' => $tableName,
						'Item' => array(
								'card_no' => array('S' => (string)$card_no),
								'card_pwd' => array('S' => (string)$card_pwd),
								'is_used' => array('N' => '0'),
								'product_id' => array('N' => '11'),
								'start_time' => array('N' => (string)time()),
								'end_time' => array('N' => (string)$end_time),
								'create_time' => array('N' => (string)time()),
						),
						'ReturnConsumedCapacity' => 'TOTAL'
				));
	++$count;
}
fclose($handler);
echo 'success';