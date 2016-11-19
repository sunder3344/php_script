<?php
/**
 * http://it.oyksoft.com/post/4831/
 * PHP可以使用mcrypt_encrypt进行DES加密与解密，但实际上操作，你会发现它与JAVA的DES加密出来的字符串，有些不同。
 * 基本上是前半段一样，后半段不一样。找到PHP官方网站上对这个函数的文档，有人回复了，并给出了代码。经测，这样加密解密就跟JAVA中的兼容了。
 * If you want to be interoperable with other PKCS  #7 padding implementations, like the Legion of the Bouncy Castle Java cryptography APIs, 
 * you should always pad, that is a 8-byte (block size)  padding should be added, even if not necessary
 * @author sunzhidong
 * CBC模式需要iv;ECB模式不需要iv
 *
 */
class DES
{
	var $key;
	var $iv;

	function DES($key) {
		$this->key = $key;
		$bytes = array(0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00);
		$this->iv =  $this->bytesToStr($bytes);
// 		$this->iv =  mcrypt_create_iv ( mcrypt_get_block_size (MCRYPT_DES, MCRYPT_MODE_CBC), MCRYPT_DEV_RANDOM );
	}

	//加密，返回大写十六进制字符串
	function encrypt($str)
	{
		$size = mcrypt_get_block_size ( MCRYPT_DES, MCRYPT_MODE_ECB );
		$str = $this->pkcs5Pad ( $str, $size );
		return strtoupper( bin2hex(mcrypt_encrypt(MCRYPT_DES, $this->key, $str, MCRYPT_MODE_ECB, $this->iv)) );
	}

	//解密
	function decrypt($str) {
		$strBin = $this->hex2bin( strtolower( $str ) );
		$str = mcrypt_decrypt(MCRYPT_DES, $this->key, $strBin, MCRYPT_MODE_ECB);
		$str = $this->pkcs5Unpad( $str );
		return $str;
	}

	function hex2bin($hexData) {
		$binData = "";
		for($i = 0; $i < strlen ( $hexData ); $i += 2) {
			$binData .= chr ( hexdec ( substr ( $hexData, $i, 2 ) ) );
		}
		return $binData;
	}

	function pkcs5Pad($text, $blocksize) {
		$pad = $blocksize - (strlen ( $text ) % $blocksize);
		return $text . str_repeat ( chr ( $pad ), $pad );
	}

	function pkcs5Unpad($text) {
		$pad = ord ( $text {strlen ( $text ) - 1} );
		if ($pad > strlen ( $text ))
			return false;
		if (strspn ( $text, chr ( $pad ), strlen ( $text ) - $pad ) != $pad)
			return false;
		return substr ( $text, 0, - 1 * $pad );
	}

	function bytesToStr($bytes) {
		$str = '';
		foreach($bytes as $ch) {
			$str .= chr($ch);
		}
		return $str;
	}
}


$crypt = new DES('24365869');
$data = $crypt->encrypt("prod.10086000004534");
echo $data . PHP_EOL;
echo '--------'. PHP_EOL;
$res = $crypt->decrypt('745874223B952AB9A16E8EA70B022AF6');
echo $res . PHP_EOL;



// 773171DE77E1B0B46FE7D72626DBE219F434394DD8B872B8				current
// 773171de77e1b0b46fe7d72626dbe219f434394dd8b872b8
// 773171DE77E1B0B46FE7D72626DBE2194BD59D68F664CBF0			