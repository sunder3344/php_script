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
$tableName = 't_card';		//hash及范围
set_time_limit(0);
//先生成50个随机号码
$ct = 50;
$prefix = 'VP';
$data = array();
$fp = fopen('generate.csv', 'w');
for ($i = 0; $i < $ct; $i++) {
	$key = $prefix . randpw(9, 'NUMBER');
	$password = randpw(9, 'NUMBER');
	echo $key . '    ' . $password . PHP_EOL;	
	$data[$i]['key'] = $key;
	$data[$i]['pwd'] = $password;
	fputcsv($fp, array($key, $password));
	unset($key);
	unset($password);
}
// print_r($data);
// die();
$end_time = strtotime('2018-12-30 12:00:00');
$count = 1;
foreach ($data as $v) {
	$card_no = $v['key'];
	$card_pwd = $v['pwd'];
	echo $count . ': ' . $card_no . '-' . $card_pwd . PHP_EOL;
	$response = $client->putItem(array(
			'TableName' => $tableName,
			'Item' => array(
					'card_no' => array('S' => (string)$card_no),
					'card_pwd' => array('S' => (string)$card_pwd),
					'is_used' => array('N' => '0'),
					'product_id' => array('N' => '3'),
					'start_time' => array('N' => (string)time()),
					'end_time' => array('N' => (string)$end_time),
					'create_time' => array('N' => (string)time()),
			),
			'ReturnConsumedCapacity' => 'TOTAL'
	));
	++$count;
}
echo 'success';