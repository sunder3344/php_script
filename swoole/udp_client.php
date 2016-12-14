<?php
$sendMsg = "testtest";
$handle = stream_socket_client("udp://127.0.0.1:9503", $errno, $errstr);
if (!$handle) {
	die("Error: {$errno} - {$errstr}\n");
}
fwrite($handle, $sendMsg . "\n");
$result = fread($handle, 1024);
fclose($handle);
return $result;
var_dump($result);