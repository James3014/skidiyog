<?php
session_start();
require('includes/sdk.php'); 


if(isset($_REQUEST['act']) && $_REQUEST['act']=='login'){
	$ACCOUNT = new MEMBER();
	if(isset($_POST['username']) && isset($_POST['password'])){
		// search new db first
		$account_info['email'] = $_POST['username'];
		$account_info['tb'] = 'members_v2';		
		$R=$ACCOUNT->get_account($account_info);
		if($R == false){										// member_v2 無記錄
			// search old db
			$account_info['tb'] = 'members';	
			$R=$ACCOUNT->get_account($account_info);
			if($R == false){									// member    無記錄
				// acount is un-exist
				_d('Login Fail！ You need to create a new account!!');
			}else{												// member    有記錄 （舊）
				// transfer to new db(member_v2)
				_d('Old account found! try to transfer to new TB');
				//var_dump($R);
				$ACCOUNT->apply($R);
				_d('........Done');
				$login_info['user'] = $_POST['username'];
				$login_info['pwd']  = $_POST['password']; 
				$login_info['tb']  = 'members_v2'; 
				$R = $ACCOUNT->login($login_info);
						
				// 進入驗證流程			
				if($R){ 					
					echo 'Login success （進入驗證流程）'.$R['active_status'];
					_go('account_verify.php');
				}else{
					_d('Login Fail');
				}				
			}	
		}else{													// member_v2 有記錄
			$login_info['user'] = $_POST['username'];
			$login_info['pwd']  = $_POST['password']; 
			$login_info['tb']  = 'members_v2'; 
			$R = $ACCOUNT->login($login_info);
			if($R){ 
				echo 'Login success （正常登入）'.$R['active_status'];
				$status = $R['active_status'];
				switch($status){
					case 'DEACTIVE':

						_go('account_verify.php?s='.$status);	
						break;
					case 'PHASE1_DONE':	// 信箱驗證完成（已點選驗證link）	
					case 'MAIL_VERIFY_DONE':	// 信箱驗證完成（已點選驗證link）
						// 直接進入手機驗證
						_go('2fauth.php');	
						break;
					case 'PHASE2_DONE':		
					case 'PHONE_VERIFY_DONE':	// 已完成驗證流程 （信箱＋手機）
					case 'ACTIVE':				// 已完成驗證流程 （信箱＋手機）, 且登入過
						break;		
					default:	
						_go('account_verify.php?s='.$status);	// 重啟驗證流程
						break;											
				}
				//_go('account_verify.php');
			}else{
				_d('Login Fail');
			}	
		}
	}	
}
?>

<div class="ui-widget" align="center">
<br /><br />
<form action="?act=login" method="post">
<table align="center" width="400px">
	<tr><td colspan="2" align="center" class="td_no_boarder"><font color="#CCFF00"></font></td></tr>
	<tr height="35">
			<td align="center" width="120px" class="td_no_boarder">Email </td>
			<td class="td_no_boarder"><input type="text" name="username" style="width:180px;" value="mauji168@gmail.com" /></td>
	</tr>
	<tr height="35">
			<td class="td_no_boarder" align="center">密碼 </td>
			<td class="td_no_boarder"><input type="password" name="password" style="width:180px;" value="1111" /></td>
	</tr>
	<tr height="35">
			<td class="td_no_boarder" colspan="2" align="center"> [<a href="reg.php">會員申請</a>] [ <a href="pwforget.php">忘記密碼</a>]</td>			
	</tr>	
	

    <tr>
    		<td width="50%" align="right" class="td_no_boarder"><button id="button">L o g i n</button></td>
    		<td width="50%" align="left">[ ] 記住我</td>			
    </tr>
</table>
</form>
</div>