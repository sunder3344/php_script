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
$tableName = 't_coupon_list';		//hash及范围
set_time_limit(0);
$count = 0;
$data = array();

do {
	$request = array(
			'TableName' => $tableName,
			'ProjectionExpression' => 'card_no, create_time',			//赠送原则
			'ScanIndexForward' => false,
			'Limit' => 100,
	);
	if(isset($response) && isset($response['LastEvaluatedKey'])) {
		$request['ExclusiveStartKey'] = $response['LastEvaluatedKey'];
	}
	$response = $client->scan($request);
	$now = time();
	foreach ($response['Items'] as $v) {
		if (isset($v['card_no']['S'])) {
			if ($v['card_no']['S'] == 'SLZJ') {
// 				echo $v['create_time']['N'].PHP_EOL;
				$time = (int) $v['create_time']['N'];
				if ($v['create_time']['N'] >= 1462896000 && $v['create_time']['N'] < 1464710400) {
					++$count;
				}
			}
		}
	}
} while (isset($response['LastEvaluatedKey']));

echo $count;