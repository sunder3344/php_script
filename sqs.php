<?php
require_once "./aws/aws-autoloader.php";
use Aws\Sqs\SqsClient;

$client = SqsClient::factory(array(
	'profile' => 'default',
	'region' => 'cn-north-1',
	'version' => 'latest',
));

$sqs_url = "https://sqs.cn-north-1.amazonaws.com.cn/787311381643/iap_unnotify_check";
// $res = $client->sendMessage(array(
// 			'QueueUrl' => $sqs_url,
// 			'MessageBody' => 'bbb',
// 		));
// var_dump($res);
$result = $client->receiveMessage(array(
    'QueueUrl' => $sqs_url,
// 	'AttributeNames' => array('All'),
	'MaxNumberOfMessages' => 10,
));

if (isset($result['Messages']) && is_array($result['Messages'])) {
	foreach ($result['Messages'] as $v) {
		echo $v['Body'] . PHP_EOL;
		$handler = $v['ReceiptHandle'];
		$res = $client->deleteMessage(array(
						'QueueUrl' => $sqs_url,
						'ReceiptHandle'	=> $handler,
				));
		var_dump($res['@metadata']['statusCode']);
	}
}