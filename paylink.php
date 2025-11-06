<?php
require('includes/sdk.php'); 
//_d($_SESSION['user_idx']); exit();

if(empty($_SESSION['user_idx'])){ 
  _go("account_login.php?id=".urlencode($_GET['id'])."&flag=pay");
}
//_d($_SESSION['user_idx']); 
$filters = array(
    'id'          => FILTER_SANITIZE_STRING,
    'refer'       => FILTER_SANITIZE_STRING,
);//_v($_POST);
$in = filter_var_array(array_merge($_REQUEST,$_POST), $filters);//_v($in);//exit();

$ko = new ko();
$instructorInfo = $ko->getInstructorInfo();
$parkInfo = $ko->getParkInfo();//_v($parkInfo);exit();
//echo crypto::dv('tsPSXw Cuyx0PQQD2wyuGuBoGDKU8ef3d1jpfcsmrbM='); exit();
$oidx = crypto::dv($in['id']);//_d($oidx);
//_d($in['id'].'-->'.$oidx); exit();
$order = $ko->getOneOrderInfo(['oidx'=>$oidx]);//_j($order);exit();

if($order['status']!=Payment::PAYMENT_CREATED){//此連結已失效
  //echo 'status='.$order['status'];exit();
  _go("message.php?msg=paylinkExpire&status={$order['status']}&serial={$order['oidx']}&id={$oidx}");
  //Header("Location: message.php?msg=paylinkExpire&status={$order['status']}&serial={$order['oidx']}&id={$oidx}");
  
}//_v($order);

