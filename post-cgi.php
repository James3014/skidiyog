<?php
require('includes/sdk.php'); 
$ACCOUNT 	= new MEMBER();
$INSURANCE 	= new INSURANCE();
$ORDER_FUNC = new ORDER();
$COMON_FUNC	= new UTILITY();
//$AWSSNS		= new awsSES();
$KO = new ko();

switch ($_REQUEST['cmd']) {
	case 'reg':		
		if($_POST['name']=='' || $_POST['phone']=='' || $_POST['email']==''  || $_POST['passwd']==''){
			echo NULL_INPUT;
			break;
		}else{
			$_POST['create_date'] = date('Y-m-d H:i:s');
			$_POST['passwd'] = md5($_POST['passwd']);
			$_POST['2fa_authcode'] = md5($_POST['email']);	// init for phase 1: auth check
			$Result = $ACCOUNT->apply($_POST);
			//$Link = "https://".domain_name."/2fauth.php?act=p1check&tk=".md5($_POST['email']);
			//$Link = "https://".domain_name."/account_mobile_verify.php?act=p1check&tk=".md5($_REQUEST['email']);
			if(MOBILE_VERIFY == 0){
			  	$Link = "https://".domain_name."/account_email_verify.php?act=p1check&tk=".md5($_REQUEST['email']);
			}else{
			  	$Link = "https://".domain_name."/account_mobile_verify.php?act=p1check&tk=".md5($_REQUEST['email']);
			}

			if($Result == FALSE){
			 	//_d("Registered Fail");	
			 	echo REG_DUP;
			}else{
				//_d('Registration Completed');
				$mail_info['email'] = $_POST['email'];
				$mail_info['subject'] = 'SKIDIY會員Email認證信';
				$mail_info['content'] = "SKIDIY會員您好,請您點擊以下連結以完成Email驗證流程, 謝謝您～\r\n".$Link."\r\n\r\n\r\n\r\nSKIDIY\r\n自助滑雪\r\nadmin@diy.ski\r\nhttps://diy.ski";
				$ACCOUNT->send_mail($mail_info);		
				//_d('Confirm Link had been send to your mail account!');		
				echo REG_OK;		
			}	
		}	
		break;
	case 'fgpwd':
		  if($_POST['email']==''){
			echo NULL_INPUT;
			break;
		  }
		  // search new db first
		  $account_info['email'] = $_POST['email'];
		  $account_info['tb'] = 'members_v2';   
		  $R=$ACCOUNT->get_account($account_info);
		  if($R == false){                    // member_v2 無記錄
		    // search old db
		    /*
		    $account_info['tb'] = 'members';  
		    $R=$ACCOUNT->get_account($account_info);
		    if($R == false){                  // member    無記錄
		      // acount is un-exist
		      //_d('The account can\'t be found！ You need to create a new account!!');
		      echo NULL_ACCOUNT;
		    }else{                        // member    有記錄 （舊）
		      // transfer to new db(member_v2)
		      //_d('Old account found! try to transfer to new TB');
		      //var_dump($R);
		      $ACCOUNT->apply($R);
		      //_d('........Done');
		      //_alert('done');
		      // send pwd reset mail
		      $ACCOUNT->send_mail($mail_info);
		      echo PWRESET_REQ;
		    } 
		    */
		    // 以全部同步到 member_v2
            $KO->log([
                    'severity'  =>  'Account',
                    'user'      =>  $_POST['email'],
                    'oidx'      =>  'n/a',
                    'resp'      =>  'NULL_ACCOUNT: Incorrect email account',
                    'msg'       =>  'Incorrect email account',
            ]);		    
		    echo NULL_ACCOUNT;
		  }else{                          // member_v2 有記錄
		  	$email = $R['email'];
			$reset_link = "https://".domain_name."/account_pw_reset.php?tk=".md5($email)."&key=".md5($email.DEFAULT_TOKEN_KEY);
			$mail_info['email'] = $email;
			$mail_info['subject'] = 'SKIDIY會員密碼重置確認信';
			$mail_info['content'] = "SKIDIY會員您好,請您點擊以下連結以重置您的登入密碼, 若非您本人請忽略此信件或通知我們.\r\n".$reset_link."\r\n\r\n\r\n\r\nSKIDIY\r\n自助滑雪\r\nadmin@diy.ski\r\nhttps://diy.ski"; 		  	
		    $ACCOUNT->send_mail($mail_info);

            $KO->log([
                    'severity'  =>  'Account',
                    'user'      =>  $_POST['email'],
                    'oidx'      =>  'n/a',
                    'resp'      =>  'PWRESET_REQ: Send Reset Link',
                    'msg'       =>  'Forget Password & Send reset link',
            ]);

		    //_d('The PWD reset link had been sent to your mail account!');
		    echo PWRESET_REQ;
		  }	
		break;
	case 'pwreset':
		//echo "tk=".$_REQUEST['tk'];
		if(isset($_REQUEST['tk']) && isset($_REQUEST['key']) ){ 
		  $result = $ACCOUNT->pwd_reset_link_auth($_REQUEST['tk'],$_REQUEST['key']);
		  if($result){
		    //_d('you can reset your pwd now');
		    
		    if(  isset($_REQUEST['password']) && isset($_REQUEST['repassword']) ){
		        if( $_REQUEST['password'] == $_REQUEST['repassword'] ){         
		          $update_data['passwd'] = md5($_REQUEST['password']);
		          $ACCOUNT->update($result,$update_data);         
		          //$ACCOUNT->pwdreset($_REQUEST['password']);
		          //_d('密碼更新完成');
		          //echo "<script> window.location.replace('index.php') </script>" ;
		          echo PWDUPDATE_OK;
		          exit();
		          //Header('Location: https://'.domain_name);
		        }else{
		          //_d('different passwd');
		          echo DIFF_PW_IN;
		        }
		    }
		    
		  }else{
		    //_d('error');
		    echo PWRSET_TK_ERR;
		    exit();
		  }
		}	
		break;
	case 'account_modify':
		//echo '>>'.$_POST['country'];
		if($_POST['name']=='' || $_POST['phone']=='' || $_POST['country']==0 ){
			echo NULL_INPUT;
			//break;
		}else{		
			  // update DB
			  //$update_data['passwd']  = md5($_REQUEST['passwd']);
			  //$update_data['email']   = $_REQUEST['email'];
			  $update_data['fbid']   = $_REQUEST['fbid'];
			  $update_data['wechat']   = $_REQUEST['wechat'];
			  $update_data['line']   = $_REQUEST['line'];
			  $update_data['phone']   = $_REQUEST['phone'];
			  $update_data['country'] = $_REQUEST['country'];
			  $update_data['name']    = $_REQUEST['name'];
			  if($_REQUEST['passwd']!='****'){
			  $update_data['passwd']    = md5($_REQUEST['passwd']);
			  }
			  $ACCOUNT->update($_SESSION['user_idx'],$update_data); 

			  // UPDATE Cookie ths same time
			  // FIXED ME 
			  
              $cookie_data = $ACCOUNT->get_user_cookie();
              //echo '>>>'.$cookie_data['user_rememberme'];
              if(isset($cookie_data['user_rememberme']) && $cookie_data['user_rememberme']=="y"){
                    //$ACCOUNT->show_user_cookie();             
                        $cookie_login_info['user_rememberme']  = 'y';     
                        //$cookie_login_info['user']  = $_REQUEST['email'];      
                        $CRYPTO_PWD = crypto::ev($_REQUEST['passwd']);   
                        //echo '<br>raw= '.$cookie_login_info['pwd'].'<br>cry= '.$CRYPTO_PWD.'<br>'.urlencode($CRYPTO_PWD);
                        $cookie_login_info['pwd']  = $CRYPTO_PWD;      
                        $cookie_login_info['instpwd']  = $CRYPTO_PWD;       
                                 
                        $ACCOUNT->set_user_cookie_v2($cookie_login_info,0); // 注意 cookie 會用 urlencode 儲存
              }			
              

			  echo ACCOUNT_MODIFY_OK;
		}
		break;				
	case 'account_p1_check': // email check
		//echo '>>'.$_POST['country'];
		if($_POST['name']=='' || $_POST['phone']=='' || $_POST['email']=='' || $_POST['country']==0 ){
			echo NULL_INPUT;
			//break;
		}else{
		
			  // update DB
			  //$update_data['passwd']  = md5($_REQUEST['passwd']);
			  $update_data['email']   = $_REQUEST['email'];
			  $update_data['phone']   = $_REQUEST['phone'];
			  $update_data['country'] = $_REQUEST['country'];
			  $update_data['name']    = $_REQUEST['name'];
			  $ACCOUNT->update($_SESSION['user_idx'],$update_data); 
			  // send confirm mail
			  if(MOBILE_VERIFY == 0){
			  		$Link = "https://".domain_name."/account_email_verify.php?act=p1check&tk=".md5($_REQUEST['email']);
			  }else{
			  		$Link = "https://".domain_name."/account_mobile_verify.php?act=p1check&tk=".md5($_REQUEST['email']);
			  }
			  $mail_info['email'] = $_POST['email'];
			  $mail_info['subject'] = 'SKIDIY會員Email認證信';
			  $mail_info['content'] = "SKIDIY會員您好,請您點擊以下連結以完成Email驗證流程, 謝謝您～\r\n".$Link."\r\n\r\n\r\n\r\nSKIDIY\r\n自助滑雪\r\nadmin@diy.ski\r\nhttps://diy.ski";
			  $ACCOUNT->send_mail($mail_info);    
			  //_d('Confirm Link had been send to your mail account: <a href="'.$Link.'">Link</a>');  

			  echo MAIL_CHECK_OK;
			  //exit();
		 	
		}
		break;	
	case 'account_p2_check':
		if(isset($_REQUEST['code'])){ // phase2: phone check  
		  //echo 'Token:'.$_REQUEST['tk'].'<br>';
		  $result = $ACCOUNT->phase1_check($_REQUEST['tk']);
		  if($result['active_status'] == 'PHASE1_DONE' ){
		    //echo 'User input:'.$_REQUEST['code'].' : AUTH_CODE(DB)'.$result['2fa_authcode'].'<br>';
		    if($_REQUEST['code'] == $result['2fa_authcode']){   
		      $update_data['active_status'] = 'PHASE2_DONE';
		      $ACCOUNT->update($result['idx'],$update_data);      
		      //echo '手機驗證完成';
		      //_alert('您已完成帳號驗證,立即登入');
		      //_go('index.php');
		      echo MOBILE_CHECK_OK;
		    }else{
		      $update_data['active_status'] = 'PHASE2_FAIL';
		      $ACCOUNT->update($result['idx'],$update_data);      
		      //echo '手機驗證失敗';
		      echo MOBILE_CHECK_FAIL;
		    }
		  }else if($result['active_status'] == 'PHASE2_DONE' ){
		    //_d('信箱與手機皆已驗證完成！ ');  
		    //_go('index.php');
		    echo TWO_FA_AUTH_DONE;
		    exit(); 
		  }else{
		    //_d('您尚未通過信箱驗證，或是您使用了錯誤來源鏈結！');
		    echo SECURITY_FAIL;
		    exit();
		  }
		  
		}	
		break;	
	case 'insurance_apply':// 保險填寫
		if($_POST['oidx']=='' || !is_numeric(crypto::dv($_REQUEST['oidx'])) ){
			echo ERR_NULL_OID;
		}elseif($_POST['p_cn']=='' || $_POST['tid']=='' || $_POST['p_no']==''  ){
			echo NULL_INPUT;
		//}elseif(!preg_match("/^([0-9A-Za-z]+)$/",$_POST['p_no']) || strlen($_POST['p_no']) <= 8 ){	// 護照
			//echo ERR_PASSPORT;			
		//}elseif(!preg_match("/^([0-9A-Za-z\s\.]+)$/",$_POST['p_en'])  ){
		}elseif(strlen($_POST['p_en']) <= 4 ){
			echo ERR_PASSPORT_N;
			//echo INSURANCE_OK;	
		}elseif(!$COMON_FUNC->chinese_check($_POST['p_cn'])){ // 非中文
			echo ERR_CHINESE_N;				
		}elseif(!preg_match("/^([0-9A-Za-z]+)$/",$_POST['tid']) || strlen($_POST['tid']) != 10 ){	// 台灣身分證
			echo ERR_TWID;
		}elseif(!preg_match("/^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)+$/",$_POST['mail'])  ){
			echo ERR_EMAIL;	
		//}elseif(!preg_match("/^[\w-]+([0-9]+)$/",$_POST['phone'])  || strlen($_POST['phone']) <=5 ){
		}elseif(strlen($_POST['phone']) <=5 ){			
			echo ERR_PHONE;	
		}elseif(strlen($_POST['addr']) <=10 ){			
			echo ERR_ADDR;	
		}elseif($_POST['nationality'] == 'none' ){			
			echo ERR_NULL_COU;	
			//echo 'v:'.$_POST['nationality'];				
		}elseif(!preg_match("/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/",$_POST['birth'])  ){
			echo ERR_BIRTH;			
		//}elseif( isset($_POST['d_date']) && isset($_POST['a_date']) && (strlen($_POST['d_date']) ==0 || strlen($_POST['a_date']) ==0 )){			
		//	echo ERR_NULL_DA_DATE;	
		}elseif( isset($_POST['d1_date'])  && strlen($_POST['d1_date']) ==0 ){			
			echo ERR_NULL_DAY1_DATE;												
		}else{		
			  // update DB
			  $update_data['twid']   			= $_REQUEST['tid'];
			  $update_data['pcname']   			= $_REQUEST['p_cn'];
			  $update_data['pename']   			= $_REQUEST['p_en'];
			  $update_data['pnumber']   		= $_REQUEST['p_no'];
			  $update_data['phone']				= $_REQUEST['phone'];
			  $update_data['email']  			= $_REQUEST['mail'];
			  $update_data['birthday']  		= $_REQUEST['birth'];
			  $update_data['sex']  				= $_REQUEST['sex'];
			  $update_data['address']   		= $_REQUEST['addr'];
			  $update_data['country']   		= $_REQUEST['nationality'];
			//   $update_data['emergencyName']   	= $_REQUEST['emergencyman'];
			  $update_data['inusrance_num']  	= $_REQUEST['p_amount'];	// 保險人數,只有主揪人才會紀錄（master=y）
			//   $update_data['c_days']  			= $_REQUEST['c_days'];		// 1.上課天數
			//   $update_data['ski_days']  		= $_REQUEST['ski_days'];	// 2.滑雪天數
			//   $update_data['cont_turn']  		= $_REQUEST['turn'];		// 3.是否可以連續轉彎？選項：a.否 b.綠線 c.紅線 d.黑線
			  if( isset($_POST['d_date']) && isset($_POST['a_date']) && (strlen($_REQUEST['d_date'])>0 && strlen($_REQUEST['a_date']) >0) ){
			  	$update_data['departure_date']  	= $_REQUEST['d_date'];
			  	$update_data['arrival_date']  		= $_REQUEST['a_date'];
			  }
			  if( isset($_POST['d1_date'])  && strlen($_REQUEST['d1_date'])>0  ){
			  	$update_data['day1_class_date']  	= $_REQUEST['d1_date'];
			  }			  



			  $update_data['master'] 	= 'Y';
			  $update_data['midx'] 		= $_SESSION['user_idx'];
			  $update_data['oidx']  	= crypto::dv($_REQUEST['oidx']);
			  $update_data['class_date']  = $ORDER_FUNC->schedule_class_date($update_data['oidx']);	
			  $insurance_idx = $INSURANCE->check($update_data['oidx'],'Y');
			  //echo $insurance_idx.'|';
			  if($insurance_idx > 0){ // update directly if exist
			  	$INSURANCE->update($insurance_idx,$update_data);
			  }else if($insurance_idx==0){ // add new
			  	$INSURANCE->apply($update_data);
			  }

			  
		
              echo ACCOUNT_MODIFY_OK;
		}

		break;	
	case 'insurance_fapply':// 保險填寫 (團員)
		$RESULT = INSURANCE_OK;
/*	
		if($_POST['p_cn']=='' || $_POST['tid']=='' || $_POST['p_no']=='' || $_POST['oidx']=='' ){
			echo NULL_INPUT;
		}elseif(!preg_match("/^([0-9A-Za-z]+)$/",$_POST['p_no']) || strlen($_POST['p_no']) <= 8 ){	// 護照
			echo NULL_INPUT;			
		}elseif(!preg_match("/^([0-9A-Za-z\s\.]+)$/",$_POST['p_en'])  ){
			echo NULL_INPUT;			
		}elseif(!preg_match("/^([0-9A-Za-z]+)$/",$_POST['tid']) || strlen($_POST['tid']) != 10 ){	// 台灣身分證
			echo NULL_INPUT;
		}elseif(!preg_match("/^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)+$/",$_POST['mail'])  ){
			echo NULL_INPUT;	
		}elseif(!preg_match("/^([0-9]+)$/",$_POST['phone'])  || strlen($_POST['phone']) <=5 ){
			echo NULL_INPUT;	
		}elseif(!preg_match("/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/",$_POST['birth'])  ){
			echo NULL_INPUT;
*/			
		if($_POST['oidx']=='' || !is_numeric(crypto::dv($_POST['oidx'])) ){
			echo ERR_NULL_OID;
		}elseif($_POST['p_cn']=='' || $_POST['tid']=='' || $_POST['p_no']==''  ){
			echo NULL_INPUT;
		//}elseif(!preg_match("/^([0-9A-Za-z]+)$/",$_POST['p_no']) || strlen($_POST['p_no']) <= 8 ){	// 護照
			//echo ERR_PASSPORT;			
		//}elseif(!preg_match("/^([0-9A-Za-z\s\-\.]+)$/",$_POST['p_en'])  ){
		}elseif(strlen($_POST['p_en']) <= 4 ){
			echo ERR_PASSPORT_N;
			//echo INSURANCE_OK;
		}elseif(!$COMON_FUNC->chinese_check($_POST['p_cn'])){ // 非中文
			echo ERR_CHINESE_N;						
		}elseif(!preg_match("/^([0-9A-Za-z]+)$/",$_POST['tid']) || strlen($_POST['tid']) != 10 ){	// 台灣身分證
			echo ERR_TWID;
		}elseif(!preg_match("/^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)+$/",$_POST['mail'])  ){
			echo ERR_EMAIL;	
		//}elseif(!preg_match("/^[\w-]+([0-9]+)$/",$_POST['phone'])  || strlen($_POST['phone']) <=5 ){
		}elseif(strlen($_POST['phone']) <=5 ){			
			echo ERR_PHONE;				
		}elseif(!preg_match("/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/",$_POST['birth'])  ){
			echo ERR_BIRTH;		
		//}elseif( isset($_POST['d_date']) && isset($_POST['a_date']) && (strlen($_POST['d_date']) ==0 || strlen($_POST['a_date']) ==0 )){			
		//	echo ERR_NULL_DA_DATE;							
		}elseif( isset($_POST['d1_date'])  && strlen($_POST['d1_date']) ==0 ){			
			echo ERR_NULL_DAY1_DATE;
		// }elseif( (strtotime($_POST['d1_date']) - strtotime($_POST['birth'])) < time() - strtotime('-18 years')  ){
		// 	echo ERR_BIRTH15;
		}elseif($_POST['nationality'] == 'none' ){			
			echo ERR_NULL_COU;					
		}else{		
			  //echo ">>".$_REQUEST['oidx'];
			  //echo ">>".crypto::dv($_REQUEST['oidx']);
			  // update DB
			  $update_data['twid']   	= $_REQUEST['tid'];
			  $update_data['pcname']   	= $_REQUEST['p_cn'];
			  $update_data['pename']   	= $_REQUEST['p_en'];
			  $update_data['pnumber']   = $_REQUEST['p_no'];
			  $update_data['phone']		= $_REQUEST['phone'];
			  $update_data['email']  	= $_REQUEST['mail'];
			  $update_data['birthday']  = $_REQUEST['birth'];
			  $update_data['sex']  				= $_REQUEST['sex'];
			  $update_data['country']   		= $_REQUEST['nationality'];
			  $update_data['address']   		= $_REQUEST['addr'];
			//   $update_data['emergencyName']   	= $_REQUEST['emergencyman'];			  
			  $update_data['master'] 	= 'N';
			  $update_data['midx'] 		= '';
			  $update_data['oidx']  	= crypto::dv($_REQUEST['oidx']);
			  $update_data['idx']		='';
			//   $update_data['c_days']  			= $_REQUEST['c_days'];		// 1.上課天數
			//   $update_data['ski_days']  		= $_REQUEST['ski_days'];	// 2.滑雪天數
			//   $update_data['cont_turn']  		= $_REQUEST['turn'];		// 3.是否可以連續轉彎？選項：a.否 b.綠線 c.紅線 d.黑線	
			  $update_data['class_date']  = $ORDER_FUNC->schedule_class_date($update_data['oidx']);		  
			  if( isset($_POST['d_date']) && isset($_POST['a_date']) && (strlen($_REQUEST['d_date'])>0 && strlen($_REQUEST['a_date']) >0) ){
			  	$update_data['departure_date']  	= $_REQUEST['d_date'];
			  	$update_data['arrival_date']  		= $_REQUEST['a_date'];
			  }			  
			  if( isset($_POST['d1_date'])  && strlen($_REQUEST['d1_date'])>0  ){
			  	$update_data['day1_class_date']  	= $_REQUEST['d1_date'];
			  }	
			  // query info
			  
		  
			  if(isset($_REQUEST['m']) && $_REQUEST['m']=='m'){ // update mode
			  	  //_d('update idx='.$_REQUEST['dbidx']);
			  	  $update_data['idx']  	= crypto::dv($_REQUEST['dbidx']); // mail link 傳過來的 dbidx
			  	 // _d('update idx='.$update_data['idx']);

			  	  //$q_oid		= crypto::dv($_REQUEST['id']);		
				  $q_twid 		=  $update_data['twid'];
				  $q_pno 		=  $update_data['pnumber'];		

				  $insurance_idx = $INSURANCE->check_others($update_data['oidx'],$q_twid,$q_pno); // 可能會查到多筆				 
				  $insurance_idx =  $update_data['idx']; // mail link 點選後,藏在 表單內按儲存送過來的 idx
				   
				  // update mode
				  //echo $insurance_idx.'|';
				  if($insurance_idx > 0){ // update directly if exist
				  	$INSURANCE->update($insurance_idx,$update_data);
				  	$q_idx 		= crypto::ev($insurance_idx);			
				  }else if($insurance_idx==0){ // add new
				  	$q_idx 		= 'ERR';
				  }		
				  $modify_link = '';
				  $q_twid 		= crypto::ev($_REQUEST['tid']);
				  $q_pno 		= crypto::ev($_REQUEST['p_no']);		
				  		  
				  $modify_link = 'https://diy.ski/insurance_fapply.php?id='.urlencode($_REQUEST['oidx']).'&mtid='.$q_twid.'&mpno='.$q_pno.'&qid='.$q_idx.'&m=m';
				  $apply_status='更新後的';
			  }else{	 // new one
			  	// 尚未有人填寫
			  	if($INSURANCE->check_others_v2($update_data['oidx'],$update_data['twid'],$update_data['pnumber']) == 0){			  		
			  		$INSURANCE->apply($update_data);
			  	
				  	// 取得剛剛insert 的 index
				  	$new_insurance_idx = $INSURANCE->check_others($update_data['oidx'],$update_data['twid'],$update_data['pnumber']);
					$q_twid 		= crypto::ev($_REQUEST['tid']);
					$q_pno 			= crypto::ev($_REQUEST['p_no']);	
					$q_idx 			= crypto::ev($new_insurance_idx);			  	
				  	$modify_link = 'https://diy.ski/insurance_fapply.php?id='.urlencode($_REQUEST['oidx']).'&mtid='.$q_twid.'&mpno='.$q_pno.'&qid='.$q_idx.'&m=m';
				  	$apply_status='新填寫的';
			  	}else{
			  		$RESULT = ERR_INFO_DUP;
			  	}
			  }


			  if($RESULT==INSURANCE_OK){
				  // SEND Mail notify			  
				  $mail_info['email'] 	=  $update_data['email'];
				  $mail_info['subject'] = 'SKIDIY 團員保單資料確認 (訂單編號：#'.$update_data['oidx'].')';
				  if(isset($_REQUEST['m']) && $_REQUEST['m']=='m') $mail_info['subject'] = 'SKIDIY 團員保單資料更新確認 (訂單編號：#'.$update_data['oidx'].')';
				  //$mail_info['content'] = $update_data['pcname']."\r\n您好,請您點擊以下連結以完成Email驗證流程, 謝謝您～\r\n".$modify_link."\r\n\r\n\r\n\r\nSKIDIY\r\n自助滑雪\r\nadmin@diy.ski\r\nhttps://diy.ski";
				  $mail_info['content']  =$update_data['pcname']." 您好, 我們已收到您".$apply_status."保險資料如下: \r\n\r\n";
				  $mail_info['content'] .="姓名: ".$update_data['pcname']." / ".$update_data['pename']." , 出生日期: ".$update_data['birthday'].", 身分證:".$update_data['twid']."\r\n";
				  $mail_info['content'] .="以上若有錯誤，請儘速點擊下列連結修改，謝謝。\r\n";
				  $mail_info['content'] .=$modify_link;
				  $ACCOUNT->send_mail($mail_info);

	              echo ACCOUNT_MODIFY_OK;
          	  }else{
          	  	echo $RESULT;
          	  }
		}

		break;			
	case 'inst_update': // 後台 教練編輯
		echo 0;
		break;
	default:
		# code...
		break;
}

?>