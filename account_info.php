<?php
require('includes/sdk.php'); 
//echo 'Login: '.$_SESSION['account']."<br>";
//echo 'Status: '.$_SESSION['status']."<br>";
$ACCOUNT = new MEMBER();
if(isset($_SESSION['account'])){
  $account_info['email'] = $_SESSION['account'];  
  $R=$ACCOUNT->get_account($account_info);
  //var_dump($R);

  switch ($R['active_status']) {
    // 單純 存檔
    case 'PHASE1_DONE':
    case 'PHASE2_DONE':
      # code...
      $POST_CMD = 'account_modify';
      break;
    case 'DEACTIVE':    // 第一次注冊
    default:
      $POST_CMD = 'account_p1_check'; //  寄信 準備做 手機認證
      # code...
      break;
  }
}else{
  _go('account_login.php');
}

if(isset($_REQUEST['act']) && $_REQUEST['act']=='up_2fcheck'){
  // update DB
  $update_data['passwd']  = md5($_REQUEST['passwd']);
  $update_data['email']   = $_REQUEST['email'];
  $update_data['phone']   = $_REQUEST['phone'];
  $update_data['country'] = $_REQUEST['country'];
  $update_data['name']    = $_REQUEST['name'];
  $ACCOUNT->update($_SESSION['user_idx'],$update_data); 
  // send confirm mail
  $Link = "https://".domain_name."/account_mobile_verify.php?act=p1check&tk=".md5($_REQUEST['email']);
  $mail_info['email'] = $_POST['email'];
  $mail_info['subject'] = '信箱驗證';
  $mail_info['content'] = "Please click the follow link to verify your phone number !!\n\n Confirm Link: ".$Link;
  $ACCOUNT->send_mail($mail_info);    
  _d('Confirm Link had been send to your mail account: <a href="'.$Link.'">Link</a>');  
  //exit();
} 

