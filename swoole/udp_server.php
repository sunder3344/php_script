<?php
$serv = new swoole_server("127.0.0.1", 9503, SWOOLE_PROCESS, SWOOLE_SOCK_UDP);
$serv->on("Packet", function($serv, $data, $clientInfo) {
	var_dump($data);
	$serv->sendto($clientInfo['address'], $clientInfo['port'], "Server " . $data);
	var_dump($clientInfo);
});

$serv->start();