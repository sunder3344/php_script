<?php
$file = './shunicom.csv'; //要上传的文件
$file = realpath(mb_convert_encoding($file,'GBK','utf8'));
// $url  = 'http://localhost/activity/avatar_upload?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyX3V1aWQiOiI0NjRhZmM3My1mNzM4LTQ5YzItYjAyNy01MzZiYTQ4MWVhYTEifQ.DqErvhhBp3RAj2_JHenysUFBuil_HjzAP2X8Trjz4Pw';
// $url  = 'http://ec2-54-223-153-135.cn-north-1.compute.amazonaws.com.cn/mobile/gd_campus_import';
$url  = 'https://api.b.cn/mobile/sh_unicom_import';

$fields['csv'] = '@'.$file;
$fields['token'] = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJkZXZpY2VJZCI6Ijg2NjkyOTAyMDg4OTcwOCIsInRpbWVzdGFtcCI6MTQ2MzkyNDg5MiwiY2hhbm5lbElkIjoiYTZkNTQ3ZDItNWFjOC00MGNjLWE3MGMtNzFkZDBhOWU4NGMzIn0.FXj5YqkT_EWM09bW8LSbHJanOLlQIbOmfMOQSjTzgK4';

$ch = curl_init();

// curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, false);
curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
curl_setopt($ch, CURLOPT_HEADER, false);
$header[] = "release: 0";
curl_setopt ( $ch, CURLOPT_HTTPHEADER, $header);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_VERBOSE, 0);
curl_setopt($ch, CURLOPT_USERAGENT,"Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322; .NET CLR 2.0.50727)");

$res = curl_exec($ch);
var_dump($res);

if ($error = curl_error($ch) ) {
// 	file_put_contents('./err.txt', $error."\n", FILE_APPEND);
	die($error);
}
curl_close($ch);