if(!empty($in['refer'])){
  $ACCOUNTFUNC = new MEMBER();
  $ACCOUNTFUNC->set_user_cookie_v2(
    ['refer' => $in['refer']]
    , 0
  );
}
?>
<!DOCTYPE html>
  <html>
    <head>
      <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover, user-scalable=false"/>
      
      <!--Import materialize.css-->
      <link rel="stylesheet" href="assets/css/materialize.min.css">
      <!--Import custom.css-->
      <link rel="stylesheet" href="assets/css/custom.min.css">
      <!--Import jQuery-->
      <script src="assets/js/jquery.min.js"></script>
    </head>

    <body>
      <header>
      <?php require('nav.inc.php');?>
      </header>

      <form action="pay.php" method="post" id="paymentForm">
      <input type="hidden" name="id" value="<?=urldecode(crypto::ev($oidx))?>">
      <main>
        <div class="container-fuild">
          <a href="javascript:" id="return-to-top" class="waves-effect waves-light"><i class="material-icons">arrow_upward</i></button></a>

          
          <div class="row header-block-booking">
            <div class="header-img-bottom">
              <img src="assets/images/header_img_bottom.png" alt="">
            </div>
            <img src="assets/images/header_class_main_img.jpg">    
          </div> 

        <div class="row header-block-float container-xl">
          <div class="col S12 m4 offset-m2">
            <img src="assets/images/class_booking_steps.png" class="steps-img steps-img-left">
          </div>
        </div>


        <!--class table-->
         <div class="row container-xl">
          <div class="col s12 m10 col-centered">
            訂單編號：<?=$order['orderNo']?>
          </div>
         </div>

         <div class="row container-xl">
           <div class="col s12 m10 col-centered">
              <table class="booking-table">
                <thead>
                  <tr>
                      <th width="25%"><p class="left">日期<br>時間/堂次</p></th>
                      <th width="30%"><p class="left">雪場<br>教練/種類</p></th>
                      <th><p class="center">人數</p></th>
                      <th width="20%"><p class="right">金額</p></th>
                  </tr>
                </thead>

                <tbody>
                <?php foreach ($order['schedule'] as $n => $s) { ?>
                  <tr>
              
                    <td><p class="date"><?=substr($s['date'],5)?></p><?=$parkInfo[$s['park']]['timeslot'][$s['slot']]?><br><span class="badge badge-gray"><?=$s['slot']?>th</span></td>
                    <td><?=$parkInfo[$s['park']]['cname']?><br>
                      <div class="class">
                        <div class="class-d">
                          <div class="avatar-img">
                            <img src="https://diy.ski/photos/<?=$s['instructor']?>/<?=$s['instructor']?>.jpg" alt="">
                          </div>
                          <p><?=$s['instructor']?></p>
                        </div>
                        <span class="badge badge-gray"><?=strtoupper($s['expertise'])?></span>
                      </div>
                    </td>
                    <td><br>
                      <p class="center"><?=$s['studentNum']?> 位</p>
                    </td>
                    <td class="right" style="display: flex"><div id="fee<?=$n?>"><p class="price" style="align-items:center;"><?=number_format($s['fee'])?> <span class="badge badge-primary"><?=$order['currency']?></span></p></div></td>
                  </tr>
                  <?php }//foreach ?>

                 <tr>
                   <td colspan="4">
                    <h5>費用總計</h5>
                     <div class="row sum-block">
                        <div class="col s12">
                          <div class="card-panel">
                            <div class="row flex-stretch">
                              <div class="col s12 m3 payment-sum">
                                <p class="col s4 m12"><i class="material-icons">ac_unit</i><br>學費總計</p>
                                <div id="price"><p class="num col s8 m12"><br><?=number_format($order['price'])?><small class="badge badge-primary"><?=$order['currency']?></small></p></div>
                                
                              </div>
                              <div class="col s12 m3 payment-sum">
                                <p class="col s4 m12"><i class="material-icons">card_giftcard</i><br>折扣優惠</p>
                                <div id="discount"><p class="num col s8 m12"><br><?=number_format($order['discount']-$order['specialDiscount'])?><small class="badge badge-primary"><?=$order['currency']?></small></p></div>
                                
                              </div>
                              <div class="col s12 m3 payment-sum">
                                <p class="col s4 m12"><i class="material-icons">payment</i><br>預付訂金<br>刷卡金額<br><sup>(匯率 <?=$order['exchangeRate']?>)</sup>                          
                                <div id="paid" class="cR"><p class="num col s8 m12"><br><?=number_format($order['prepaid'])?> <small class="badge badge-primary"><?=$order['currency']?></small></p></div>
                                <p class="col s4 m12"></p>
                                <div id="prepaid"><p class="num col s8 m12"><br><?=number_format($order['paid'])?> <small class="badge badge-primary">NTD</small></p></div>
                                
                              </div>
                              <div class="col s12 m3 payment-sum">
                                <p class="col s4 m12"><i class="material-icons">attach_money</i><br>上課尾款</p>
                                <div id="payment"><p class="num col s8 m12"><br><?=number_format($order['payment']+$order['specialDiscount'])?> <small class="badge badge-primary"><?=$order['currency']?></small></p></div>
                                
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="input-field col s12">
                        說明事項：<?=$order['requirement']?>
                        <br>
                        <span style="color: red; font-size:1rem;">＊此付款連結為一次性付款，當點擊下方『進行付款』後，需完成線上刷卡手續。付款流程中若未完成，請重新索取付款連結。謝謝您的配合與諒解～</span>
                        </div>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>


            

            <div class="row space-top-2 center">
            <div class="row">
              <div class="col s12 center-align">
                <br>
                 <button class="btn btn-primary modal-trigger" data-target="terms">進行付款 <i class="material-icons">keyboard_arrow_right</i></button>
              </div>
            </div>
            </div>
         </div>        
      </main>
      </form>
      
      <footer>
        <div class="footer-copyright">
          <p class="center-align">© 2018 diy.ski</p>
        </div>
      </footer>

      <!-- Modal -->
      <div id="terms" class="modal modal-fixed-footer">
        <div class="modal-content">
          <div class="row center">
            <div class="col s11 col-centered">
              <i class="material-icons">error_outline</i>
              <h4>預訂滑雪課程服務說明</h4>
            </div>
          </div>
          <div class="row">
            <div class="col s11 col-centered">
              <h5>訂課前注意事項</h5>
              <ol>
                <li>尾款請準備日幣現金在上課時交給教練。</li>
                <li>若於上課期間無故曠課，將沒收訂金賠償教練損失，除非提供相關證明，因天災、意外原因，非故意曠課，才會退還訂金。</li>
                <li>此為自助行程，請提早在上課時間前抵達，以免影響上課時間，教練會按照時間準時上下課。</li>
                <li>預定課程完成後若預取消，需遵守以下列條款。
                  <ul>
                    <li>&nbsp;&nbsp;&nbsp;🚨2個月前取消，訂金全額退費；</li>
                    <li>&nbsp;&nbsp;&nbsp;🚨1個月前取消，退還50%訂金；</li>
                    <li>&nbsp;&nbsp;&nbsp;🚨1個月內取消，訂金不退還。<br>(以上退還金額需扣除刷卡金額3%手續費後轉帳退回)</li>
                  </ul>
                </li>
                <li>進入付訂頁面時請勿跳出或關閉視窗，以免造成訂單錯誤需等待課程釋出才能重新預訂。</li>
                <li>「按下「結帳」鈕之後，若中途離開或付款失敗，系統將保留課程20分鐘，請稍等待再重新訂課，謝謝」。</li>
                <li>訂課完請記得至後台更新連絡方式，教練將會在上課前一週主動連繫。</li>
              </ol>
              <br><br><br><br>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <div class="row">
            <label>
              <input type="checkbox" id="read"/>
              <span class="font-primary">我已閱讀完畢</span>
            </label>
          </div>
          <button data-target="success-msg" class="waves-effect btn btn-primary align-center" id="paymentBtn">確認並付款 <i class="material-icons">navigate_next</i></button>
        </div>
      </div>
      

      
      <!--JavaScript at end of body for optimized loading-->
      <script src="assets/js/materialize.min.js"></script>
      
      <!--custom js-->
      <!--<script src="assets/js/custom.js"></script>-->

      <script src="skidiy.data.php"></script>
      <script src="skidiy.func.php"></script>
      <script>
      function _d(d){console.log(d)}
      function _a(a){alert(a)}

      $(document).ready(function(){
        $('.sidenav').sidenav();
        $('.modal').modal();
        $('select').formSelect();

        $('#paymentBtn').on('click', function(){
            if($('#read').prop('checked')){
              paymentForm.submit();
            }else{
              _a('請勾選我已閱讀完畢，才可進行付款喔～');
            }
        });
      });
      </script>      


    </body>
  </html>