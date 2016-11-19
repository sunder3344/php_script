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
$tableName = 't_user';		//hash及范围
set_time_limit(0);
$count = 0;
$data = array();

$fp = fopen('sh_unicom_list.csv', 'w');
$endDate = date("Y-m-d");
do {
	$request = array(
			'TableName' => $tableName,
			'ProjectionExpression' => 'user_uuid, register_time, cellphone, activity_flag',			//赠送原则
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
			$key = 'default';
			if (isset($v['activity_flag']['S'])) {
				$key = $v['activity_flag']['S'];
			}
			if ($key == 'SH_UNICOM') {
				$reg_time = $v['register_time']['N'];
				if ($reg_time >= 1477238400) {
					fputcsv($fp, array($v['cellphone']['S'], date("Y-m-d", $v['register_time']['N']), date("H:i:s", $v['register_time']['N'])));
				}
			}
		}
	}
} while (isset($response['LastEvaluatedKey']));

echo "success";