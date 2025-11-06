<?php
require('includes/sdk.php'); 

//pwd_reset_link_auth($token,$key)
$ACCOUNT = new MEMBER();
if(isset($_REQUEST['tk']) && isset($_REQUEST['key']) ){	
	$result = $ACCOUNT->pwd_reset_link_auth($_REQUEST['tk'],$_REQUEST['key']);
	if($result){
		_d('you can reset your pwd now');
		if(isset($_REQUEST['act']) && $_REQUEST['act'] == 'pwdrest' ){
			if(  isset($_REQUEST['password']) && isset($_REQUEST['repassword']) ){
				if( $_REQUEST['password'] == $_REQUEST['repassword'] ){					
					$update_data['passwd'] = md5($_REQUEST['password']);
					$ACCOUNT->update($result,$update_data);					
					//$ACCOUNT->pwdreset($_REQUEST['password']);
					_d('密碼更新完成');
					echo "<script> window.location.replace('index.php') </script>" ;
				}else{
					_d('different passwd');
				}
			}
		}		
	}else{
		_d('error');
		exit();
	}
}	

	

?>

<hr>
<div class="ui-widget" align="center">
<br /><br />
<form action="?act=pwdrest" method="post">
<input type="hidden" name="tk" style="width:180px;" value="<?php echo $_REQUEST['tk']; ?>" />
<input type="hidden" name="key" style="width:180px;" value="<?php echo $_REQUEST['key']; ?>" />
<table align="center" width="400px">
	<tr><td colspan="2" align="center" class="td_no_boarder"><font color="#CCFF00"></font></td></tr>

	<tr height="35">
			<td class="td_no_boarder" align="center">新密碼 </td>
			<td class="td_no_boarder"><input type="password" name="password" style="width:180px;" value="" /></td>
	</tr>

	<tr height="35">
			<td class="td_no_boarder" align="center">密碼確認 </td>
			<td class="td_no_boarder"><input type="password" name="repassword" style="width:180px;" value="" /></td>
	</tr>
	
	

    <tr>
    		<td width="50%" align="right" class="td_no_boarder"><button id="button">確認重置</button></td>
    		<td width="50%" align="left"></td>			
    </tr>
</table>
</form>
</div>