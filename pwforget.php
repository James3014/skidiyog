<?php
require('includes/sdk.php'); 

if(isset($_REQUEST['act']) && $_REQUEST['act']=='pwforget'){
	$ACCOUNT = new MEMBER();
	$reset_link = "https://mj.diy.ski/pwdreset.php?tk=".md5($_POST['email'])."&key=".md5($_POST['email'].DEFAULT_TOKEN_KEY);
	$mail_info['email'] = $_POST['email'];
	$mail_info['subject'] = '密碼重置（Password Reset）';
	$mail_info['content'] = "You trigger an password reset request at ".NOW."\n\nPlease click the follow link to reset your password !!\n\n".$reset_link;	
	// search new db first
	$account_info['email'] = $_POST['email'];
		$account_info['tb'] = 'members_v2';		
	$R=$ACCOUNT->get_account($account_info);
	if($R == false){										// member_v2 無記錄
		// search old db
		$account_info['tb'] = 'members';	
		$R=$ACCOUNT->get_account($account_info);
		if($R == false){									// member    無記錄
			// acount is un-exist
			_d('The account can\'t be found！ You need to create a new account!!');
		}else{												// member    有記錄 （舊）
			// transfer to new db(member_v2)
			_d('Old account found! try to transfer to new TB');
			//var_dump($R);
			$ACCOUNT->apply($R);
			_d('........Done');
			// send pwd reset mail
			$ACCOUNT->send_mail($mail_info);
		}	
	}else{													// member_v2 有記錄
		$ACCOUNT->send_mail($mail_info);
		_d('The PWD reset link had been sent to your mail account!');
	}
}

?>



<form action="?act=pwforget" method="post">
<table align="center" width="400px">
	<tr><td colspan="2" align="center" class="td_no_boarder"><font color="#F00F00">密碼忘記</font><hr /></td></tr>
	<tr height="35">
			<td align="center" width="120px" class="td_no_boarder">Email </td>
			<td class="td_no_boarder"><input type="text" name="email" style="width:180px;" value="mauji168@gmail.com" /></td>
	</tr>

    <tr>
    		<td width="100%" align="center" colspan="2" class="td_no_boarder"><button id="button">確認申請</button></td>    		
    </tr>
</table>
</form>
</div>