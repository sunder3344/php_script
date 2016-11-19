<?php
set_time_limit(0);

$ch = curl_init();
$url = "http://117.131.17.114:18080/charge/stopAutoSubscriptionViaOrder?clientId=130ff5b6-2e25-4d91-b6b5-0c7582dd43b6&sign=UvCEkvYYP9PXKT3yrtzoucF2CJ4jlJxXLBSI8X1kn/AvX/dZmFDB3VEPDLRR+8XJ4c+nQoWuH9tBgUEnih+2I5KPeOXaIPCMOg7x0Il4mhkrXu5mvtTYn5HDzBaxXlCo//s99ZRjx++CeGirEMt1MpHoxEvncL95CIccCppN7Rg=&signType=RSA";
curl_setopt($ch,CURLOPT_URL, $url);
$data = "{\"orderId\":\"452058300_323d6e45-43b7-4ca3-b2a2-50756f2ff414_ItemPurchaseOrder\",\"phoneNum\":\"13916171237\"}";
// $header[] = "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8";
// $header[] = "Accept-Encoding: gzip, deflate";
// $header[] = "Accept-Language: zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3";
// $header[] = "Cache-Control: max-age=0";
// $header[] = "Connection: keep-alive";
// $header[] = "Host: 220.196.52.146";
// $header[] = "Upgrade-Insecure-Requests: 1";
$header[] = "Content-Type: application/json";
$header[] = "User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:49.0) Gecko/20100101 Firefox/49.0 FirePHP/0.7.4";
// $header[] = "x-insight: activate";
$header[] = "version: v1.3.*";

curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

$response = curl_exec($ch);
// var_dump(curl_error($ch));
var_dump($response);
// if (isset($response) && !empty($response)) {
// 	$str_json = json_decode($response, true);
// 	print_r($str_json);
// }


// $url = "http://220.196.52.146/ship_action/video.aspx";
// $data = "method=order_zero&id=KCQN1ChGhl7AXGRmBF6tlzFn1+q5NM48j91y3nifaixe1kx+O6gg4AyYYKUg5hpM4ajaQtrhRVkPgb0XissxHQ==";
// $header = "Content-type: application/x-www-form-urlencoded\r\n".
// 		"Content-length:".strlen($data)."\r\n";
// $opts = array(
// 		'http'=>array(
// 				'method'	=>	'post',
// 				'header'	=>	$header,
// 				'content'	=>	$data,
// 				'timeout'	=>	60,
// 		)
// );
// $cxContext = stream_context_create($opts);
// $content = file_get_contents($url, false, $cxContext);
// var_dump($content);



// $url = "http://220.196.52.146/ship_action/video.aspx?method=order_zero&id=KCQN1ChGhl7AXGRmBF6tlzFn1+q5NM48j91y3nifaixe1kx+O6gg4AyYYKUg5hpM4ajaQtrhRVkPgb0XissxHQ==";
// $fp = fopen($url, 'r');
// stream_get_meta_data($fp);
// $result = '';
// while(!feof($fp)) {
// 	$result .= fgets($fp, 1024);
// }
// echo "url body: $result";
// fclose($fp);


// function socketPost($url, $data, &$ret)
// {
//  $urlArr = parse_url($url);
//  $host = $urlArr['host'];
//  $port = isset($urlArr['port'])?$urlArr['port']:80;
//  $path = isset($urlArr['path'])?$urlArr['path']:"/";
//  $fp = fsockopen($host, $port, $errno, $errstr, 30);
//  if (!$fp)
//  {
//      echo "$errstr ($errno)<br />\n";
//   return false;
//  }
//  else
//  {
//      $out = "POST $path HTTP/1.1\r\n";
//      $out .= "Host: $host\r\n";
//   $out .= "Content-Type: application/x-www-form-urlencoded\r\n";
//   $out .= "Content-Length: ".strlen($data)."\r\n";
//      $out .= "Connection: Keep-Alive\r\n\r\n";
//   $out .= $data;
//   $ret = "";
//      fwrite($fp, $out);
//      while (!feof($fp))
//   {
//          $ret .= fgets($fp, 128);
//          var_dump($ret);
//      }
//      fclose($fp);
//  }
//  return true;
// }

// socketPost('http://220.196.52.146/ship_action/video.aspx', "method=order_zero&id=KCQN1ChGhl7AXGRmBF6tlzFn1+q5NM48j91y3nifaixe1kx+O6gg4AyYYKUg5hpM4ajaQtrhRVkPgb0XissxHQ==", $res);
// var_dump($res);