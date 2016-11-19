<?php
$target = 'result';
$retry = 10;
$sleep = 0;
$method = $_REQUEST['method'];
$id = $_REQUEST['id'];
$urlhead = "http://220.196.52.146/ship_action/video.aspx";
$url = "http://220.196.52.146/ship_action/video.aspx?method=get_zero&id=KCQN1ChGhl7AXGRmBF6tlzFn1+q5NM48j91y3nifaixe1kx+O6gg4AyYYKUg5hpM4ajaQtrhRVkPgb0XissxHQ==";
// $url = $urlhead . '?method=' . $method . '&id=' . $id;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 信任任何证书
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // 检查证书中是否设置域名（为0也可以，就是连域名存在与否都不验证了）
$output = curl_exec($ch);
while((strpos($output, $target) === FALSE) && $retry--){ //检查$targe是否存在
	sleep($sleep); //阻塞1s
	$output = curl_exec($ch);
}
curl_close($ch);
echo $output;