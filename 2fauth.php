<?php
// 1. check the mail link & send the confrim code by sms
// 2. input the confirm code for phone number verify
require('includes/sdk.php'); 
if(!isset($_REQUEST['act']) || !isset($_REQUEST['tk']) ){ _d('Error request !!'); exit();}
$ACCOUNT = new MEMBER();
$COMMON_F = new COMMON_FUNC();

$p1_check_result ='STEP 1. 已完成 Email 驗證';
if($_REQUEST['act']=='p1check' && isset($_REQUEST['tk'])){ // phase1: token & mail check
	$result = $ACCOUNT->phase1_check($_REQUEST['tk']);	
	if($result != false && $result['active_status'] == 'DEACTIVE' ){ // 避免重複驗證（sms 一直發）
		$p1_check_result ='STEP 1. 已完成 Email 驗證 ('.$result['email'].' )';
		//update to DB & send auth code with sms
		$AUTH_CODE = $COMMON_F->randomStr(2,4);
		echo 'AUTH CODE: '.$AUTH_CODE .'<br>';
		// UPDATE to DB
		echo 'UPDATE TO DB: '.$result['email'].'<br>';
		$update_data['2fa_authcode'] = $AUTH_CODE;
		$update_data['active_status'] = 'PHASE1_DONE';
		$ACCOUNT->update($result['idx'],$update_data);
		// SEND AUTH_CODE to USER 
		$SMS_PhoneNumber = '+'.$result['country'].$result['phone'];
		echo 'SEND TO USER: '.$SMS_PhoneNumber.'<BR>';		

		$SNS_OBJ = new AWS_SNS();
		$SNS_OBJ->SEND_SMS($SMS_PhoneNumber,'<DIY.SKI> Your CODE:'.$AUTH_CODE,'PHPONE_DIRECTLY');		
	}else if($result['active_status'] == 'PHASE1_DONE' ){
		_d('STEP 1. Email 已驗證完成！ ');		
	}else{
		_d('STEP 1. Email 驗證失敗！ (無效的確認鏈結)');
		exit();
	}	
}

if(isset($_REQUEST['act']) && $_REQUEST['act'] == 'p2check' && isset($_REQUEST['code'])){ // phase2: phone check	
	echo 'Token:'.$_REQUEST['tk'].'<br>';
	$result = $ACCOUNT->phase1_check($_REQUEST['tk']);
	if($result['active_status'] == 'PHASE1_DONE' ){
		echo 'User input:'.$_REQUEST['code'].' : AUTH_CODE(DB)'.$result['2fa_authcode'].'<br>';
		if($_REQUEST['code'] == $result['2fa_authcode']){		
			$update_data['active_status'] = 'PHASE2_DONE';
			$ACCOUNT->update($result['idx'],$update_data);			
			//echo '手機驗證完成';
			_alert('您已完成帳號驗證,立即登入');
			_go('index.php');
			//exit();
		}else{
			$update_data['active_status'] = 'PHASE2_FAIL';
			$ACCOUNT->update($result['idx'],$update_data);			
			echo '手機驗證失敗';
		}
	}else if($result['active_status'] == 'PHASE2_DONE' ){
		_d('信箱與手機皆已驗證完成！ ');	
		_go('index.php');
		exit();	
	}else{
		_d('您尚未通過信箱驗證，或是您使用了錯誤來源鏈結！');
		exit();
	}
	
}


?>

<p align="center"><?php echo $p1_check_result; ?></p><hr />

<p align="center">STEP 2. 請輸入手機簡訊</p>
<form action="?act=p2check" method="post">
<input type="hidden" name="tk" style="width:180px;" value="<?php echo $_REQUEST['tk']; ?>" />
<input type="hidden" name="email" style="width:180px;" value="<?php echo $result['email']; ?>" />
<table align="center" width="400px">
	<tr><td colspan="2" align="center" class="td_no_boarder"><font color="#CCFF00"></font></td></tr>
	<tr height="35">
			<td align="right" width="120px" class="td_no_boarder">驗證碼</td>
			<td class="td_no_boarder"><input type="text" name="code" style="width:180px;" value="" /></td>
	</tr>

    <tr>
    		<td width="100%" align="center" colspan="2" class="td_no_boarder"><button id="button">驗證</button></td>    		
    </tr>
</table>
</form>
</div>