?>
<!DOCTYPE html>
  <html>
    <head>
    <?php require('head.php'); ?>
    </head>

    <script>
      $(document).ready(function(){
        $(function(){          
           $('#modifybt').on('click', function(e){         
                e.preventDefault();
                $.ajax({
                    //url: "account_info.php?act=up_2fcheck",
                    url: "post-cgi.php?cmd=<?=$POST_CMD?>",                    
                    type: "POST",
                    data: $('#accountinfo-form').serialize(),                   
                    success: function(resp){
                        //alert("Successfully submitted."+resp)
                            if(resp==101005){ // user profile verify & MAIL CHECK PASS
                                $('#email_verify').modal('open');
                            }else if(resp==101008){ // user profile save ok                              
                                 $('#success-msg').modal('open');
                            }else if(resp==100006){
                                //$('#ERRMSG').text('資料填寫不完整');
                                $('#PERRMSG').text('資料填寫不完整');
                                $('#err_msg').modal('open'); 
                            }else{
                                $('#err_msg').modal('open');                                
                            }                         
                    }
                });
           });
           
           $('#logoutbt').on('click', function(e){       
              window.location.replace('index.php?act=logout') 
           });
           $('#myorder').on('click', function(e){         
              window.location.replace('my_order_list.php') 
           }); 
           $('#myfollow').on('click', function(e){         
              window.location.replace('my_follow_list.php') 
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
                <p class="resort-name">帳號資訊</p>
                <button id="myfollow"  class="btn waves-effect waves-light btn-primary space-top-1" name="logoutbt">課程追蹤 <i class="material-icons">add_alert</i></button>
                <button id="myorder"  class="btn waves-effect waves-light btn-primary space-top-1" name="logoutbt">訂單資訊 <i class="material-icons">shopping_cart</i></button>
                
              </div> 
          </div> 
        </div>

              <div class="container">
               <div class="row">
                 <div class="col s12 l8 col-centered center">
                    <p class="text-center"></p>
                    <p>提醒您！務必將您的帳號資訊保持在最新狀態，<br>以確保教練與客服人員於上課前可緊急和您聯繫教學事宜！</p>
                    <form class="col s12 space-top-2" id="accountinfo-form">
                      <div class="input-field col s12">
                        <label for="">E-mail: </label>
                        
                        <?php
                          
                            if($R['active_status']=='PHASE1_DONE'){ // 已驗證 不可編輯
                              echo '<input name="email" type="email" disabled value="'.$R['email'].'">';
                              //echo $R['email'].'ssss';
                            }else{
                              echo '<input name="email" type="email" value="'.$R['email'].'">';
                            }
                        ?>
                      </div>
                      <div class="input-field col s12 m6">
                        <!--<label for="">國家</label>                       
                        <input name="country" type="text" value="886">-->
                        <select class="browser-default" id="country" name="country">
                          <option value="0"  selected>請選擇所在國家</option>
                        <?php
                          foreach($COUNTRY_CODE as $k => $v){
                            //echo $k."|";
                            $sflag = '';

                            if($COUNTRY_CODE[$k]['code'] == $R['country']) $sflag = 'selected';
                            echo '<option '. $sflag.' value="'.$COUNTRY_CODE[$k]['code'].'">'.$COUNTRY_CODE[$k]['name'].'</option>';
                          }
                        ?> 
                        </select>                        
                      </div>                      
                      <div class="input-field col s12 m6">
                        <label for="">手機</label>
                        <input name="phone" type="text" value="<?php echo $R['phone']; ?>">
                      </div>
                      <div class="input-field col s12 m6">
                        <label for="">FB ID</label>
                        <input name="fbid" type="text" value="<?php echo $R['fbid']; ?>">
                      </div>
                      <div class="input-field col s12 m6">
                        <label for="">䁥稱</label>
                        <?php
                          $ro = ($R['type']=='instructor') ? 'readonly' : '';
                        ?>
                        <input name="name" type="text" value="<?php echo $R['name']; ?>" <?=$ro?>>
                      </div>
                      <!--
                      <div class="input-field col s12 m6">
                        <label for="">密碼</label>
                        <input name="password" type="password">
                      </div>
                      -->
                      <div class="input-field col s12 m6">
                        <label for="">Wechat</label>
                        <input type="text" name="wechat" value="<?php echo $R['wechat']; ?>" >
                      </div>
                      <div class="input-field col s12 m6">
                        <label for="">LINE</label>
                        <input type="text" name="line" value="<?php echo $R['line']; ?>" >
                      </div>
                      <div class="input-field col s12 m6">
                        <label for="">密碼</label>
                        <input name="passwd" value="****" type="password" >
                      </div>                      
                      </form>   
                        <!--<button id="modifybt" data-target="email_verify" class="btn waves-effect waves-light btn-primary space-top-2 modal-trigger" type="submit" name="action">修改 <i class="material-icons">chevron_right</i></button>-->
                        <button id="logoutbt"  class="btn waves-effect waves-light btn-primary space-top-2" name="logoutbt">登出 <i class="material-icons">exit_to_app</i></button>
                        <button id="modifybt"  class="btn waves-effect waves-light btn-primary space-top-2 modal-trigger" type="submit" name="action">修改 <i class="material-icons">chevron_right</i></button>
                                                                                   
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
      <div id="verify" class="modal center">
        <div class="modal-content">
          <i class="material-icons">phone_iphone</i>
          <h4>手機驗證碼</h4>
          <div class="row">
            <div class="input-field col s12 m8 col-centered">
              <p class="space-2">己將驗證碼傳送至您的手機，<br>請您前往確認後輸入。</p> 
              <input type="text" value="" placeholder="手機簡訊驗證碼" class="center">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button data-target="success-msg" class="modal-trigger modal-close waves-effect btn btn-primary align-center">確認 <i class="material-icons">check</i></button>
        </div>
      </div>

      <div id="success-msg" class="modal center">
        <div class="modal-content">
          <i class="material-icons">beenhere</i>
          <h4>修改成功</h4>
          <div class="row space-top-2">
            <div class="input-field col s12 m8 col-centered">
              <p class="space-2">您所修改的資料己儲存成功。</p> 
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button href="#!" class="modal-close waves-effect btn btn-primary align-center">知道了 <i class="material-icons">check</i></button>
        </div>
      </div>

      <div id="email_verify" class="modal center">
        <div class="modal-content">
          <i class="material-icons">drafts</i>
          <h4>Email 驗證</h4>
          <div class="row space-top-2">
            <div class="input-field col s12 m8 col-centered">
              <p class="space-2">基本資料更新完成！</p> 
              <p class="space-2">請前往您的Email點擊「驗證連結」。</p> 
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button href="#!" class="modal-close waves-effect btn btn-primary align-center">知道了 <i class="material-icons">check</i></button>
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
      <script src="assets/js/materialize.min.js"></script>
      
      <!--custom js-->
      <script src="assets/js/custom.js?v180920"></script>


    </body>
  </html>