<?php
require('includes/sdk.php'); 
$KO = new ko();

$ERR_MSG = '';
$page_from = isset($_REQUEST['from']) ?$_REQUEST['from']:'INDEX' ;
 //_d($_REQUEST['flag']);
if((isset($_REQUEST['act']) && $_REQUEST['act']=='login') || (isset($_REQUEST['act']) && $_REQUEST['act']=='relogin')  ){  
      $ACCOUNT = new MEMBER();  

      // modify the _post data with cookie auth for relogin from other page
      if($_REQUEST['act']=='relogin'){
              $cookie_data = $ACCOUNT->get_user_cookie();
              if(isset($cookie_data['user_rememberme']) && $cookie_data['user_rememberme']=="y"){
                    //$ACCOUNT->show_user_cookie();             
                    if(isset($cookie_data['user']) && isset($cookie_data['pwd'])){ 
                          // 注意 cookie 會用 urlencode 儲存                 
                          $DECRYPTO_PWD = crypto::dv(urldecode($cookie_data['pwd'])); 
                          $_POST['username'] = $cookie_data['user'];                    
                          $_POST['password'] = $DECRYPTO_PWD;   
                          $login_info['cookie']  = 'Y'; 
                    }
                  //echo 'login with cookie user/pwd ';
              }else{
                $login_info['cookie']  = 'N'; 
              }        
      }

      if(!empty($_POST['username']) && !empty($_POST['password'])){
        // search new db first
        $account_info['email'] = trim($_POST['username']);
        $account_info['tb'] = 'members_v2';  
        if(strstr($account_info['email'],'\'') || strstr($account_info['email'],'#') || strstr($account_info['email'],' ') || strstr($account_info['email'],';')  ) {
                $KO->log([
                    'severity'  =>  'Security',
                    'user'      =>  $_POST['username'],
                    'oidx'      =>  'n/a',
                    'resp'      =>  'SQL Injection Detected',
                    'msg'       =>  $account_info['email'],
                ]);
        } 
        $R=$ACCOUNT->get_account($account_info);
        if($R == false){                // member_v2 無記錄
              // search old db
              $account_info['tb'] = 'members';  
              //$R=$ACCOUNT->get_account($account_info);
              $R = false;// 2019.01.07 members table 已經不在，不用在作搬移動作
              if($R == false){                  // member    無記錄                
                // acount is un-exist
                //_d('Login Fail！ You need to create a new account!!');
                $LogMsg['Account']      =  $_POST['username'];
                $LogMsg['Dpwd']         =  '****';
                $LogMsg['Epwd']         =  md5($_POST['password']); 
                $LogMsg['Result']       =  '**** Search from old DB ****';
                if(!empty($login_info['cookie'])){
                  $LogMsg['CookieLogin']  =  $login_info['cookie'];
                }
                $LogMsg['BreakPoint']   =  __FILE__.':'.__LINE__;                
                $KO->log([
                    'severity'  =>  'Login',
                    'user'      =>  $_POST['username'],
                    'oidx'      =>  'n/a',
                    'resp'      =>  $LogMsg['Result'],
                    'msg'       =>  json_encode($LogMsg, JSON_UNESCAPED_UNICODE),
                ]);                
                $ERR_MSG = '帳號不存在或密碼錯誤 ！ ! ';
              }else{                        // member    有記錄 （舊）
                // transfer to new db(member_v2)
                //_d('Old account found! try to transfer to new TB');
                //var_dump($R);
                $ACCOUNT->apply($R);

                $login_info['user'] = $_POST['username'];
                $login_info['pwd']  = $_POST['password']; 
                $login_info['tb']  = 'members_v2'; 
                $R = $ACCOUNT->login($login_info);
                    
                // 進入驗證流程     
                if($R){           
                  //echo 'Login success （進入驗證流程）'.$R['active_status'];
                  _go('account_verify.php');          
                  exit();
                  //_go('account_info.php');

                }else{
                  _d('Login Fail');
                  $ERR_MSG = 'Internal transfer Error!';
                }       
              } 
        }else{                          // member_v2 有記錄
              $login_info['user'] = trim($_POST['username']);
              $login_info['pwd']  = $_POST['password']; 
              $login_info['tb']  = 'members_v2'; 
              
              // login with cookie user/pwd if rememberme selected
              // Rewrite the _post for cookie login
              $cookie_data = $ACCOUNT->get_user_cookie();
              if(isset($cookie_data['user_rememberme']) && $cookie_data['user_rememberme']=="y"){
                    //$ACCOUNT->show_user_cookie();             
                    if(isset($cookie_data['user']) && isset($cookie_data['pwd'])){ 
                          // 注意 cookie 會用 urlencode 儲存                 
                          $DECRYPTO_PWD = crypto::dv(urldecode($cookie_data['pwd'])); 
                          $login_info['user'] = $cookie_data['user'];                    
                          $login_info['pwd']  = $DECRYPTO_PWD;   
                          $login_info['cookie']  = 'Y'; 
                    }
                  //echo 'login with cookie user/pwd ';
              }else{
                  $login_info['cookie']  = 'N'; 
              }

              $R = $ACCOUNT->login($login_info);
              if($R){ 
                //echo 'Login success （正常登入）'.$R['active_status'];exit();
                $LogMsg['Account']      =  $login_info['user'];
                $LogMsg['Dpwd']         =  substr($login_info['pwd'],0,4).'****';
                $LogMsg['Epwd']         =  md5($login_info['pwd']); 
                $LogMsg['Result']       =  'Success';
                $LogMsg['CookieLogin']  =  $login_info['cookie'];
                $LogMsg['BreakPoint']   =  __FILE__.':'.__LINE__;                
                $KO->log([
                    'severity'  =>  'Login',
                    'user'      =>  $login_info['user'],
                    'oidx'      =>  'n/a',
                    'resp'      =>  $LogMsg['Result'],
                    'msg'       =>  json_encode($LogMsg, JSON_UNESCAPED_UNICODE),
                ]); 

                // 登入成功，依該帳號狀態，做頁面轉換 ＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝
                $status = $R['active_status'];
                switch($status){
                  case 'DEACTIVE':                            // 帳號已轉換完成，但尚為完成資料驗證
                    //_go('account_verify.php?s='.$status); 
                    _go('account_info.php?s='.$status); 
                    break;
                  case 'PHASE1_DONE':                         // 信箱驗證完成（已點選驗證link）  
                  case 'MAIL_VERIFY_DONE':                    // 信箱驗證完成（已點選驗證link）
                    // 2018.08.13  改為mail 認證即可
                    if($_REQUEST['flag']=="pay"){
                      //echo $_POST['id']; exit();
                      //_go('paylink.php?id='.$_POST['id']); 
                      header("Location: http://".domain_name."/paylink.php?id=".$_REQUEST['id']);  
                    }else{ // 信箱驗證 登入成功              
                      if(isset($_POST['rememberme']) && $_POST['rememberme']=="on"){
                        //echo ">>".$_POST['rememberme'];  
                        $login_info['user_rememberme']  = 'y';     
                        $CRYPTO_PWD = crypto::ev($login_info['pwd']);   
                        //echo '<br>raw= '.$login_info['pwd'].'<br>cry= '.$CRYPTO_PWD.'<br>'.urlencode($CRYPTO_PWD);
                        $login_info['pwd']  = $CRYPTO_PWD;                
                        $ACCOUNT->set_user_cookie($login_info); // 注意 cookie 會用 urlencode 儲存
                        //setcookie("user",$login_info['user'],time()+3600);
                        //setcookie("pwd",$login_info['pwd'],time()+3600);
                        //echo 'username='.$_COOKIE['username'];
                      }else{
                        $tmp_login_info['user'] = '';
                        $tmp_login_info['pwd'] = '';
                        $tmp_login_info['user_rememberme'] = 'n';
                        $ACCOUNT->set_user_cookie($tmp_login_info);
                      }                                            
                      //_v($page_from);
                      //_v($_REQUEST['from']);
                      $arg['date']       = isset($_REQUEST['date']) ? '' : $_REQUEST['date'];
                      $arg['expertise']  = isset($_REQUEST['expertise']) ? '' : $_REQUEST['expertise'];
                      $arg['park']       = isset($_REQUEST['park']) ? '' : $_REQUEST['park'];  
                      $arg['order_id']   = isset($_REQUEST['id']) ? $_REQUEST['id']:'';  
                      // 避免undefine 跑版
                      switch($page_from){
                        case 'SCH':
                          //_go('schedule.php');
                          _go('schedule.php?date='.$arg['date'].'&expertise='.$arg['expertise'].'&park='.$arg['park']); 
                          break;
                        case 'MYORDER':
                          _go('class_booking_edit.php?id='.$arg['order_id']); 
                          break;                          
                        case 'RESERV':
                          _go('reservation.php?date='.$_REQUEST['date'].'&expertise='.$_REQUEST['expertise'].'&park='.$_REQUEST['park']); 
                          break; 
                        case 'INSURM': // mail 點選保單 鏈結
                          _go('insurance_apply.php?id='.$arg['order_id']); 
                          break;                           
                        default:
                          _go('index.php');  
                      }
                      
                    }
                    // 直接進入手機驗證
                    //_go('2fauth.php');             
                    break;
                  case 'PHASE2_DONE':   
                  case 'PHONE_VERIFY_DONE': // 已完成驗證流程 （信箱＋手機）
                  case 'ACTIVE':        // 已完成驗證流程 （信箱＋手機）, 且登入過
                    _go('index.php');  
                    break; 
                  case 'PHASE2_FAIL':
                    _go('account_info.php?s='.$status); // 重啟驗證流程
                    break;   
                  default:  
                    //_go('account_verify.php?s='.$status); // 重啟驗證流程
                    //_go('account_info.php?s='.$status); // 重啟驗證流程
                    break;                      
                }
                //_go('account_verify.php');
              }else{ // account exist but login fail !! (error Password !!) 
                //_d('Login Fail');       
                $LogMsg['Account']      =  $login_info['user'];
                $LogMsg['Dpwd']         =  $login_info['pwd'];
                $LogMsg['Epwd']         =  md5($login_info['pwd']); 
                $LogMsg['Result']       =  'Fail';
                $LogMsg['CookieLogin']  =  $login_info['cookie'];
                $LogMsg['BreakPoint']   =  __FILE__.':'.__LINE__;                
                $KO->log([
                    'severity'  =>  'Login',
                    'user'      =>  $login_info['user'],
                    'oidx'      =>  'n/a',
                    'resp'      =>  $LogMsg['Result'],
                    'msg'       =>  json_encode($LogMsg, JSON_UNESCAPED_UNICODE),
                ]);                
                $ACCOUNT->clear_user_cookie(); // 清 cookie
                $ERR_MSG = '帳號不存在或密碼錯誤 !';
              } 
        }
      }else{
        $ERR_MSG = '請填寫您的帳號及密碼欄 !';
      } 
}

