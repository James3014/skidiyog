<?php

class mpg
{
    private $merchantID     = "MS3490533126";                               //商店代號
    private $hashKey        = "Xk7YZxAmisA6L6nQ2a5Z62Pk7NWytuf2";           //HashKey
    private $hashIV         = "PE4N4LPa6m7GprOC";                           //HashIV
    private $endpoint       = "https://core.newebpay.com/MPG/mpg_gateway";  //正式環境
    private $VER            = "1.5";                                        //API版本

    private $ReturnURL      = "https://diy.ski/newebpay/ReturnURL.php"; 	//支付完成 返回商店網址
    private $NotifyURL_atm  = "https://diy.ski/newebpay/NotifyURL.php"; 	//支付通知網址
    private $ClientBackURL  = "https://diy.ski"; 					        //支付取消 返回商店網址

    public function __construct()
    {
    }

    /*HashKey AES 加密 */
    private function create_mpg_aes_encrypt ($parameter = "" , $key = "", $iv = "") {
        $plaintext = http_build_query($parameter);
        return trim(bin2hex(openssl_encrypt($this->addpadding($plaintext), 'AES-256-CBC', $key, OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING, $iv)));
    }

    /*HashKey AES 解密 */
    private function create_aes_decrypt($parameter = "", $key = "", $iv = "") {
        return $this->strippadding(openssl_decrypt(hex2bin($parameter), 'AES-256-CBC', $key, OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING, $iv));
    }

    /*HashIV SHA256 加密*/
    private function SHA256($key="", $tradeinfo="", $iv=""){
        $HashIV_Key = "HashKey=".$key."&".$tradeinfo."&HashIV=".$iv;

        return $HashIV_Key;
    }

    private function addpadding ($string, $blocksize = 32) {
        $len = strlen($string);
        $pad = $blocksize - ($len % $blocksize);
        $string .= str_repeat(chr($pad), $pad);
        return $string;
    }

    private function strippadding ($string) {
        $slast = ord(substr($string, -1));
        $slastc = chr($slast);
        $pcheck = substr($string, -$slast);
        if (preg_match("/$slastc{" . $slast . "}/", $string)) {
            $string = substr($string, 0, strlen($string) - $slast);
            return $string;
        } else {
            return false;
        }
    }

    private function CheckOut($URL="", $MerchantID="", $TradeInfo="", $SHA256="", $VER="") {
        $szHtml = '<!doctype html>';
        $szHtml .='<html>';
        $szHtml .='<head>';
        $szHtml .='<meta charset="utf-8">';
        $szHtml .='</head>';
        $szHtml .='<body>';
        $szHtml .='<form name="Newebpay" id="Newebpay" method="post" action="'.$URL.'" style="display:none;">';
        $szHtml .='<input type="text" name="MerchantID" value="'.$MerchantID.'" type="hidden">';
        $szHtml .='<input type="text" name="TradeInfo" value="'.$TradeInfo.'" type="hidden">';
        $szHtml .='<input type="text" name="TradeSha" value="'.$SHA256.'" type="hidden">';
        $szHtml .='<input type="text" name="Version"  value="'.$VER.'" type="hidden">';
        $szHtml .='</form>';
        $szHtml .='<script type="text/javascript">';
        $szHtml .='document.getElementById("Newebpay").submit();';
        $szHtml .='</script>';
        $szHtml .='</body>';
        $szHtml .='</html>';
        return $szHtml;
    }

    public function decrypt($cipher){
        return  $this->create_aes_decrypt($cipher, $this->hashKey, $this->hashIV);
    }

    public function pay($info){
        //付款資料
        $trade = [
            'MerchantID' => $this->merchantID,
            'RespondType' => 'JSON',
            'TimeStamp' => time(),
            'Version' => $this->VER,
            'MerchantOrderNo' => $info['orderNo'],
            'Amt' => $info['price'],
            'ItemDesc' => $info['description'],
            'ReturnURL' => $this->ReturnURL,            //支付完成 返回商店網址
            'NotifyURL' => $this->NotifyURL_atm,        //支付通知網址
            'ClientBackURL' => $this->ClientBackURL ,   //支付取消 返回商店網址
            'Email' => $info['email'],
            'LoginType' => 0
        ];

        $TradeInfo = $this->create_mpg_aes_encrypt($trade, $this->hashKey, $this->hashIV);
        $SHA256 = strtoupper(hash("sha256", $this->SHA256($this->hashKey, $TradeInfo, $this->hashIV)));

        return $this->CheckOut($this->endpoint, $this->merchantID, $TradeInfo, $SHA256, $this->VER);
    }
}
