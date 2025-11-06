<?php
require('includes/sdk.php');
if(!isset($_SESSION['user_idx'])){
  _go('https://'.domain_name.'/account_login.php');
}

$filters = array(
    'order'         => FILTER_SANITIZE_STRING,
    'from'          => FILTER_SANITIZE_STRING,
);//_v($_POST);
$in = filter_var_array(array_merge($_REQUEST,$_POST), $filters);//_v($in);exit();


$in['order'] = substr($in['order'], 0, -2);//刪除最後##
$order = explode('##', $in['order']);//_v($order);

$ko = new ko();
$instructorInfo = $ko->getInstructorInfo();
$parkInfo = $ko->getParkInfo();//_v($parkInfo);exit();

//var regexpRule = /d=(.+),s=([1-4]),p=([a-z]+),i=([a-z]+),ri=(\d+),rs=(.+),re=(.+),rc=(\d+)d(\d+)c/i;
//var regexpCust = /d=(.+),s=([1-4]),p=([a-z]+),i=([a-z]+)/i;
$orderSheet = $instructorSheet = $ruleSheet = $dateSheet = $orderSheet_tmp =[];
$lessonCnt = 0;
foreach ($order as $n => $lessonStr) {
    if(preg_match('/^x=(\d+),d=(.+),s=([1-4]),p=([a-z]+),i=([a-z0-9]+),e=(.+),ri=(.+),rs=(.+),re=(.+),rc=(\d+)d(\d+)c$/', $lessonStr, $lesson)){//_j($lesson);//條件開課
        $orderSheet[] = [
            'sidx'          => $lesson[1],
            'date'          => $lesson[2],
            'slot'          => $lesson[3],
            'park'          => $lesson[4],
            'instructor'    => $lesson[5],
            'expertise'     => $lesson[6],
            'ruleId'        => $lesson[7],
        ];
        $dateSheet[$lesson[2]] = $instructorSheet[$lesson[5]] = $ruleSheet[$lesson[6]] = 1;
        $lessonCnt += 1;

    }else if(preg_match('/^x=(\d+),d=(.+),s=([1-4]),p=([a-z]+),i=([a-z0-9]+),e=(.+)$/', $lessonStr, $lesson)){//_j($lesson);//固定開課
        $orderSheet[] = [
            'sidx'          => $lesson[1],
            'date'          => $lesson[2],
            'slot'          => $lesson[3],
            'park'          => $lesson[4],
            'instructor'    => $lesson[5],
            'expertise'     => $lesson[6],
            'ruleId'        => 0,
        ];
        $dateSheet[$lesson[2]] = $instructorSheet[$lesson[5]] = 1;
        $lessonCnt += 1;

    }else{
        echo 'Error!!!';
        exit();
    }
}

// sorting by class slot manually ====================
if(0){
  //print("<pre>".print_r($orderSheet,true)."</pre>");
  foreach ($orderSheet as $n => $o) { 
    $index = $o['slot'] - 1 ;
    $orderSheet_tmp[$index] = $orderSheet[$n];
  }
  ksort($orderSheet_tmp);
  //print("<pre>".print_r($orderSheet_tmp,true)."</pre>");
  $orderSheet = $orderSheet_tmp; //exit();
}
// =====================================
//print_r($orderSheet);exit();//_v($instructorSheet);_v($ruleSheet);_d($lessonCnt);exit();

if($_SESSION['type'] === 'instructor' || in_array($_SESSION['user_idx'], ['13361','8870','22009'])){
  $isAdmin = true;
}else{
  $isAdmin = false;
}

