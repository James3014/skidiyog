<?php
require('includes/sdk.php'); 

//pwd_reset_link_auth($token,$key)
/*
$ACCOUNT = new MEMBER();
if(isset($_REQUEST['tk']) && isset($_REQUEST['key']) ){ 
  $result = $ACCOUNT->pwd_reset_link_auth($_REQUEST['tk'],$_REQUEST['key']);
  if($result){
    //_d('you can reset your pwd now');
    if(isset($_REQUEST['act']) && $_REQUEST['act'] == 'pwdrest' ){
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
    }   
  }else{
    //_d('error');
    echo PWRSET_TK_ERR;
    exit();
  }
} 
*/
  

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
           $('#pwresetbt').on('click', function(e){         
                e.preventDefault();
                $.ajax({
                    //url: "account_pw_reset.php?cmd=pwreset",
                    url: "post-cgi.php?cmd=pwreset",
                    type: "POST",
                    data: $('#pwdreset-form').serialize(),                   
                    success: function(resp){
                        //alert("Successfully submitted.")
                            if(resp==101003){ // success reset
                                $('#alert-msg').modal('open');
                            }else if(resp == 100004){                                
                                $('#ERRMSG').text('您輸入了兩次不同之密碼設定！！\r\n請重新設定。');
                                $('#err_pwdreset_msg').modal('open');                                                          
                            }else if(resp == 100005){    
                                $('#ERRMSG').text('無效的來源鏈結!!\r\n請重新發起密碼重置！');
                                $('#err_pwdreset_msg').modal('open');                              
                            }                          
                    }
                });
           });

           $('#reset-done-btn').on('click', function(e){  
              //alert('Success & Redirect');       
              window.location.replace('https://<?=domain_name?>');
           });            
        });
      });  
    </script>


    <body>
      <header>
        <?php require('nav.inc.php');?>
        <!--
        <div class="navbar-fixed">
          <nav>
            <div class="nav-wrapper nav-header">
              <a href="#" data-target="mobile-nav" class="sidenav-trigger"><i class="material-icons">menu</i></a>
              <a href="/" ><img src="assets/images/logo-skidiy.png" alt="" class="logo"></a>
              <ul class="hide-on-med-and-down">
                <li><a href="index.html">雪場資訊</a></li>
                <li><a href="#">教練團隊</a></li>
                <li><a href="#">相關文章</a></li>
                <li><a href="#">聯絡我們</a></li>
              </ul>
              <div><a class=" waves-effect waves-light btn btn-outline" onclick="location.href='account_login.html'">登入</a></div>
            </div>
          </nav>
        </div>

        <ul class="sidenav" id="mobile-nav">
          <div class="row">
              <a href="#" class="sidenav-close"> <img src="assets/images/logo-skidiy.png" alt=""  class="logo"></a>
          </div>
          <li><a href="index.html">雪場資訊</a></li>
          <li><a href="#">教練團隊</a></li>
          <li><a href="#">相關文章</a></li>
          <li><a href="#">聯絡我們</a></li>
        </ul>
        -->
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
                <p class="resort-name">密碼重設 <span></span></p>
              </div> 
          </div> 
        </div>

              <div class="container">
               <div class="row">
                 <div class="col s12 l8 col-centered center">
                    <p class="text-center"></p>
                    <p>請輸入新密碼，<br>即可完成密碼重設！</p>
                    <form class="col s12 space-top-2" id="pwdreset-form" action="#" method="post">
                      <input type="hidden" name="tk" style="width:180px;" value="<?php echo $_REQUEST['tk']; ?>" />
                      <input type="hidden" name="key" style="width:180px;" value="<?php echo $_REQUEST['key']; ?>" />                    
                      <div class="input-field col s12">
                        <label for="">新密碼</label>
                        <input type="password" placeholder="新密碼" name="password" value="">
                      </div>
                      <div class="input-field col s12">
                        <label for="">確認密碼</label>
                        <input type="password" placeholder="請再一次輸入密碼" name="repassword" value="" >
                      </div>
                      
                        <button id="pwresetbt"  class="btn waves-effect waves-light btn-primary space-top-2 modal-trigger" type="submit" name="action">完成 <i class="material-icons">chevron_right</i></button>
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
      <div id="alert-msg" class="modal center">
        <div class="modal-content">
          <i class="material-icons">vpn_key</i>
          <h4>重設完成</h4>
          <div class="row space-top-2">
            <div class="input-field col s12 m8 col-centered">
              <p class="space-2">您的密碼己重設完成，<br>下次登入請使用新密碼。</p> 
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button id="reset-done-btn"  class="modal-close waves-effect btn btn-primary align-center">立即登入 <i class="material-icons">check</i></button>
        </div>
      </div>


      <div id="err_pwdreset_msg" class="modal center">
        <div class="modal-content">
          <i class="material-icons">sentiment_very_dissatisfied</i>
          <h4>Ooooops.....</h4>
          <div class="row space-top-2">
            <div class="input-field col s12 m8 col-centered">
              <p id="ERRMSG" class="space-2">您輸入了兩次不同之密碼設定！！<br>請重新設定。</p> 
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