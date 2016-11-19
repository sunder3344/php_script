<?php
header("Content-type: text/html; charset=utf-8");
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

do {
	$request = array(
			'TableName' => $tableName,
			'ProjectionExpression' => 'user_uuid, nickname, cellphone',			//赠送原则
			'ScanIndexForward' => false,
			'Limit' => 100,
	);
	if(isset($response) && isset($response['LastEvaluatedKey'])) {
		$request['ExclusiveStartKey'] = $response['LastEvaluatedKey'];
	}
	$response = $client->scan($request);
	$now = time();
	foreach ($response['Items'] as $v) {
		if (isset($v['nickname']['S'])) {
			if ($v['nickname']['S'] == '黑驴骑士') {
				echo $v['nickname']['S'].PHP_EOL;
				echo $v['user_uuid']['S'].PHP_EOL;
// 				echo $v['cellphone']['S'].PHP_EOL;
				break;
				exit();
			}
		}
	}
} while (isset($response['LastEvaluatedKey']));

print_r($data);