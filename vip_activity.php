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
			'ProjectionExpression' => 'vip_begin_time, vip_end_time, user_uuid, cellphone',			//赠送原则
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
			if ($v['vip_end_time']['N'] < 1466006400) {			//此类人员开始时间为6-16，结束时间为6-17
				//修改会员日期
				$res = $client->updateItem(array(
									'TableName' => $tableName,
									'Key' => array(
											'user_uuid' => array('S' => (string)$v['user_uuid']['S']),
									),
									'ExpressionAttributeValues' => array(
											':param1' => array('N' => '1466006400'),
											':param2' => array('N' => '1466092800'),
									),
									'UpdateExpression' => 'set vip_begin_time = :param1, vip_end_time = :param2',
									'ReturnConsumedCapacity' => 'TOTAL'
							));
				$num = $res->getPath('ConsumedCapacity/CapacityUnits');
				echo $v['user_uuid']['S'] . ':==' . $num . PHP_EOL;
			} else if ($v['vip_end_time']['N'] >= 1466006400 && $v['vip_end_time']['N'] < 1466092800) {			//此类会员结束日期直接定为6-17即可
				//修改会员日期
				$res = $client->updateItem(array(
									'TableName' => $tableName,
									'Key' => array(
											'user_uuid' => array('S' => (string)$v['user_uuid']['S']),
									),
									'ExpressionAttributeValues' => array(
											':param2' => array('N' => '1466092800'),
									),
									'UpdateExpression' => 'set vip_end_time = :param2',
									'ReturnConsumedCapacity' => 'TOTAL'
							));
				$num = $res->getPath('ConsumedCapacity/CapacityUnits');
				echo $v['user_uuid']['S'] . ':==' . $num . PHP_EOL;
			}
		}
	}
} while (isset($response['LastEvaluatedKey']));

echo $count;
echo 'success';