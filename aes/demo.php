<?php 
require 'AES.class.php';
// require 'aess.class.php';     // AES PHP implementation
// require 'aesctr.class.php';  // AES Counter Mode implementation 
// require 'Xcrypt.php';

header('Content-Type:text/html;Charset=utf-8;');
/*echo 'each change<br>';

$mstr = AesCtr::encrypt('Hello World', 'key', 256);
echo "Encrypt String : $mstr<br />";

$dstr = AesCtr::decrypt($mstr, 'key', 256);
echo "Decrypt String : $dstr<br />";

echo 'each not change<br>';

$mstr = AesCtr::encrypt('Hello World', 'key', 256, 1);	// keep=1
echo "Encrypt String : $mstr<br />";*/

$str = 'D6EQND4r26pvT6NXHJfB11DT2pC6Phj8q1mAfl9nwndPfXPf66SNqEQU+wMuRpQHZEbZ0URCdsBdKnfgltWk1FvBeAVkI2d7UiSbESlVF1prsQ7j2QqRzW3LdWOqpHC1YX5anO4wM6/rXB5J8oKNJ61i5H8LuF3hiW8ZKDaT7tc=';
$mstr = base64_decode($str);

$key = 'abcdef12345600000000000000000000';
$aes = new AES($key);
var_dump($aes->decrypt($mstr));

/*$str = 'userToken$userID$10.138.8.96$mac$20160309150400$3221225526$$$3$checkwords$';
$mstr = AesCtr::encrypt($str, 'abcdef12345600000000000000000000', 128);
var_dump($mstr);
$mstr = base64_encode($mstr);
var_dump($mstr);*/