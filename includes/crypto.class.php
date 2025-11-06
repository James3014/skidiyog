<?php
class crypto{/* Crypto v18.07.28 By Ko */
	

	private static $SALT = '6666a666666f666abc66aaedab20180728adbdf6666f6666666d666a54336684';//32 for AES-256

	
	public static function ev($txt){
		$iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC), MCRYPT_RAND);
		$ciphertext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, pack('H*', self::$SALT), $txt, MCRYPT_MODE_CBC, $iv);
		$ciphertext = $iv . $ciphertext;
		return urlencode(base64_encode($ciphertext));
	}

	public static function dv($txt){
		$ciphertext_dec = base64_decode($txt);
		$iv = substr($ciphertext_dec, 0, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC));
		$ciphertext_dec = substr($ciphertext_dec, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC));
		return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, pack('H*', self::$SALT), $ciphertext_dec, MCRYPT_MODE_CBC, $iv));
	}
	
	public static function ea($data){
		return self::ev(json_encode($data,JSON_UNESCAPED_UNICODE));
	}

	public static function da($data){
		return json_decode(trim(self::dv($data)), true);
	}

	/*public function generate_token($deviceid){
		$d = date('YmdHis');
		$token = DOMAIN_NAME.">>{$d}-{$deviceid}@InnCom";
		return $this->ev($token);
	}

	public function verify_token($_IN){//return true;
		$cipher = $_IN['token'];
		$plain = $this->dv($cipher);
		$regexp = '/^'.DOMAIN_NAME.'>>20\d{12}-(.+)\@InnCom$/';
		if(preg_match($regexp, $plain, $arr)){
			$deviceid = $arr[1];//機碼
		}else{
			return false;
		}
		return ($deviceid==$_IN['deviceid']);
	}*/

}