?>
<!DOCTYPE html>
  <html>
    <head>
    <meta name="robots" content="noindex, nofollow">
    <?php require('head.php'); ?>
    </head>

    <script type="text/javascript">
      $(document).ready(function(){
        $('#username').focus();
        $('#password').focus();

        $('select').formSelect();

        $(function(){          
               $('#subscribe-email-submit').on('click', function(e){         
                    e.preventDefault();
                    $.ajax({
                        //url: "account_login.php?act=pwforget",
                        url: "post-cgi.php?cmd=fgpwd",
                        type: "POST",
                        data: $('#pwdforget-form').serialize(),                   
                        success: function(resp){
                            //alert("Successfully submitted."+$(this).serialize()+$("#email").val()+'-mj')
                            if(resp==101004){ // success reset request
                                $('#send').modal('open');
                            }else if(resp==100006){ // null input
                                $('#PERRMSG2').text('請填寫正確的email！');
                                $('#err_msg').modal('open');
                            }else if(resp==100007){ // no account 
                                $('#PERRMSG2').text('錯誤的信箱帳號！');
                                $('#err_msg').modal('open');
                            }else{
                                $('#PERRMSG2').text('Internal Error！'+resp);
                                $('#err_msg').modal('open');                              
                            }                            
                        }
                    });
               }); 
        });
        
        $(function(){          
               $('#regbtn').on('click', function(e){         
                    e.preventDefault();
                    $.ajax({
                        url: "post-cgi.php?cmd=reg",
                        type: "POST",
                        data: $('#regform').serialize(),                   
                        success: function(resp){
                            if(resp==101001){ // success reg
                                $('#signup_msg').modal('open');
                            }else if(resp==100006){
                                $('#PERRMSG2').text('資料填寫不完整');
                                $('#err_msg').modal('open'); 
                            }else if(resp==100001){                                
                                $('#PERRMSG2').text('帳號已存在!');
                                $('#err_msg').modal('open');                                 
                            }else{
                                $('#PERRMSG').text('Debug:'+resp);
                                $('#err_reg').modal('open');                                
                            }   
                            //alert("Successfully submitted!!"+resp);
                        }
                    });
               }); 
        });


      });  
    </script>




    <body>
      <header>
        <?php require('nav.inc.php');?>
      </header>


      <main>
        <div class="container-fuild">
          <a href="javascript:" id="return-to-top" class="waves-effect waves-light"><i class="material-icons">arrow_upward</i></button></a>
          <div class="row header-block-login">
            <div class="header-img-bottom">
              <img src="assets/images/header_img_bottom.png" alt="">
            </div>
            <img src="assets/images/header_login_main_img.jpg">
            <div class="col s10 push-s1  m6 push-m3  header-block-content">
                <p class="text-center">會員帳號</p>
                <p class="resort-name">登入 <span></span></p>
              </div> 
          </div> 
        </div>

              <div class="container">
               <div class="row">
                 <div class="col s12 l8 col-centered center">
                    <p class="text-center"></p>
                     
                    <p class="center"><font color="#ff0000"><?=$ERR_MSG;?></font></p> 
                    <p class="center">登入後進行選課預約以及管理您的帳號資訊</p>
                    <form class="col s12" action="?act=login&from=<?=$page_from;?>" method="post">
                      <input type="hidden" name="id" value="<?=urlencode($_REQUEST['id'])?>">
                      <input type="hidden" name="flag" value="<?=$_REQUEST['flag']?>">
                      <!-- redirect args for reserv  -->
                      <input type="hidden" name="date" value="<?=$_REQUEST['date']?>">
                      <input type="hidden" name="expertise" value="<?=$_REQUEST['expertise']?>">
                      <input type="hidden" name="park" value="<?=$_REQUEST['park']?>">


