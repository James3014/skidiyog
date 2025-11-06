<?php
// 1. check the mail link & send the confrim code by sms
// 2. input the confirm code for phone number verify
require('includes/sdk.php'); 
if(!isset($_REQUEST['act']) || !isset($_REQUEST['tk']) ){ _d('Error request !!'); exit();}
$ACCOUNT = new MEMBER();
$COMMON_F = new COMMON_FUNC();

// 避免鏈結不斷被點選
if($_REQUEST['act']=='p1check' && isset($_REQUEST['tk'])){ // phase1: token & mail check
  $result = $ACCOUNT->phase1_check($_REQUEST['tk']);  
  /*
  if($result != false && $result['active_status'] == 'DEACTIVE' ){ // 避免重複驗證（sms 一直發）
    $p1_check_result ='STEP 1. 已完成 Email 驗證 ('.$result['email'].' )';
    //update to DB & send auth code with sms
    $AUTH_CODE = $COMMON_F->randomStr(2,4);
    //echo 'AUTH CODE: '.$AUTH_CODE .'<br>';
    // UPDATE to DB
    //echo 'UPDATE TO DB: '.$result['email'].'<br>';
    $update_data['2fa_authcode'] = $AUTH_CODE;
    $update_data['active_status'] = 'PHASE1_DONE'; // 有收到信 且有點選來到本畫面
    $ACCOUNT->update($result['idx'],$update_data);
    // SEND AUTH_CODE to USER 
    $SMS_PhoneNumber = '+'.$result['country'].$result['phone'];
    //echo 'SEND TO USER: '.$SMS_PhoneNumber.'<BR>';    

    $SNS_OBJ = new AWS_SNS();
    $SNS_OBJ->SEND_SMS($SMS_PhoneNumber,'<DIY.SKI> Your CODE:'.$AUTH_CODE,'PHPONE_DIRECTLY');   
  }else if($result['active_status'] == 'PHASE1_DONE' ){
    //_d('Email 已驗證完成！ ');    
    // 避免重複驗證（sms 一直發）
  }else if($result['active_status'] == 'PHASE2_DONE' ){
    _alert('信箱與手機皆已驗證完成！');    
    Header('Location: https://'.domain_name);
  }else{
    _d('STEP 1. Email 驗證失敗！ 或是 無效的確認鏈結！');
    exit();
  } 
  */
  // DEACTIVE --> PHASE1_DONE --> PHASE2_DONE
  if($result != false){
    switch ($result['active_status']) {
      case 'DEACTIVE':    // 認證信已寄送，點了後才會倒這頁面
          //update to DB & send auth code with sms
          $AUTH_CODE = $COMMON_F->randomStr(2,4);
          //echo 'AUTH CODE: '.$AUTH_CODE .'<br>';
          //UPDATE to DB
          $update_data['2fa_authcode'] = $AUTH_CODE;
          $update_data['active_status'] = 'PHASE1_DONE'; // 有收到信 且有點選來到本畫面
          $ACCOUNT->update($result['idx'],$update_data);
          // SEND AUTH_CODE to USER 
          $SMS_PhoneNumber = '+'.$result['country'].$result['phone'];
          //echo 'SEND TO USER: '.$SMS_PhoneNumber.'<BR>';    
          $SNS_OBJ = new AWS_SNS();
          $SNS_OBJ->SEND_SMS($SMS_PhoneNumber,'<DIY.SKI> Your CODE:'.$AUTH_CODE,'PHPONE_DIRECTLY');   
          break;
      case 'PHASE2_FAIL': // phase2 fail 跳回 phase1 重新手機驗證 (重發sms auth code)    
      case 'PHASE1_DONE': // EMAIL 認證完成
          //_d('Email 已驗證完成！ ');    
          // 避免重複驗證（sms 一直發）            
        # code...
        break;
      case 'PHASE2_DONE': // 手機驗證完成
          //_alert('信箱與手機皆已驗證完成！');    
          //Header('Location: https://'.domain_name);
          break;                              
      default:
        # code...
        break;
    }
  }else{
    _d('STEP 1. Email 驗證失敗！ 或是 無效的確認鏈結！');
  }




}



