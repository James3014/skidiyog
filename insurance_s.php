<?php
require('includes/sdk.php'); 
//echo 'Login: '.$_SESSION['account']."<br>";
//echo 'Status: '.$_SESSION['status']."<br>";
$ACCOUNT = new MEMBER();
$POST_CMD = 'insurance_fapply';


if(isset($_REQUEST['act']) && $_REQUEST['act']=='up_2fcheck'){
  // update DB

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
          $('#myorder_profile').on('click', function(e){         
              window.location.replace('my_order_list.php') 
          }); 
          $('#myorder_profile_b').on('click', function(e){         
              window.location.replace('my_order_list.php') 
          });           

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

           $('#gotit').on('click', function(e){         
              window.location.replace('insurance_note.php') 
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
                <p class="text-center"></p>
                <p class="resort-name">保單填寫 <span></span></p>
                <button id="myorder_profile"  class="btn waves-effect waves-light btn-primary space-top-2" name="logoutbt">回訂單資訊 <i class="material-icons">account_box</i></button>  
              </div> 
          </div> 
        </div>

              <div class="container">
               <div class="row">
                 <div class="col s12 l8 col-centered center">
                    <p class="text-center"></p>
                    <p>親愛的SKIDIY學員，
我們的新保險後台已完成，若要填寫保險資料
請先登入SKIDIY後，至『帳號』-> 『訂單資訊』 點擊對應訂單後
您可於費用總計上方找到【保單填寫】，並完成自己或是其他團員的內容填寫（如下圖示）
並請於上課前兩週完成填寫且送出核保，以利後續投保作業，謝謝。<br>
<img width="500" src="assets/images/sample.png">
<strong><font color="#3294d1">
溫馨小提醒： 若是您要更改保單人數，可在訂單本人的保單資料表中，更新被保團員人數即可！

提醒您: 若您先前已在舊表單上填寫過資料，亦請再重新填寫一次唷。</font></strong>
以上若有疑問，可回信至 admin@diy.ski，我們將盡快協助，謝謝。</p><hr>                        
                  <button id="myorder_profile_b"  class="btn waves-effect waves-light btn-primary space-top-2" name="logoutbt">回訂單資訊 <i class="material-icons">account_box</i></button>                                                                 
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
          <h4>保單資料</h4>
          <div class="row space-top-2">
            <div class="input-field col s12 m8 col-centered">
              <p class="space-2">已送出您的保單基本資料資料！<br>審核結果將於出發前兩週寄送至您所填寫信箱。</p> 
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button id="gotit" href="#!" class="modal-close waves-effect btn btn-primary align-center">知道了 <i class="material-icons">check</i></button>
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