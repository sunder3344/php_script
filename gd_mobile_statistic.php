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

$fp = fopen('./gd_mobile_phone.csv', 'w');
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
			$key = 'default';
			$phone = '';
			if (isset($v['origin']['S'])) {
				$key = $v['origin']['S'];
			}
			if (isset($v['cellphone']['S'])) {
				$phone = $v['cellphone']['S'];
			}
			if ($key == 'GD_Mobile') {
				fputcsv($fp, array($phone));
			} else {
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
						if ($vv['product_id']['N'] == 8 || $vv['product_id']['N'] == 10) {
							fputcsv($fp, array($phone));
							break;
						}
					}
				}
			}
		}
	}
} while (isset($response['LastEvaluatedKey']));
fclose($fp);