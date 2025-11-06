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
                <p class="resort-name">保險提醒 <span></span></p>
                <button id="myorder"  class="btn waves-effect waves-light btn-primary space-top-2" name="logoutbt">訂單資訊 <i class="material-icons">shopping_cart</i></button>
              </div> 
          </div> 
        </div>

              <div class="container">
               <div class="row">
                 <div class="col s12 l8 col-centered center">
                    <p class="text-center"></p>
                    <p>親愛的SKIDIY學員，
19-20此年度中国学生(港澳台籍必须在中国大陆工作或生活够半年，并从中国大陆出发开始行程)只要报名Skidiy课程，将在上课期间附带五日专属滑雪意外保险（安联环球境外旅游保险），不另收费，无法投保成功亦不退费。

安联环球境外旅游保险的保障如下：

1.意外身故，残疾，烧伤50000
2.医疗费用32万
3.医疗运送和送返50000

保险细节：
http://weixin.qiandanba.com/fanhaionline-wx/product/anlianjwlx/jwlx_proDetail.html
 
＊＊保险需在离开中国”一周前"提供，否则无法顺利投保＊＊为了自身权益请注意时间
請加聯絡人马宁Ma Ning, WeChat ID: mny880
1.告知上课日期(投保開始日期)、学员人数、订单编号、中国出境日期
2.提供每位成人学员的信箱、电话、护照影本，”请照下列格式提供“
3.提供每位未成年学员请提供护照影本及监护人的信箱、电话、护照影本，

”请照下列格式提供”
范例：
1.2/8上课,共2位, 6411 ,2/4出境
学员1：王美 135888888888 123456789@qq.com
学员2：王小美(未成年) 监护人：王美 135888888888 123456789@qq.com

护照照片1
护照照片2</p><hr>                        
                                                                                   
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