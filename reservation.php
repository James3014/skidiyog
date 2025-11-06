<?php
Header('Location: https://booking.diy.ski');exit();
require('includes/sdk.php');


$filters = array(
    'date'        =>  FILTER_SANITIZE_STRING,
    'expertise'   =>  FILTER_SANITIZE_STRING,
    'park'        =>  FILTER_SANITIZE_STRING,
);
$in = filter_var_array(array_merge($_REQUEST,$_POST), $filters);//_v($in);//exit();
// if(empty($in['date'])||empty($in['expertise'])){
//   Header('Location: schedule.php');
//   exit();
// }

$in['date'] = empty($in['date']) ? date('Y-m-d', strtotime("+3 days")) : $in['date'];
$in['expertise'] = empty($in['expertise']) ? 'sb' : $in['expertise'];
$in['park'] = empty($in['park']) ? 'any' : $in['park'];
$in['instructor'] = empty($_POST['instructor']) ? [] : $_POST['instructor'];

if(!isset($_SESSION['user_idx'])){
  //_go('https://'.domain_name.'/account_login.php');
  _go('https://'.domain_name.'/account_login.php?from=RESERV&date='.$in['date'].'&expertise='.$in['expertise'].'&park='.$in['park']);
}//_v($_REQUEST);exit();



$ko = new ko();
$parkInfo = $ko->getParkInfo();
$instructorInfo = $ko->getInstructorInfo(['type'=>'reservation']);//_j($instructorInfo);exit();
$calendar = $ko->getSchedulesResv($in, $instructorInfo, $distinctParks);//_j($calendar);exit();
$distinctParks = $ko->distinctParkName($calendar);//_v($distinctParks);exit();
$distinctInstructors = $ko->distinctInstructorName($calendar);

