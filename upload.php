<?php
$file = './111.png'; //要上传的文件
$file = realpath(mb_convert_encoding($file,'GBK','utf8'));
// $url  = 'http://localhost/activity/avatar_upload?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyX3V1aWQiOiI0NjRhZmM3My1mNzM4LTQ5YzItYjAyNy01MzZiYTQ4MWVhYTEifQ.DqErvhhBp3RAj2_JHenysUFBuil_HjzAP2X8Trjz4Pw';
$url  = 'http://ec2-54-223-153-135.cn-north-1.compute.amazonaws.com.cn/activity/avatar_upload';

$fields['avatar'] = '@'.$file;
$fields['token'] = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJkZXZpY2VJZCI6IlpURStBMjAxNV9lYy0xZC03Zi1jMy03NS0yNl84NjcyNDEwMjA2ODMyNjYiLCJ0aW1lc3RhbXAiOjE0NjEzMTQxOTUsImNoYW5uZWxJZCI6ImFhMmRkZmRiLTQzODctNDljMC05NjUxLTkyY2M4M2I4ZTkwNSJ9.oJdLbpnf8Ck1b7e1kWnoMx2ki92YFfzvcfj0INDPsJc';

$ch = curl_init();

// curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, false);
curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_VERBOSE, 0);
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Expect:"));
curl_setopt($ch, CURLOPT_USERAGENT,"Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322; .NET CLR 2.0.50727)");

$res = curl_exec($ch);
var_dump($res);

if ($error = curl_error($ch) ) {
// 	file_put_contents('./err.txt', $error."\n", FILE_APPEND);
	die($error);
}
curl_close($ch);