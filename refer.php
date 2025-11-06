<?php
session_start();
require('includes/sdk.php');

$ACCOUNTFUNC = new MEMBER();
$DB = new db();

if(isset($_REQUEST['target'])){	 // 寫入 cookie
	$refer_info['refer']=$_REQUEST['target'];
	//_v($refer_info);
	$ACCOUNTFUNC->set_user_cookie_v2($refer_info,0);
	$_SESSION['refer_hash'] = $refer_info['refer'];
	//echo 'write to cookie successful';

	$sql = "SELECT * FROM `refer` WHERE `refer`='{$_REQUEST['target']}'"; //_d($sql);
	$r2 = $DB->QUERY('SELECT',$sql);//_v($r2);
	if(isset($r2[0]['idx'])){
		$redirect_to ='https://'.$_SERVER['HTTP_HOST'].'/'.$r2[0]['landingpage'];
		//echo $redirect_to;
		_go($redirect_to);
	}

}else{// 從 cookie 讀取
	$ref_hash = $ACCOUNTFUNC->get_user_cookie_v2('refer');
	$_SESSION['refer_hash'] = $ref_hash;
}





?>