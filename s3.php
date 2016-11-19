<?php
require_once "./aws/aws-autoloader.php";
use Aws\S3\S3Client;

$client = S3Client::factory(array(
	'profile' => 'default',
	'region' => 'cn-north-1',
	'version' => 'latest',
));

$bucket = "dev-test";
/*$result = $client->createBucket(array(
		'Bucket'             => $bucket,
		'LocationConstraint' => 'cn-north-1',
));
echo $result['Location'] . "\n";
echo $result['RequestId'] . "\n";*/

/*$result = $client->getBucketAcl(array(
		'Bucket' => $bucket,
));
print_r($result);die();*/

$result = $client->putObject(array(
		'Bucket' => $bucket,
		'Key' => 'msp_icon.png',
		'SourceFile' => './msp_icon.png',
// 		'Metadata' => array(
// 				'Foo' => 'abc',
// 				'Bar' => '123'
// 		),
));
print_r($result);

$iterator = $client->getIterator('ListObjects', array(  
	'Bucket' => $bucket  
));  
  
foreach($iterator as $object) {  
	print_r($object);  
}

$plainUrl = $client->getObjectUrl($bucket, 'msp_icon.png');
echo $plainUrl;

$request = $client->get($plainUrl);
$signedUrl = $client->createPresignedUrl($request, '+10 minutes');
echo $signedUrl;

/*$result = $client->deleteObjects(array(
		'Bucket' => $bucket,
		'Delete' => array(
			'Objects' => array(
					array(
							'Key' => 'msp_icon.png',
					),
			),
		)
));
print_r($result);*/


// $res = $client->deleteBucket(array('Bucket' => $bucket));
// print_r($res);