<?php
                    if(isset($_COOKIE['user_rememberme']) && $_COOKIE['user_rememberme'] == 'y'){
                        $user = (isset($_COOKIE['user'])) ? $_COOKIE['user']:'';
                        $pwd = (isset($_COOKIE['pwd'])) ? $_COOKIE['pwd']:'';
                    }else{
                        $user = '';
                        $pwd = '';
                    }
?>                      
                      <div class="input-field col s12">
                        <label  >帳號</label>
                        <input type="text" name="username" id="username" value="<?=$user?>" placeholder="請填寫您註冊時的email">
                      </div>
                      <div class="input-field col s12">
                        <label >密碼</label>
                        <input type="password" name="password" id="password" value="<?=$pwd?>">
                      </div>
                      <div style="font-size:1rem; color:red; ">
                        <span style="text-align:left;">～溫馨提醒您～<br>
                        <ul>
                          <li>＊今年起我們只改用Email登入，若您之前是透過Facebook登入，請使用下方忘記密碼，輸入您的FB Email來重置密碼後登入。
                          </li>
                          <li>＊您也可以使用新的Email來重新註冊，唯查無訂單時請與我們聯繫。 Email: admin@diy.ski <br>造成您的不便還請見諒！
                          </li>
                        </ul><br>
                        </span>
                      </div>

                      <p>

                      </p>

                      <div class="col s12">
                        <!--<a onclick="location.href='account_info.html'" class="btn btn-primary waves-effect waves-light space-top-2" >登入 <i class="material-icons">arrow_forward</i></a>-->
                        <button class="modal-trigger modal-close waves-effect btn btn-primary align-center" type="submit" name="action" id="loginbt">登入 <i class="material-icons">arrow_forward</i></button>   
                        <?php 
                            $check_string = (isset($_COOKIE['user_rememberme']) && $_COOKIE['user_rememberme'] == 'y') ? 'checked="checked"':'';
                        ?>
                        <label class="align-center">
                          <input id="rememberme" name="rememberme" type="checkbox" class="filled-in"  <?=$check_string; ?> />
                          <span> 記住我</span>
                        </label>

                      </div>
                      <div class="col s12">
                        <button data-target="signup" class="waves-effect waves-light btn-flat space-top-2 modal-trigger" type="submit" name="action">申請 <i class="material-icons">account_circle</i></button>
                        <button data-target="forgot" class="waves-effect waves-light btn-flat space-top-2 modal-trigger" type="submit" name="action">忘記密碼 <i class="material-icons">help_outline</i></button>                        
                      </div>
                    </form>
                  </div>
               </div>
              </div>

      </main>

      
      <footer>
        <div class="footer-copyright">
          <p class="center-align">© 2018 diy.ski</p>
        </div>
      </footer>


      <!-- Modal Structure -->      
      <div id="signup" class="modal center">
      <form class="col s12" id="regform" name="regform" action="#" method="post">
        <div class="modal-content">
          <i class="material-icons">person_pin</i>
          <h4>申請</h4>
          <div class="row space-top-2">
            <div class="col s12 col-centered">              
              <p>請填寫以下資料</p> 
              <div class="input-field col s12">
                <label for="">Email</label>
                <input type="text" name="email" value="">
              </div>

              <div class="input-field col s12 m6">
              <select class="browser-default" id="country" name="country">
                <option value="0" selected>請選擇所在國家</option>
                <!--
                <option value="886" selected>台灣(TW)</option>
                <option value="86">中國(CN)</option>-->
              <?php
                foreach($COUNTRY_CODE as $k => $v){
                  //echo $k."|";
                  echo '<option value="'.$COUNTRY_CODE[$k]['code'].'">'.$COUNTRY_CODE[$k]['name'].'</option>';
                }
              ?> 

              </select>
              </div>

              <div class="input-field col s12 m6">
                <label for="" >手機</label>
                <input type="text" id="phone" name="phone" value="">                
              </div>

              <div class="input-field col s12 m6">
                <label for="">密碼</label>
                <input type="password" id="passwd" name="passwd" value="">
              </div>

              <div class="input-field col s12 m6">
                <label for="">䁥稱</label>
                <input type="text" id="name" name="name" value="">
                <!--<span style="color:red;"><sup>＊教練請填小寫英文代號。</sup><br><sup>如:頭獎徽請填 <u>jeter</u> </sup></span>-->
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button id="regbtn"  class="modal-trigger modal-close waves-effect btn btn-primary align-center">註冊 <i class="material-icons">check</i></button>
          
        </div>
      </form>  
      </div>
      

      
      <div id="forgot" class="modal center">
        <form class="col s12" id="pwdforget-form" name="pwdforget-form" action="#" method="post">
        <div class="modal-content">
          <i class="material-icons">help_outline</i>
          <h4>忘記密碼</h4>
          <div class="row space-top-2">
            <div class="input-field col s12 m8 col-centered">
              <p class="space-2">請輸入您的Email帳號，<br>我們會將寄送「密碼重置函」。</p> 
              <input type="text" name="email" id="email" placeholder="Email" class="center" value="">
            </div>
            <span style="color: red; font-size:1rem;">＊也請留意SKIDIY的重置信件是否在廣告分類或垃圾桶內，謝謝～</span>
          </div>
        </div>
        <div class="modal-footer">         
          <button id="subscribe-email-submit" class="modal-trigger modal-close waves-effect btn btn-primary align-center"  >寄送 <i class="material-icons">send</i></button>
        </div>
        </form>
      </div>
      



      <div id="signup_msg" class="modal center">
        <div class="modal-content">
          <i class="material-icons">drafts</i>
          <h4>Email 驗證</h4>
          <div class="row space-top-2">
            <div class="input-field col s12 m8 col-centered">
              <p class="space-2">已送出會員申請</p> 
              <p class="space-2">請前往您的Email點擊「驗證連結」。</p> 
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button href="#!" class="modal-close waves-effect btn btn-primary align-center">知道了 <i class="material-icons">check</i></button>
        </div>
      </div>

      <div id="send" class="modal center">
        <div class="modal-content">
          <i class="material-icons">drafts</i>
          <h4>己寄送</h4>
          <div class="row space-top-2">
            <div class="input-field col s12 m8 col-centered">
              <p class="space-2">請前往您的Email確認，<br>並點擊「密碼重置」連結。</p> 
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button href="#!" class="modal-close waves-effect btn btn-primary align-center">知道了 <i class="material-icons">check</i></button>
        </div>
      </div>

      <div id="err_reg" class="modal center">
        <div class="modal-content">
          <i class="material-icons">sentiment_very_dissatisfied</i>
          <h4>Ooooops.....</h4>
          <div class="row space-top-2">
            <div class="input-field col s12 m8 col-centered">              
              <p id="PERRMSG" class="space-2">Internal Error</p> 
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button data-target="signup" class="modal-trigger modal-close waves-effect btn btn-primary align-center">知道了 <i class="material-icons">check</i></button>
        </div>
      </div> 

      <div id="err_msg" class="modal center">
        <div class="modal-content">
          <i class="material-icons">sentiment_very_dissatisfied</i>
          <h4>Ooooops.....</h4>
          <div class="row space-top-2">
            <div class="input-field col s12 m8 col-centered">
              <p id="PERRMSG2" class="space-2">....</p> 
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button href="#!" class="modal-close waves-effect btn btn-primary align-center">知道了 <i class="material-icons">check</i></button>
        </div>
      </div> 


      
      <!--JavaScript at end of body for optimized loading-->
      <script src="assets/js//materialize.min.js"></script>
      
      <!--custom js-->
      <script type="text/javascript">
      $(document).ready(function () {
        $('.sidenav').sidenav();
        $('.materialboxed').materialbox();
        $('.modal').modal();
        $('.datepicker').datepicker();
        $('select').formSelect();
        $('.tabs').tabs();
      });
      </script>
    </body>
  </html>