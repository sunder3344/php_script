<?php
require_once "./aws/aws-autoloader.php";

use Aws\DynamoDb\DynamoDbClient;
use Aws\S3\S3Client;


function randpw($len = 1, $format='ALL'){
	$arr = range(0, 9);
	shuffle($arr);
	$pwd = "";
	for ($i = 0; $i < $len; $i++) {
		$pwd = $pwd . $arr[$i];
	}
	return $pwd;
}

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
$tableName = 't_card_test';		//hash及范围
set_time_limit(0);
$count = 0;

do {
	$request = array(
			'TableName' => $tableName,
			'ProjectionExpression' => 'card_no, card_pwd',
			'ScanIndexForward' => false,
			'Limit' => 100,
	);
	if(isset($response) && isset($response['LastEvaluatedKey'])) {
		$request['ExclusiveStartKey'] = $response['LastEvaluatedKey'];
	}
	$response = $client->scan($request);
	$now = time();
	foreach ($response['Items'] as $v) {
		if (isset($v['card_no']['S']) && isset($v['card_pwd']['S'])) {
			++$count;
		}
	}
} while (isset($response['LastEvaluatedKey']));

echo 'count:=' . $count;