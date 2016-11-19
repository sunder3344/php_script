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

do {
	$request = array(
			'TableName' => $tableName,
			'ProjectionExpression' => 'user_uuid, origin',			//赠送原则
			'ScanIndexForward' => false,
			'Limit' => 100,
	);
	if(isset($response) && isset($response['LastEvaluatedKey'])) {
		$request['ExclusiveStartKey'] = $response['LastEvaluatedKey'];
	}
	$response = $client->scan($request);
	$now = time();
	foreach ($response['Items'] as $v) {
		if (isset($v['user_uuid']['S'])) {
			$key = 'default';
			if (isset($v['origin']['S'])) {
				$key = $v['origin']['S'];
			}
			if (isset($data[$key])) {
				++$data[$key];
			} else {
				$data[$key] = 1;
			}
		}
	}
} while (isset($response['LastEvaluatedKey']));

print_r($data);