<?php
set_time_limit(0);
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

$count = 0;
$result = array();
do {
	$request = array(
			'TableName' => $tableName,
			'ProjectionExpression' => 'user_uuid, vip_begin_time, vip_end_time, cellphone',			//赠送原则
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
			$uuid = $v['user_uuid']['S'];
			$phone = '';
			if (isset($v['cellphone']['S']) && !empty($v['cellphone']['S'])) {
				$phone = $v['cellphone']['S'];
			}
			if (!empty($phone)) {
				//查看支付记录
				$res = $client->query(array(
			    			'TableName' => 'order_info',
			    			'IndexName' => 'user_id-pay_status-index',
			    			'KeyConditionExpression' => 'user_id = :userId',
			    			'ExpressionAttributeValues' => array(
			    					':userId' => array('S' => (string)$uuid),
			    			),
			    			'ProjectionExpression' => 'order_id, pay_status, pay_type',
			    			'ConsistantRead' => true,
				    	));
				foreach ($res['Items'] as $v) {
					$payStatus = $v['pay_status']['N'];
					$payType = $v['pay_type']['N'];
					if (in_array($payType, array(1, 2, 3))) {
						if ($payStatus >= 2) {
							//保存
// 							echo '写入phone' . $phone . PHP_EOL;
// 							++$count;
// 							fputcsv($fp, array($phone));
// 							continue;
							if (!in_array($phone, $result)) {
								array_push($result, $phone);
							}
						}
					}
				}
			}
		}
	}
} while (isset($response['LastEvaluatedKey']));
$fp = fopen('./validaUserPhone.csv', 'w');
foreach ($result as $v) {
	++$count;
	fputcsv($fp, array($v));
}
fclose($fp);
echo '计费用户共:'.$count;