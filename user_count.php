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

$fp = fopen('./duplicate.csv', 'w');
do {
	$request = array(
			'TableName' => $tableName,
			'ProjectionExpression' => 'user_uuid, register_time, cellphone',			//赠送原则
			'ScanIndexForward' => false,
			'Limit' => 1000,
	);
	if(isset($response) && isset($response['LastEvaluatedKey'])) {
		$request['ExclusiveStartKey'] = $response['LastEvaluatedKey'];
	}
	$response = $client->scan($request);
	$now = time();
	foreach ($response['Items'] as $v) {
		if (isset($v['cellphone']['S'])) {
			$phone = $v['cellphone']['S'];
			$response2 = $client->query(array(
								'TableName' => $tableName,
								'IndexName' => 'cellphone-user_uuid-index',
								'KeyConditionExpression' => 'cellphone = :phone',
								'ExpressionAttributeValues' => array(
										':phone' => array('S' => $phone),
								),
								'ProjectionExpression' => 'id, score',
								'ConsistantRead' => true,
								'Limit' => 20,
						));
			$count = $response2['Count'];
			if ($count > 1) {
				//写入csv
				fputcsv($fp, array($phone));
			}
		}
	}
} while (isset($response['LastEvaluatedKey']));
fclose($fp);
echo 'success';