$limitDays = (in_array($_SESSION['user_idx'],[2,3,48])) ? 1 : 4;
?>
<!DOCTYPE html>
  <html>
    <head>
      <?php require('head.php'); ?>
      <!--swiper-->
      <link rel="stylesheet" href="schedule.css?v180920<?//=rand(1,999999)?>">
      <style>
      b{color: red;}
      </style>
    </head>

    <body>
      <header>
        <?php require('nav.inc.php');?>
      </header>

      <div id="loading"><!--loading begin-->
        <div class="row">
          <div class="s12">
            <div class="center"><span style="font-size: 1.6rem; color:#FFF;">🕓 教練課表下載中, 請稍候...</span></div>
          </div>
        </div>
      </div><!--loading end-->


      <main>
        <div class="container-fuild">
          <a href="javascript:" id="return-to-top" class="waves-effect waves-light"><i class="material-icons">arrow_upward</i></button></a>
          <div class="row header-block-class">
            <div class="header-img-bottom">
              <img src="assets/images/header_img_bottom.png" alt="">
            </div>
            <img src="assets/images/header_class_main_img.jpg">    
          </div> 

        <div class="row header-block-float">
          <div class="col m3 push-m8 hide-on-small-only">
            <img src="assets/images/class_booking_steps.png" class="steps-img">
          </div>
            <ul class="tabs col s12 m6 offset-m1 pull-m3">
              <li class="tab col s6"><a class="active" href="#"><i class="material-icons">stars</i> 私人課程</a></li>
              <li class="tab col s6"><a href="class_group_list.php"><i class="material-icons">supervised_user_circle</i> 團體課程</a></li>
            </ul>

          <!--form-->
          <form action="reservation.php" method="post" id="calendar">
          <div class="col s12 m6 offset-m1 pull-m3 header-block-content-w" style="z-index:88888888;">
            <div class="row space-top-1 row-margin-b0">
              
              <div class="col s11 col-centered" id="private">
                <div class="input-field col s6">
                  <label><span>Step 1</span> 上課日期</label>
                  <input type="text" class="datepicker" name="date" value="<?=$in['date']?>" readonly>
                  
                </div>
                <div class="input-field col s6">
                  <select class="icons expertise" name="expertise">
                    <option value="sb" data-icon="" <?=($in['expertise']=='sb')?'selected':''?>>單板</option>
                    <option value="ski" data-icon=""<?=($in['expertise']=='ski')?'selected':''?>>雙板</option>
                  </select>
                  <label><span>Step 2</span> 課程種類</label>
                </div>
                <div class="input-field col s6">
                  <select class="icons park" name="park" id="park">
                    <?php if(empty($distinctParks)){ ?>
                      <option value="">請換日期</option>
                    <?php } ?>
                    <option value="any"><?=($in['park']=='any')?'不限':'🔙其它雪場'?></option>
                    <?php foreach ($distinctParks as $name) { ?>
                      <option value="<?=$name?>" data-icon="https://diy.ski/photos/<?=$name?>/<?=$name?>.jpg"
                        <?=($in['park']==$name)?'selected':''?>><?=$parkInfo[$name]['cname']?></option>
                    <?php } ?>
                  </select>
                  <label><span>Step 3</span> 選擇雪場</label>
                </div>
                <div class="input-field col s6">
                  <select class="icons instructor" multiple name="instructor[]" id="instructor">
                    <option value="any" selected="selected">請選擇</option>
                    <?php foreach ($distinctInstructors as $name) { 

                      if(!$ko->instructorObsolete($name)){
                    ?>

                      <option value="<?=$name?>" data-icon="https://diy.ski/photos/<?=$name?>/<?=$name?>.jpg?v190919"
                        <?=(in_array($name, $in['instructor']))?'selected':''?>><?=$instructorInfo[$name]['cname']?></option>
                    <?php }} ?>
                  </select>
                  <label><span>4</span> 選擇教練(可複選)</label>
                </div>
              </div>
            </div>
          </div>
          </form>
          <!--form-->
        </div>

        <?php if(sizeof($distinctParks)==0){ ?>
          <div class="row">
            <div class="col s12">
              <p class="center-align">很抱歉～ 此日期無教練可開課。</p><br>
              <p class="center-align"><a href="schedule.php?date=<?=$in['date']?>&expertise=<?=$in['expertise']?>">返回預訂課程</a></p>
            </div>
          </div>
        <?php }else{ ?>

        <form action="booking.php" method="post" id="orderForm">
          <input type="hidden" name="order" id="order">
          <input type="hidden" name="from" value="reservation">
        </form>

        <!--schedule table start-->
         <div class="row">
           <div class="col s12 m10 col-centered container-xl">
            <h5 class="hide-on-small-only">挑選課程</h5>
            <p class="hide-on-small-only space-2">請先由此點擊挑選您專屬的課程</p>
            <p class="center-align" ><a href="https://diy.ski/instructorList.php" target="_blank">👉點此看教練介紹～</a></p>
              <table class="class-table">
                <!--<thead>
                  <tr>
                      <th>2018</th>
                      <th class="weekend">日</th>
                      <th>一</th>
                      <th>二</th>
                      <th>三</th>
                      <th>四</th>
                      <th>五</th>
                      <th class="weekend">六</th>
                  </tr>
                </thead>-->
                <tbody>
<?php 
$cnt = 1;
foreach ($calendar as $date => $s) {
  $y = date('Y', strtotime($date));
  $d = date('m/d', strtotime($date));
  $dd= date('N', strtotime($date));
  $today = ($date==$in['date']) ? 'selected-date' : null;
  
  if($cnt%7==1){
    echo "<tr>\n";
    echo "<td class=\"sub-title\">
          <div class=\"date\">{$y}<br>Date</div>
            <p>1<sub>st</sub></p>
            <p>2<sub>nd</sub></p>
            <p>3<sub>rd</sub></p>
            <p>4<sub>th</sub></p>
          </td>";
  }

  echo "\t<td>\n\t\t<div class=\"dateSchedule {$today}\">\n";
  echo "\t\t\t<div class=\"date\">{$d}<br>{$WD[$dd]}</div>\n";
    foreach ($s as $slot => $lesson) {
      foreach ($lesson as $park => $instructors) {
        foreach ($instructors as $instructor => $extraInfo) {
          echo "\t\t\t
                <div schedule=\"x={$extraInfo['sidx']},d={$date},s={$slot},p={$park},i={$instructor},e={$extraInfo['expertise']}{$extraInfo['rule']}\">";
?>
                      <div class="class area-color-1"><!--色塊-->
                        <div class="class-m hide-on-med-and-up"><!--手機-->
                          <div class="avatar-img" style="background-image: url('https://diy.ski/photos/<?=$instructor?>/<?=$instructor?>.jpg?v190919');">
                            <p class="coach-name"><?=$parkInfo[$park]['abbr']?></p>
                            <div class="overlay"><i class="material-icons">check</i></div>
                          </div>
                        </div>

                        <div class="class-d hide-on-small-only"><!--桌機-->
                          <div class="avatar-img">
                            <div class="overlay"><i class="material-icons">check</i></div>
                            <img src="https://diy.ski/photos/<?=$instructor?>/<?=$instructor?>.jpg?v190919" alt="">
                          </div>
                          <p><?=$parkInfo[$park]['cname']?></p>
                        </div>
                      </div>
<?php
          echo  "</div>\n";
        }//foreach instructor
      }//foreach park
    }//foreach slot
  echo "\t\t</div>\n\t</td>\n";

  $cnt++;
  if($cnt%7==1) echo "</tr>\n\n";
}//foreach date
?>
                </tbody>
              </table>

            </div>
         </div>
        <!--schedule table end-->

        <div class="row count-block">
          <div class="col s12 m10 col-centered container-xl">
            <h5 class=" hide-on-small-only">申請開課</h5>
            <p class=" hide-on-small-only space-2">於上方選取課程後即可以點選「申請開課」，建議訂四堂以上申請成功機會較高喔！</p>
            <div class="card-panel">
              <div class="row row-margin-0" style="height: 160px;">
                <div class="col s12">
                  <div class="col s4 m3 class-count">
                    <p>己選堂數</p>
                    <p class="num" id="classNum">0</p>
                  </div>
                  <div class="col s8 m9">
                    <p id="classMsg"></p>
                  </div>
                  <div class="col s12 m9">
                    <small class="font-primary"> <i class="material-icons">info</i> 提醒您！可於下一步結帳時設定上課人數與觀看上課時間。</small>
                  </div>
                </div>
                <div class="col s12 m3">
                  <div id="bookingBtnDiv">
                    <button class="btn btn-primary right" id="bookingBtn"><i class="material-icons">payment</i> 申請開課</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="row">
          <div class="col s12">
            <p class="center-align"><a href="schedule.php?date=<?=$in['date']?>&expertise=<?=$in['expertise']?>">🔙 返回預訂課程</a></p>
          </div>
        </div>
        <?php }//end of has distinctParks ?>
      </main>


      <footer>
        <div class="footer-copyright">
          <p class="center-align">© 2018 diy.ski</p>
        </div>
      </footer>


      
      <!--JavaScript at end of body for optimized loading-->
      <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-rc.2/js/materialize.min.js"></script>
      <script src="https://diy.ski/assets/js/select_workaround.js"></script>
      
      <!--custom js-->
      <!--<script src="assets/js/custom.js"></script>-->
      <script src="skidiy.func.php?v=<?=rand(1,999999)?>"></script>
      <script src="skidiy.data.php?v=<?=rand(1,999999)?>"></script>
      <script>
      $(document).ready(function(){
        var scheduleNum = $("[schedule]").length;_d(scheduleNum);
        $('.class .material-icons').hide();

        $("[schedule]").each(function(cnt){
          //套堂次位置
          var schedule = $(this).attr('schedule');
          var regexp = /s=([1-4]),/gi;
          var slot = regexp.exec(schedule);
          if(slot == null){_a(schedule);}//異常
          var css = 'slot'+slot[1];          
          $(this).addClass(css);
          //套條件開課顏色
          var ruleRegexp = /ri=(\d+),/gi;
          var rid = ruleRegexp.exec(schedule);//_d(rid);
          if(rid != null){//_d(schedule);
            rid[1] = rid[1]%10;//_d(ruleColor[rid[1]]+'='+rid[1]);
            //$(this).css('border-bottom', '3px solid ' + ruleColor[rid[1]]);
            $(this).addClass('area-color-'+rid[1]);
          }
          if(cnt+1===scheduleNum){
            $('#loading').hide();
          }
        });
        if($("[schedule]").length==0){
          $('#loading').hide();
        }

        $('.sidenav').sidenav();
        var today = (+new Date()); //Date.now() milliseconds 微秒數
        $('.datepicker').datepicker({
          minDate: new Date(today + (86400000 * <?=$limitDays?>)),
          selectMonths: true, // Creates a dropdown to control month
          selectYears: 100, // Creates a dropdown of 15 years to control year
          format: 'yyyy-mm-dd',
          setDefaultDate: true,
        });
        $('select').formSelect();
        $('.datepicker,.expertise,select.park').on('change', function(){
          $('#calendar').submit();
        });

        $('select.instructor').on('change',function(){//_d('park,instructor changed');
          showLessons();
        });

        $('#bookingBtn').on('click', booking);

        $('[schedule]').on('click',function(){//訂課與否
          var thisInstructor = getInstructorinSchecule($(this).attr('schedule'));
          if(!checkSameInstructor(thisInstructor)){//檢查是否訂課在同一雪場
            alert("只能向同一位教練申請開課喔～\n您目前選擇的教練是："+instructorInfo[reservedInstructor]['cname']);
            return false;
          }
          var thisPark = getParkinSchecule($(this).attr('schedule'));
          if(!checkSamePark(thisPark)){//檢查是否訂課在同一雪場
            alert("同一次訂課只能選擇一個雪場喔～\n您目前訂課雪場為："+parkInfo[bookedPark]['cname']);
            return false;
          }

          $(this).toggleClass('booked');
          $(this).find('.material-icons').toggle();
          $(this).find('.class-m').toggleClass('class-m-active').find('.coach-name').toggle();
          $(this).find('.class-d').toggleClass('class-d-active');
          showSummary();
        });

        showLessons();
        showSummary();
      });
      </script>

    </body>
  </html>