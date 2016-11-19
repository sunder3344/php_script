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
$tableName = 'sh_unicom';		//hash及范围
set_time_limit(0);
$count = 0;
$data = array();

$fp = fopen('sh_daily_unicom_list.csv', 'w');
$endDate = date("Y-m-d");
do {
	$request = array(
			'TableName' => $tableName,
			'ProjectionExpression' => 'cellphone, get_date, get_time',			//赠送原则
			'ScanIndexForward' => false,
			'Limit' => 100,
	);
	if(isset($response) && isset($response['LastEvaluatedKey'])) {
		$request['ExclusiveStartKey'] = $response['LastEvaluatedKey'];
	}
	$response = $client->scan($request);
	$now = time();
	foreach ($response['Items'] as $v) {
		if (isset($v['cellphone']['S'])) {
			$key = '';
			if (isset($v['get_date']['N'])) {
				$date = $v['get_date']['N'];
			}
			if ($date == 20161117) {
				$purchase_date = $v['get_time']['S'];
				fputcsv($fp, array($v['cellphone']['S'], $purchase_date));
			}
		}
	}
} while (isset($response['LastEvaluatedKey']));

echo "success";