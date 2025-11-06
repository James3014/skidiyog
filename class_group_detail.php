
<?php
require('includes/sdk.php');
if(!isset($_SESSION['user_idx'])){
  _go('https://'.domain_name.'/account_login.php');
}
$filters = array(
    'gidx'        =>  FILTER_SANITIZE_NUMBER_INT,
);//_v($_POST);
$in = filter_var_array(array_merge($_REQUEST,$_POST), $filters);//_v($in);//exit();

$ko = new ko();
$exchangeRateInfo = $ko->getExchageRate();//_v($exchangeRateInfo);
$lesson = $ko->getGroupLessons($in['gidx']);
$lesson['paid'] = (int)$lesson['prepaid']*(float)$exchangeRateInfo[$lesson['currency']];
//_v($lesson);exit();
$studentNum = $ko->getGroupLessonStudents($lesson['gidx']);
$available = ($studentNum>=$lesson['max']) ? '已滿' : ($lesson['max']-$studentNum).' 人';

$parkInfo = $ko->getParkInfo();//_v($parkInfo);
$instructorInfo = $ko->getInstructorInfo();
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
      <style>
      sub{
        margin-left: 2rem;
        font-size: 1rem;
        color: red;
      }
      sup{
          color: red;
      }
      </style>
    </head>

    <body>
      <header>
        <?php require('nav.inc.php');?>
      </header>


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

        <div class="row group-list">
          <div class="col s12 m10 col-centered container-xl">
            <div class="card-panel item">
              <div class="row">
                <div class="col s12 l3 coach">
                <a href="https://diy.ski/<?=$lesson['instructor']?>" target="_blank">
                  <div class="coach-d">
                    <div class="avatar-img">
                      <img src="https://diy.ski/photos/<?=$lesson['instructor']?>/<?=$lesson['instructor']?>.jpg" alt="">
                    </div>
                    <p><?=isset($parkInfo[$lesson['park']]['cname'])?$parkInfo[$lesson['park']]['cname'].', ':''?><?=$instructorInfo[$lesson['instructor']]['cname']?> 教練</p>
                    <span class="badge badge-gray"><?=strtoupper($lesson['expertise'])?></span>
                    <span class="badge badge-primary">介紹</span>
                  </div>
                </a>
                </div>
                <div class="col s12 l6">
                  <h5><?=$lesson['title']?></h5>
                </div>
                <div class="info col s3 l4">
                    <p class="center-align">
                    <?php if(!empty($studentNum)){ ?>
                    <b style="font-size:2em; color:red;"><?=$studentNum?></b>人<br>已報名
                    <?php } ?>
                    <?php if($studentNum>=$lesson['min']){ ?>
                        <br><sup>確定開班</sup>
                      <?php }else{ ?>
                        <br><sup>尚缺<?=($lesson['min']-$studentNum)?>人開班</sup>
                      <?php } ?>
                    </p>
                  </div>
                <div class="info col s9 l4">
                  <p class="date col s12"><?=$lesson['start']?> ～ <?=$lesson['end']?></p>

                  <?php if($studentNum<$lesson['max']){ ?>
                  <p class="price col s12"><small>$</small><?=$lesson['fee']?><small>/人</small> <span class="badge badge-gray"><?=$lesson['currency']?></span></p>
                  <p class="people col s12">開班限制<span class="badge badge-primary"> <?=$lesson['min']?> ~ <?=$lesson['max']?> 人</span></p>
                  <p class="people col s12">可報名人數<span class="badge badge-primary"> <?=$available?></span></p>
                  <?php }else{ ?>
                  <p class="people col s12"><span class="badge badge-primary"> <?=$available?></span></p>
                  <?php } ?>

                  
                </div>
              </div>
              
            </div>

            <div class="row">
                <div class="col s12">
                  <h5>課程說明<sub>此為申請團體開課，報名未達開班標準(前)，教練有可能取消課程喔！</sub></h5>
                  <p><?=($lesson['content'])?></p>
                </div>
              </div>

            <table class="booking-table">
                <tbody>
                 <tr>
                   <td colspan="4">
                    <?php if($studentNum<$lesson['max']){ ?>
                    <h5>費用總計</h5>
                     <div class="row sum-block">
                        <div class="col s12">
                          <div class="card-panel">
                            <div class="row flex-stretch">
                              <div class="col s12 m4 payment-sum">
                                <p class="col s4 m12"><i class="material-icons">ac_unit</i><br>學費總計</p>
                                <p class="num col s8 m12"><?=number_format($lesson['fee'])?><small class="badge badge-primary"><?=$lesson['currency']?></small></p>
                              </div>
                              <div class="col s12 m4 payment-sum">
                                <p class="col s4 m12"><i class="material-icons">payment</i><br>刷卡金額<br>預付訂金<br><small class="font-primary">(匯率 <?=$exchangeRateInfo['JPY']?>)</small></p>
                                <p class="num col s8 m12"><?=number_format($lesson['prepaid'])?><small class="badge badge-primary"><?=$lesson['currency']?></small></p>
                                <p class="col s4 m12"></p>
                                <p class="num col s8 m12"><?=number_format($lesson['paid'])?><small class="badge badge-primary">NTD</small></p>
                              </div>
                              <div class="col s12 m4 payment-sum">
                                <p class="col s4 m12"><i class="material-icons">attach_money</i><br>上課尾款</p>
                                <p class="num col s8 m12"><?=number_format($lesson['fee']-$lesson['prepaid'])?><small class="badge badge-primary"><?=$lesson['currency']?></small></p>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    <?php }//還可報名 ?>

                      <div class="row  space-top-2">
                        <form action="paymentGroup.php" method="post" id="paymentForm">
                        <input type="hidden" name="gidx" value="<?=$in['gidx']?>">
                        <input type="hidden" name="price" value="<?=$lesson['fee']?>">
                        <input type="hidden" name="prepaid" value="<?=$lesson['prepaid']?>">
                        <input type="hidden" name="paid" value="<?=$lesson['paid']?>">
                        <input type="hidden" name="payment" value="<?=$lesson['fee']-$lesson['prepaid']?>">
                        <input type="hidden" name="currency" value="<?=$lesson['currency']?>">
                        <input type="hidden" name="exchangeRate" value="<?=$exchangeRateInfo[$lesson['currency']]?>">
                        
                        <?php if($studentNum<$lesson['max']){ ?>
                        <div class="input-field col s12"><textarea name="requirement" class="materialize-textarea" placeholder="請輸入您的授課需求"></textarea>
                        <label for="textarea1">備註</label></div>
                        <?php } ?>
                        </form>
                      </div>

                      <?php if($studentNum<$lesson['max']){ ?>
                      <div class="row  space-top-2">
                        <div class="col col-s12 col-m6 offset-m3">
                          <label>
                            <input type="checkbox" id="understand" />
                            <span class="font-primary">我已了解 <a href="#" class="modal-trigger" data-target="terms">退款&上課規則</a></span>
                          </label>
                          <br>  
                          <!--<label>
                            <input type="checkbox" id="confirmation" />
                            <span class="font-primary">我已確認下述聯絡方式： <br>E-mail: <?=$_SESSION['account']?> <br>手機：<?=$_SESSION['phone']?> 可連繫至我本人</span>
                          </label>-->
                        </div>
                      </div>
                      <?php } ?>

                    </td>
                  </tr>
                </tbody>
              </table>

              <div class="row space-top-2 center">
                <button class="btn btn-outline btn-outline-primary" onclick="history.back();"><i class="material-icons">keyboard_arrow_left</i> 回前一頁</button>
                <?php if($studentNum<$lesson['max']){ ?>
                <button class="btn btn-primary" id="paymentBtn">確認刷卡 <i class="material-icons">keyboard_arrow_right</i></button>
                <?php } ?>
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
            <!--<div class="col s11 col-centered">
              <h5>上課費用</h5>
              <p>每個雪場的定價不同，選擇好課程後會自動計算，查詢價格可以選擇「預約」教練查詢。</p>

              <h5>上課費用</h5>
              <p>每個雪場的定價不同，選擇好課程後會自動計算，查詢價格可以選擇「預約」教練查詢。</p>

              <h5>上課時間</h5>
              <p>每堂課為2小時，全天建議選擇上下午各一堂課，總共為4小時。部份雪場開放3堂或4堂課不同的上課時間，初學者建議課堂中間安排休息或自由練習的時間；進階者若訂連續兩堂課，教練可配合課程帶到較進階的雪道練習。</p>

              <h5>上課堂數</h5>
              <p>一般學習滑雪建議至少上4-6堂課才能完整學會滑雪；有些人因擔心不知道喜不喜歡滑雪，所以選擇上1-2堂課的方式體驗，其實去一趟日本的成本不低，包含機票、吃住、交通花費，開銷很容易超過3萬元，若一趟沒有學會滑雪，下一趟仍然要再花差不多的費用，也要花時間找教練。 因此建議最好一次就將滑雪學好，下一趟旅程就可以自己規劃滑雪，若需要精進滑雪技術再找教練上課即可。</p>

              <h5>親子滑雪</h5>
              <p>由於成人和小孩一起上課的狀況較多，請在報名前先了解</p>
              <a href="https://diy.ski/article.php?idx=15" target="_blank">親子及家族滑雪需注意的事項</a>

              <h5>上課常見問題</h5>
              <p><b>Q：為什麼學費不是依一般的計算方式以人頭計費呢？上課的人數上限為什麼限6人？</b></p>
              <p>A：一般滑雪教室或旅行社都是以人頭計算學費，每人大概多少費用，但這樣的方式有一個最大的問題是，你永遠不知道你的同學有幾位；有時會因為不同等級的學生人數相差太多，造成教練1個人教10幾個人的現象，因此當你看到依人頭收費時，請先確認你這班有沒有人數上限，最好要求限制人數，不然這堂課可能就白上了。</p>
              <p class="space-3">同時上課的人數越多，教練分配給每位學員的時間越少，有時候進度快的學員們只能在旁邊等待教練指導進度慢的人，這就是非小班制最常見的問題。 SkiDiy網站希望大家的學費花得值得，因此學費定價會因為人數的增加，大家分擔的費用相對變少，同時也限制上課人數在6人以內以確保教學品質。這是我們可以為學生及教練們把關的事，也是我們網站的原則。<br><a href="https://diy.ski/article.php?idx=11" target="_blank">更詳細的解釋請見：學習滑雪上課人數很重要</a></p>
            </div>-->
          </div>
        </div>
        <div class="modal-footer">
          <button data-target="success-msg" class="modal-trigger modal-close waves-effect btn btn-primary align-center">好的 <i class="material-icons">navigate_next</i></button>
        </div>
      </div>

      
      <!--JavaScript at end of body for optimized loading-->
      <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-rc.2/js/materialize.min.js"></script>
      
      <!--custom js-->
      <script>
      $(document).ready(function(){
        $('.sidenav').sidenav();
        $('.modal').modal();
        
        $('#paymentBtn').on('click', function(){
          if($('#understand').prop('checked') /*&& $('#confirmation').prop('checked')*/){
              paymentForm.submit();
            }else{
              alert('請勾選瞭解與確認後，才可進行付款喔～');
            }
        });

      });
      </script>

    </body>
  </html>