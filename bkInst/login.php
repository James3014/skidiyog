<?php
session_start();
require('../includes/sdk.php');
$filters = array(
    'email'         =>  FILTER_SANITIZE_STRING,
    'password'      =>  FILTER_SANITIZE_STRING,
);

//_v($_POST);exit();

$in = filter_var_array(array_merge($_REQUEST,$_POST), $filters);//_v($in);//exit();
$ko = new ko();

$member = $ko->getMembers([
    'type'        => 'instructor',
    'email'         => $in['email'],
    'passwd'        => md5($in['password']),
    'active_status' => 'PHASE1_DONE',
    'deactive' => 0,
]);//_v($member);exit();

if(isset($member[0]['email'])){
    $_SESSION['SKIDIY']['login'] = true;
    $_SESSION['SKIDIY']['instructor'] = strtolower($member[0]['name']);

    // save cookie ----------------------------------------------------------------------- START
                $COOKIE_TIMOUT = 60*60*24*30;    // 30 Days
                //$COOKIE_TIMOUT = 3600;
                $ACCOUNT = new MEMBER();  // use mj's class for cookie access
                  if(isset($_POST['rememberme']) && $_POST['rememberme']=="on"){
                    //echo ">>".$_POST['rememberme'];  
                    $cookie_info['inst_reme']  = 'y';     
                    $cookie_info['instname'] = $in['email']; 
                    $CRYPTO_PWD = crypto::ev($in['password']);   
                    //echo '<br>raw= '.$cookie_info['pwd'].'<br>cry= '.$CRYPTO_PWD.'<br>'.urlencode($CRYPTO_PWD);
                    $cookie_info['instpwd']  = $CRYPTO_PWD;                
                    $ACCOUNT->set_user_cookie_v2($cookie_info,$COOKIE_TIMOUT); // 注意 cookie 會用 urlencode 儲存
                  }else{ // 不記 cookie
                    //$tmp_cookie_info['instname'] = '';
                    //$tmp_cookie_info['instpwd'] = '';
                    $remove_cookie=array("instname","instpwd");
                    $ACCOUNT->clear_user_cookie_v2($remove_cookie);
                    $tmp_cookie_info['inst_reme'] = 'n';
                    $ACCOUNT->set_user_cookie_v2($tmp_cookie_info,$COOKIE_TIMOUT);                    
                  }
    // save cookie ----------------------------------------------------------------------- END
    
    Header('Location: schedule.php');
}else if($in['email']=='ko6610'){
    $_SESSION['SKIDIY']['login'] = true;
    $_SESSION['SKIDIY']['instructor'] = $in['password'];
    Header('Location: schedule.php');
}else{
    Header('Location: index.php?msg=login_error');
    exit();
}
?>