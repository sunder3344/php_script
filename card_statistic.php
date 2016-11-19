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
$filename = "./card_statistic.csv";
$handler = fopen($filename, "r");
$count = 0;
while(!feof($handler)) {
	$m = fgets($handler,4096); //fgets逐行读取，4096最大长度，默认为1024
	$m = trim($m);
	if (!empty($m)) {
		$response = $client->query(array(
							'TableName' => $tableName,
							'KeyConditionExpression' => 'card_no = :card_no',
							'ExpressionAttributeValues' => array(
									':card_no' => array('S' => (string)$m),
							),
							'ProjectionExpression' => 'card_no, is_used',
							'ConsistantRead' => true,
							'Limit' => 1,
					));
		if ($response['Count'] == 1) {
			//判断是否使用过
			$is_used = isset($response['Items'][0]['is_used']['N'])?$response['Items'][0]['is_used']['N']:'0';
			if ($is_used == 1) {
				++$count;
			}
		}
	}
}
fclose($handler);
echo '使用卡密为：' . $count . '个';