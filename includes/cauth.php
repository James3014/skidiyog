<?php
// ***********************************
// Login with cookie

if(!isset($_SESSION['SKIDIY']['login'])){

    $ACCOUNT = new MEMBER();  // use mj's class for cookie access
    if(isset($_COOKIE['inst_reme']) && $_COOKIE['inst_reme']=="y"){
        //$ACCOUNT->show_user_cookie();             
        if(isset($_COOKIE['instname']) && isset($_COOKIE['instpwd'])){ 
            // 注意 cookie 會用 urlencode 儲存                 
            $DECRYPTO_PWD = crypto::dv(urldecode($_COOKIE['instpwd'])); 
            $login_info['instname'] = $_COOKIE['instname'];                    
            $login_info['instpwd']  = $DECRYPTO_PWD;   

            //echo 'login with cookie user/pwd ';
            $ko = new ko();
            $member = $ko->getMembers([
                'type'        => 'instructor',
                'email'         => $_COOKIE['instname'],
                'passwd'        => md5($DECRYPTO_PWD),
                'active_status' => 'PHASE1_DONE',
            ]);//_v($member);exit();   
            if(isset($member[0]['email'])){
                $_SESSION['SKIDIY']['login'] = true;
                $_SESSION['SKIDIY']['instructor'] = strtolower($member[0]['name']);                
            }else{
                // relogin with cookie fail                
                //_v($login_info); exit();
                $remove_cookie=array("instname","instpwd");
                $ACCOUNT->clear_user_cookie_v2($remove_cookie);
                //_v($_COOKIE); exit();
                Header('Location: index.php?msg=clogin_error');
            }                        
        }else{
            // no cookie auth data for relogin
            if(!strstr($_SERVER['PHP_SELF'],'index.php')){
                Header('Location: index.php');
            } 
        }        
    }else{
        // 沒 login 也沒 記住我 
        if(!strstr($_SERVER['PHP_SELF'],'index.php')){
            Header('Location: index.php');
        }        
    }
}else{
    // auto login if session exist on index page
    if(strstr($_SERVER['PHP_SELF'],'index.php')){
        Header('Location: schedule.php');
    }
}