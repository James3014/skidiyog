<?php
require 'includes/sdk.php';

$SNS_OBJ = new AWS_SNS();


if(isset($_REQUEST['phone']) && strlen($_REQUEST['phone']) >=9){	
	echo 'Send msg to '.$_REQUEST['phone'];
	$SNS_OBJ->SEND_SMS('+886'.$_REQUEST['phone'],'SMS MSG from AWS Testing....^__^','PHPONE_DIRECTLY');

}else{
	echo 'Send msg to 0952527577';
	//$SNS_OBJ->SEND_SMS('+8860952527577','SNS MSG by AWS Testing....^__^','PHPONE_DIRECTLY');
	$SNS_OBJ->SEND_SMS('+886952527577','SNS MSG by AWS Testing....中文測試'.date('Y-m-d H:i'),'PHPONE_DIRECTLY');
	//$SNS_OBJ->SEND_SMS('+886910930472','SNS MSG by AWS Testing....MJ','PHPONE_DIRECTLY');
}

?>