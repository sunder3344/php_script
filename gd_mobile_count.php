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
			'ProjectionExpression' => 'user_uuid, origin, cellphone',			//赠送原则
			'ScanIndexForward' => false,
			'Limit' => 1000,
	);
	if(isset($response) && isset($response['LastEvaluatedKey'])) {
		$request['ExclusiveStartKey'] = $response['LastEvaluatedKey'];
	}
	$response = $client->scan($request);
	$now = time();
	foreach ($response['Items'] as $v) {
		if (isset($v['user_uuid']['S'])) {
			//检查订单
			$response2 = $client->query(array(
					'TableName' => 'order_info',
					'IndexName' => 'user_id-pay_status-index',
					'KeyConditionExpression' => 'user_id = :user_id',
					'ExpressionAttributeValues' => array(
							':user_id' => array('S' => $v['user_uuid']['S']),
					),
					'ProjectionExpression' => 'order_id, product_id',
					'ConsistantRead' => true,
					'Limit' => 10,
			));
			foreach ($response2['Items'] as $vv) {
				if (isset($vv['product_id']['N'])) {
					if ($vv['product_id']['N'] == 8) {
						$count++;
						break;
					}
				}
			}
		}
	}
} while (isset($response['LastEvaluatedKey']));
var_dump('9.9元用户一共：'.$count);
$fp = fopen('./gd_mobile_9.csv', 'w');
fputcsv($fp, array($count));
fclose($fp);
