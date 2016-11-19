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
$tableName = 't_user';		//hash及范围
set_time_limit(0);
$count = 0;

do {
	$request = array(
			'TableName' => $tableName,
			'ProjectionExpression' => 'vip_begin_time, vip_end_time',			//赠送原则
			'ScanIndexForward' => false,
			'Limit' => 100,
	);
	if(isset($response) && isset($response['LastEvaluatedKey'])) {
		$request['ExclusiveStartKey'] = $response['LastEvaluatedKey'];
	}
	$response = $client->scan($request);
	$now = time();
	foreach ($response['Items'] as $v) {
		if (isset($v['vip_begin_time']['N']) && isset($v['vip_end_time']['N'])) {
			if ($v['vip_end_time']['N'] >= $now && $v['vip_begin_time']['N'] <= $now) {
				++$count;
			}	
		}
	}
} while (isset($response['LastEvaluatedKey']));

echo 'count:=' . $count;