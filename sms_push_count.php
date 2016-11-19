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
$count = 0;
$data = array();

do {
	$request = array(
			'TableName' => $tableName,
			'ProjectionExpression' => 'cellphone, pushed',
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
			if ($v['pushed']['N'] == 1) {
				$count++;
			}
		}
	}
} while (isset($response['LastEvaluatedKey']));
echo "count:=" . $count . PHP_EOL;