$instructorStr = '';
?>
<!DOCTYPE html>
  <html>
    <head>
      <?php require('pageHeader.php'); ?>
      <?php require('head.php'); ?>
      <style>
      ol li{
        font-size: 1rem;
        line-height: 2rem;
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


        <!--class table-->
         <div class="row container-xl">
           <div class="col s12 m10 col-centered">
              <!--<div class="row">
                <div class="col s12 space-2">
                  <h5>您己選擇 <?=$lessonCnt?> 堂課程</h5>
                  <p class="space-2">請確認您所選取的課堂資訊，並依各堂課程設定上課人數。若要增減課程項目您也可點選「重選課程」返回重選。</p>
                  <button class="btn btn-outline btn-outline-primary right" onclick="history.back();"><i class="material-icons">keyboard_arrow_left</i> 重選課程</button>
                </div>
              </div>-->
      <form action="payment.php" method="post" id="paymentForm">
      <input type="hidden" name="payment" id="paymentData">
      <input type="hidden" name="from" value="<?=$in['from']?>">

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
                <?php foreach ($orderSheet as $n => $o) { 
                        if($n==0) $query_date=$o['date'];
                        $instructorStr = $o['instructor'];
                ?>
                  <tr>
              
                    <td><p class="date"><?=substr($o['date'],5)?></p><?=$parkInfo[$o['park']]['timeslot'][$o['slot']]?><br><span class="badge badge-gray"><?=$o['slot']?>th</span></td>
                    <td><?=$parkInfo[$o['park']]['cname']?><br>
                      <div class="class">
                        <div class="class-d">
                          <div class="avatar-img">
                            <img src="https://diy.ski/photos/<?=$o['instructor']?>/<?=$o['instructor']?>.jpg" alt="">
                          </div>
                          <p><?=$instructorInfo[$o['instructor']]['cname']?></p>
                        </div>
                        <span class="badge badge-gray"><?=strtoupper($o['expertise'])?></span>
                      </div>
                    </td>
                    <td>
                      <select id="<?=$n?>" class="lesson" sidx="<?=$o['sidx']?>" date="<?=$o['date']?>" slot="<?=$o['slot']?>" park="<?=$o['park']?>" instructor="<?=$o['instructor']?>" expertise="<?=$o['expertise']?>" ruleId=<?=$o['ruleId']?>>
                        <?php for($i=1;$i<=$parkInfo[$o['park']]['maxStudentNum'];$i++){ ?>
                        <option value="<?=$i?>"><?=$i?></option>
                        <?php } ?>
                      </select>
                    </td>
                    <td class="right" style="display: flex"><div id="fee<?=$n?>"><p class="price" style="align-items:center;"><span class="badge badge-primary">JPY</span></p></div></td>
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
                                <div id="price"><p class="num col s8 m12"><small class="badge badge-primary">JPY</small></p></div>
                                
                              </div>
                              <div class="col s12 m3 payment-sum">
                                <p class="col s4 m12"><i class="material-icons">card_giftcard</i><br>折扣優惠</p>
                                <div id="discount"><p class="num col s8 m12"><small class="badge badge-primary">JPY</small></p></div>
                                
                              </div>
                              <div class="col s12 m3 payment-sum">
                                <p class="col s4 m12"><i class="material-icons">payment</i><br>預付訂金<br>刷卡金額<br><small id="exchangeRate" class="font-primary">(匯率)</small></p>                              
                                <div id="prepaid"><p class="num col s8 m12"><small class="badge badge-primary">NTD</small></p></div>
                                <p class="col s4 m12"></p>
                                <div id="paid" class="cR"><p class="num col s8 m12"><small class="badge badge-primary">JPY</small></p></div>
                                
                              </div>
                              <div class="col s12 m3 payment-sum">
                                <p class="col s4 m12"><i class="material-icons">attach_money</i><br>上課尾款</p>
                                <div id="payment"><p class="num col s8 m12"><small class="badge badge-primary">JPY</small></p></div>
                                
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="row  space-top-2">
                      <?php
                          if(0 && in_array($o['park'], ['gala','habuba','myoko'])){
                             $note = '請於說明事項中填寫上課雪場。若有小朋友上課，請務必註明人數與年紀，謝謝。';
                             $require_check = 1;
                          }else{
                             $note = '若有小朋友上課，請務必註明人數與年紀，謝謝。';
                             $require_check = 0;
                          }
                      ?>
                        <input type="hidden" name="require_check" id="require_check" value="<?=$require_check;?>">
                        <div class="input-field col s12 m12"><textarea name="requirement" id="requirement" class="materialize-textarea" placeholder="<?=$note?>"></textarea>
                        <label for="textarea1">說明事項</label></div>
                        <!--<div class="input-field col s12 m4"><input name="refer" placeholder="請輸入識別碼" type="text" class="validate"><label for="first_name">訂課識別碼</label></div>-->
                        <?php if($isAdmin){ $ro = in_array($_SESSION['user_idx'], ['2','3']) ? null : 'readonly'; ?>
                        <div class="input-field col s12"><input placeholder="輸入訂課連結失效分鐘，EX:5hr請輸入300" type="text" class="validate" name="timeout" value="2880" <?=$ro?>><label for="first_name">付款連結期限(分鐘)</label></div>
                        <?php } ?>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
              </form>
            </div>
            

            

            <div class="row space-top-2 center">
            <div class="row">
              <div class="col s12 center-align">
                <?php if($in['from']=='reservation'){ ?>
                  <small class="font-primary">
                    <i class="material-icons">info</i> 提醒您！由於申請開課的<?=ucfirst($instructorStr)?>教練不一定在當地，所以有可能您選定的教練不能上課，會另外安排其它教練，若都沒有教練將全額退費。<br></small>
                <?php } ?>
                <br>
                 <button class="btn btn-outline btn-outline-primary" onclick="window.location.replace('schedule.php?date=<?=$query_date?>');"><i class="material-icons">keyboard_arrow_left</i> 回前一頁</button>
                 <!--<button class="btn btn-primary modal-trigger" data-target="terms">確定付款 <i class="material-icons">keyboard_arrow_right</i></button>-->
                 <button class="btn btn-primary modal-trigger" id="sendBtn">確定付款 <i class="material-icons">keyboard_arrow_right</i></button>
              </div>
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
          <div class="row">
            <label>
              <input type="checkbox" id="read"/>
              <span class="font-primary">我已閱讀完畢</span>
            </label>
          </div>
          <button data-target="success-msg" class="waves-effect btn btn-primary align-center" id="paymentBtn">確認並付款 <i class="material-icons">navigate_next</i></button>
        </div>
      </div>
      

      <div id="pre_check" class="modal center">
        <div class="modal-content">
          <i class="material-icons">drafts</i>
          <h4>提醒您</h4>
          <div class="row space-top-2">
            <div class="input-field col s12 m8 col-centered">
              <p class="space-2">請於說明事項中填寫上課雪場!</p> 
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
      <!--<script src="assets/js/custom.js"></script>-->

      <script src="skidiy.data.php?v<?=uniqid()?>"></script>
      <script src="skidiy.func.php?v<?=uniqid()?>"></script>
      <script>
      function _d(d){console.log(d)}
      function _a(a){alert(a)}

      $(document).ready(function(){
        $('.sidenav').sidenav();
        $('.modal').modal();
        $('select').formSelect();

        $('.lesson').on('change', function(){
          calculateOrder(0);
        });
        calculateOrder(0);

        $('#sendBtn').on('click', function(){
            //_a($('#requirement').val());
            if($('#require_check').val() == 1 && $('#requirement').val()=='' ){
              $('#pre_check').modal('open');
            }else{
              $('#terms').modal('open');
            }
        });

        $('#paymentBtn').on('click', function(){
            if($('#read').prop('checked')){
              paymentForm.submit();
              $(this).hide();
            }else{
              _a('請勾選我已閱讀完畢，才可進行付款喔～');
            }
        });
      });
      </script>      


    </body>
  </html>