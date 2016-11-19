<?php
require_once "./aws/aws-autoloader.php";

use Aws\DynamoDb\DynamoDbClient;
use Aws\S3\S3Client;

$client = DynamoDbClient::factory(array(
	'profile' => 'default',
	'region' => 'cn-north-1',
	'version' => 'latest',
));

date_default_timezone_set("UTC");
$tableName = 'test2';		//hash及范围
$tableOnly = 'test3';		//仅hash

//insert one
/*$response = $client->putItem(array(
	'TableName' => $tableName,
	'Item' => array(
		'id' => array('S' => 'bbb'),
		'score' => array('N' => '443'),
	)	
));
var_dump($response);*/

//insert multi
/*$response = $client->batchWriteItem(array(
		'RequestItems' => array(
			$tableName => array(
				array(
					'PutRequest' => array(
						'Item' => array(
							'id' => array('S' => '2'),
							'score' => array('N' => '123'),
						)
					),
				),
				array(
					'PutRequest' => array(
						'Item' => array(
							'id' => array('S' => '3'),
							'score' => array('N' => '456'),
						)
					),
				)
			)
		)
));
var_dump($response);*/


/*
//getItem
// $response = $client->getItem(array(
// 	'TableName' => $tableOnly,
// 	'Key' => array(
// 			'ID' => array('N' => '1'),
// 	),
// 	'ProjectionExpression' => 'NAME_',
// 	'ConsistentRead' => true
// ));
$response = $client->getItem(array(
		'TableName' => $tableName,
		'Key' => array(
				'id' => array('S' => 'bbb'),
				'score' => array('N' => '443'),
		),
		'ProjectionExpression' => 'id, score',
		'ConsistentRead' => true
));
print_r($response);*/


//getBatchItem
/*$response = $client->batchGetItem(array(
		'RequestItems' => array(
			$tableOnly => array(
					'Keys' => array(
							array("ID" => array('N' => '2')),
					)
			),
// 			$tableName => array(
// 					'Keys' => array(
// 							array('id' => array('S' => 'aaa')),
// 							array('score' => array('N' => '1432')),
// 					)
// 			)
		),
));
print_r($response);*/


/*
//TODO 单一hash键和hash及范围键的不同，后者的key不能被修改
//update Item
// $response = $client->updateItem(array(
// 		'TableName' => $tableOnly,
// 		'Key' => array(
// 				'ID' => array('N' => '1'),
// 		),
// 		'ExpressionAttributeValues' => array(
// 				':param1' => array('S' => '呵呵呵'),
// 		),
// 		'UpdateExpression' => 'set NAME_ = :param1',
// ));
$response = $client->updateItem(array(
		'TableName' => $tableName,
		'Key' => array(
				'id' => array('S' => 'bbb'),
				'score' => array('N' => '443')
		),
		'ExpressionAttributeValues' => array(
				':param1' => array('N' => '444'),
		),
		'UpdateExpression' => 'set content = :param1',
));
print_r($response);*/

/*
//delete Item
// $response = $client->deleteItem(array(
// 		'TableName' => $tableOnly,
// 		'Key' => array(
// 				'ID' => array('N' => '2'),
// 		)
// ));
$response = $client->deleteItem(array(
		'TableName' => $tableName,
		'Key' => array(
				'id' => array('S' => 'aaa'),
				'score' => array('N' => '1432'),
		),
		'ExpressionAttributeValues' => array(
				':param1' => array('N' => '1432'),
		),
		'ConditionExpression' => 'score = :param1',
		'ReturnValues' => 'ALL_OLD',
));
print_r($response);*/


//query
/*$response = $client->query(array(
		'TableName' => $tableName,
		'KeyConditionExpression' => 'id = :id and score >= :score',
		'ExpressionAttributeValues' => array(
			':id' => array('S' => 'bbb'),
			':score' => array('N' => '443'),
		),
		'ProjectionExpression' => 'id, score',
		'ConsistantRead' => true,
		'Limit' => 2,
));
print_r($response);
print_r($response['Items']);*/

/*
//scan
// $response = $client->scan(array(
// 		'TableName' => $tableName,
// 		'ProjectionExpression' => 'id, score',
// 		'ExpressionAttributeValues' => array(
// 				':param1' => array('S' => 'bbb'),
// 				':param2' => array('N' => '443'),
// 		),
// 		'FilterExpression' => 'id = :param1 and score >= :param2',
// ));
$response = $client->scan(array(
		'TableName' => $tableOnly,
		'ProjectionExpression' => 'ID, NAME_',
		'ExpressionAttributeValues' => array(
				':param1' => array('N' => '1'),
		),
		'FilterExpression' => 'ID = :param1',
		'Limit' => 2,
));
print_r($response);
print_r($response['Items']);*/

try {
	$res = $client->describeTable(array('TableName' => "aaa"));
	print_r($res);
} catch (Exception $e) {
	
}