?>
<!DOCTYPE html>
  <html>
    <head>
      <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover, user-scalable=false"/>
      
      <!--Import materialize.css-->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-rc.2/css/materialize.min.css">
      <!--Import custom.css-->
      <link rel="stylesheet" href="assets/css/custom.min.css">
      <!--Import jQuery-->
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
      
      <!--swiper-->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.3.3/css/swiper.min.css">
      <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.3.3/js/swiper.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.3.3/js/swiper.esm.bundle.js"></script>
    </head>

    <script>
      $(document).ready(function(){
        $(function(){          
           $('#phoneverifybt').on('click', function(e){         
                e.preventDefault();
                $.ajax({
                    //url: "account_mobile_verify.php?act=p2check",
                    url: "post-cgi.php?cmd=account_p2_check",                       
                    type: "POST",
                    data: $('#2fauth-form').serialize(),                   
                    success: function(resp){
                        //alert("Successfully submitted."+resp);
                            if(resp==101006){ // MOBILE CHECK PASS
                                $('#success-msg').modal('open');
                            }else if(resp==100008){
                                //$('#ERRMSG').text('資料填寫不完整');
                                $('#PERRMSG').text('驗證碼錯誤');
                                $('#err_msg').modal('open'); 
                            }else{
                                $('#PERRMSG').text('內部錯誤');
                                $('#err_msg').modal('open');                                
                            }                        
                        

                    }
                });
           }); 

           $('#auth-done-bt').on('click', function(e){  
              //alert('Success & Redirect');       
              window.location.replace('https://<?=domain_name?>');
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
                
                <p class="resort-name">恭禧您！己完成Email驗證 <span></span></p>
                <p class="text-center">再一步就完成囉！</p>
              </div> 
          </div> 
        </div>

              <div class="container">
               <div class="row">
                 <div class="col s12 l8 col-centered center">
                    <p class="text-center"></p>                  
                    <form class="col s12 space-top-2" id="2fauth-form">
                        <input type="hidden" name="tk" style="width:180px;" value="<?php echo $_REQUEST['tk']; ?>" />
                        <input type="hidden" name="email" style="width:180px;" value="<?php echo $result['email']; ?>" />          
                        <h4><i class="material-icons">phone_iphone</i>手機驗證碼</h4>
                        <div class="row">
                          <div class="input-field col s12 m8 col-centered">
                            <p class="space-2">己將驗證碼傳送至您的手機，<br>請您前往確認後輸入。</p> 
                            <input type="text" value="" placeholder="手機簡訊驗證碼" class="center" name="code">
                          </div>
                        </div>
                        <!--
                        <button id="phoneverifybt" data-target="success-msg" class="btn waves-effect waves-light btn-primary space-top-2 modal-trigger" type="submit" name="action">驗證 <i class="material-icons">chevron_right</i></button>-->
                        <button id="phoneverifybt"  class="btn waves-effect waves-light btn-primary space-top-2 modal-trigger" type="submit" name="action">驗證 <i class="material-icons">chevron_right</i></button>
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

      <!-- Modal -->

      <div id="success-msg" class="modal center">
        <div class="modal-content">
          <i class="material-icons">person_pin</i>
          <h4>驗證成功</h4>
          <div class="row space-top-2">
            <div class="input-field col s12 m8 col-centered">
              <p class="space-2">恭禧您！己完成會員身份驗證。</p> 
              <p class="space-2">立即登入，安排課程吧。</p> 
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button id="auth-done-bt" href="https://<?=domain_name?>" class="modal-close waves-effect btn btn-primary align-center">知道了 <i class="material-icons">check</i></button>
        </div>
      </div>

      <div id="err_msg" class="modal center">
        <div class="modal-content">
          <i class="material-icons">sentiment_very_dissatisfied</i>
          <h4>Ooooops.....</h4>
          <div class="row space-top-2">
            <div class="input-field col s12 m8 col-centered">
              <p id="PERRMSG" class="space-2">....</p> 
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button href="#!" class="modal-close waves-effect btn btn-primary align-center">知道了 <i class="material-icons">check</i></button>
        </div>
      </div> 

      
      <!--JavaScript at end of body for optimized loading-->
      <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-rc.2/js/materialize.min.js"></script>
      
      <!--custom js-->
      <script src="assets/js/custom.js"></script>

      
    </body>
  </html>