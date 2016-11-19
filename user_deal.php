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

$filename = "./duplicate.csv";
$handler = fopen($filename, "r");
$handler = fopen($filename, "r");
$count = 0;
while(!feof($handler)) {
	$m = fgets($handler, 4096); //fgets逐行读取，4096最大长度，默认为1024
	$m = trim($m);
	if (!empty($m)) {
		$response = $client->query(array(
							'TableName' => $tableName,
							'IndexName' => 'cellphone-user_uuid-index',
							'KeyConditionExpression' => 'cellphone = :cellphone',
							'ExpressionAttributeValues' => array(
									':cellphone' => array('S' => (string)$m),
							),
							'ProjectionExpression' => 'user_uuid, register_time, origin',
							'ConsistantRead' => true,
							'Limit' => 20,
					));
		if ($response['Count'] > 1) {
			var_dump($m);
			foreach ($response['Items'] as $item) {
				$uuid = $item['user_uuid']['S'];
				//检索订单
				$response2 = $client->query(array(
									'TableName' => 'order_info',
									'IndexName' => 'user_id-pay_status-index',
									'KeyConditionExpression' => 'user_id = :user_id',
									'ExpressionAttributeValues' => array(
											':user_id' => array('S' => (string)$uuid),
									),
									'ProjectionExpression' => 'order_id, product_id, expire_date',
									'ConsistantRead' => true,
									'Limit' => 20,
							));
				if ($response2['Count'] ==1 && $response2['Items'][0]['product_id']['N'] == 4) {
					//需要删除订单
					$response3 = $client->deleteItem(array(
										'TableName' => 'order_info',
										'Key' => array(
												'order_id' => array('N' => $response2['Items'][0]['order_id']['N']),
										)
								));
					//再删除用户
					$response4 = $client->deleteItem(array(
										'TableName' => $tableName,
										'Key' => array(
												'user_uuid' => array('S' => $uuid),
										)
								));
				} else {		//多个订单的
					$res = array();
					foreach ($response2['Items'] as $it) {
						$key = '';
						if (!isset($it['expire_date']['N'])) {
							var_dump($m);
						}
						$expire = $it['expire_date']['N'];
						$product_id = $it['product_id']['N'];
						$key = $product_id . '-' . $expire;
						if (empty($res)) {
							$res[$key] = 1;
						} else {
							if (isset($res[$key]) && $res[$key] == 1) {
								//再删除用户
								$response5 = $client->deleteItem(array(
													'TableName' => 'order_info',
													'Key' => array(
															'order_id' => array('N' => $it['order_id']['N']),
													)
											));
							} else {
								$res[$key] = 1;
							}
						}
					}
				}
			}
		}
	}
}
fclose($handler);
var_dump("success");