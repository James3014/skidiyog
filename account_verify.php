<?php
session_start();
require('includes/sdk.php'); 
echo 'Login: '.$_SESSION['account']."<br>";
echo 'Status: '.$_SESSION['status']."<br>";
$ACCOUNT = new MEMBER();
$account_info['email'] = $_SESSION['account'];	
$R=$ACCOUNT->get_account($account_info);

if(isset($_REQUEST['act']) && $_REQUEST['act']=='up_2fcheck'){
	// update DB
	$update_data['passwd']	= md5($_REQUEST['passwd']);
	$update_data['email']  	= $_REQUEST['email'];
	$update_data['phone'] 	= $_REQUEST['phone'];
	$update_data['country'] = $_REQUEST['country'];
	$update_data['name']  	= $_REQUEST['name'];
	$ACCOUNT->update($_SESSION['user_idx'],$update_data);	
	// send confirm mail
	$Link = "https://".domain_name."/2fauth.php?act=p1check&tk=".md5($_REQUEST['email']);
	$mail_info['email'] = $_POST['email'];
	$mail_info['subject'] = '信箱驗證';
	$mail_info['content'] = "Please click the follow link to verify your phone number !!\n\n Confirm Link: ".$Link;
	$ACCOUNT->send_mail($mail_info);		
	_d('Confirm Link had been send to your mail account: <a href="'.$Link.'">Link</a>');	
	exit();
}	

?>

<div class="ui-widget" align="center">
<br /><br />
<form action="?act=up_2fcheck" method="post">
<table align="center" width="400px">
	<tr><td colspan="2" align="center" class="td_no_boarder"><font color="#FF0000">帳 號 驗 證</font><hr></td></tr>
	<tr height="35">
			<td align="center" width="120px" class="td_no_boarder">Email </td>
			<td class="td_no_boarder"><input type="text" name="email" style="width:180px;" value="<?php echo $R['email']; ?>" /></td>
	</tr>
	<tr height="35">
			<td class="td_no_boarder" align="center">手機 </td>
			<td class="td_no_boarder"><input type="text" name="phone" style="width:180px;" value="<?php echo $R['phone']; ?>" /></td>
	</tr>	
	<tr height="35">
			<td class="td_no_boarder" align="center">密碼 </td>
			<td class="td_no_boarder"><input type="password" name="passwd" style="width:180px;" value="" /></td>
	</tr>
	<tr height="35">
			<td class="td_no_boarder" align="center">國家 </td>
			<td class="td_no_boarder"><select name="country"><option value="886">TW</option><option value="86">CN</option></select></td>
	</tr>

	<tr height="35">
			<td class="td_no_boarder" align="center">匿稱 </td>
			<td class="td_no_boarder"><input type="text" name="name" style="width:180px;" value="<?php echo $R['name']; ?>" /></td>
	</tr>
	

    <tr>
    		<td width="100%" align="center" colspan="2" class="td_no_boarder"><button id="button">驗 證</button></td>    		
    </tr>
</table>
</form>
</div>