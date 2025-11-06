<?php
session_start();
require('includes/sdk.php'); 

if(isset($_REQUEST['act']) && $_REQUEST['act']=='reg'){
	$ACCOUNT = new MEMBER();
	$_POST['create_date'] = date('Y-m-d H:i:s');
	$_POST['passwd'] = md5($_POST['passwd']);
	$_POST['2fa_authcode'] = md5($_POST['email']);	// init for phase 1: auth check
	$Result = $ACCOUNT->apply($_POST);
	$Link = "https://mj.diy.ski/2fauth.php?act=p1check&tk=".md5($_POST['email']);
	if($Result == FALSE){
	 	_d("Registered Fail");	
	 	exit();
	}else{
		_d('Registration Completed');
		$mail_info['email'] = $_POST['email'];
		$mail_info['subject'] = '信箱驗證';
		$mail_info['content'] = "Please click the follow link to verify your phone number !!\n\n Confirm Link: ".$Link;
		$ACCOUNT->send_mail($mail_info);		
		_d('Confirm Link had been send to your mail account!');		
		exit();
	}
	
}
?>

<div class="ui-widget" align="center">
<br /><br />
<form action="reg.php?act=reg" method="post">
<table align="center" width="400px">
	<tr><td colspan="2" align="center" class="td_no_boarder"><font color="#CCFF00"></font></td></tr>
	<tr height="35">
			<td align="center" width="120px" class="td_no_boarder">Email </td>
			<td class="td_no_boarder"><input type="text" name="email" style="width:180px;" value="mauji168@gmail.com" /></td>
	</tr>
	<tr height="35">
			<td class="td_no_boarder" align="center">密碼 </td>
			<td class="td_no_boarder"><input type="password" name="passwd" style="width:180px;" value="1111" /></td>
	</tr>
	<tr height="35">
			<td class="td_no_boarder" align="center">國家 </td>
			<td class="td_no_boarder"><select name="country"><option value="886">TW</option><option value="86">CN</option></select></td>
	</tr>
	<tr height="35">
			<td class="td_no_boarder" align="center">手機 </td>
			<td class="td_no_boarder"><input type="text" name="phone" style="width:180px;" value="0952527577" /></td>
	</tr>
	<tr height="35">
			<td class="td_no_boarder" align="center">匿稱 </td>
			<td class="td_no_boarder"><input type="text" name="name" style="width:180px;" value="Mauji" /></td>
	</tr>
	

    <tr>
    		<td width="100%" align="center" colspan="2" class="td_no_boarder"><button id="button">確認申請</button></td>    		
    </tr>
</table>
</form>
</div>