<?php
Header('Location: https://booking.diy.ski');exit();
require('includes/sdk.php');
$filters = array(
    'date'        =>  FILTER_SANITIZE_STRING,
    'expertise'   =>  FILTER_SANITIZE_STRING,
    'park'        =>  FILTER_SANITIZE_STRING,
);//_v($_POST);

$in = filter_var_array(array_merge($_REQUEST,$_POST), $filters);//_v($in);//exit();
$in['date'] = empty($in['date']) ? date('Y-m-d', strtotime("+3 days")) : $in['date'];
//$in['date'] = empty($in['date']) ? '2022-11-15' : $in['date'];
$in['expertise'] = empty($in['expertise']) ? 'sb' : $in['expertise'];
$in['instructor'] = empty($in['instructor']) ? ['any'] : [$in['instructor']];
$in['park'] = empty($in['park']) ? 'any' : $in['park'];
if(!empty($_REQUEST['p'])) $in['park'] = $_REQUEST['p'];

if(!isset($_SESSION['user_idx'])){
  //_go('https://'.domain_name.'/account_login.php?from=SCH');
  $in['date']       = isset($in['date']) ? '' : $in['date'];
  $in['expertise']  = isset($in['expertise']) ? '' : $in['expertise'];
  $in['park']       = isset($in['park']) ? '' : $in['park'];
  _go('https://'.domain_name.'/account_login.php?from=SCH&date='.$in['date'].'&expertise='.$in['expertise'].'&park='.$in['park']);  
  //_go('https://'.domain_name.'/account_login.php?from=SCH'.htmlentities($_SERVER['PHP_SCRIPT']));  
}

$in['park'] = ($in['park']=='taipei') ? 'any' : $in['park'];


$ko = new ko();
$parks = $ko->getParkInfo();
$instructorInfo = $ko->getInstructorInfo(['type'=>'picker','expertise'=>$in['expertise']]);
$parkInfo = $ko->getParkInfo();//_v($parkInfo);

$calendar = $ko->getSchedules($in, $distinctParks);//_j($calendar);exit();
//$distinctParks = $ko->distinctParkName($calendar);
$distinctInstructors = $ko->distinctInstructorName($calendar);
?>
<!DOCTYPE html>
  <html>
    <head>
      <?php require('pageHeader.php'); ?>
      <?php require('head.php'); ?>
      <link rel="stylesheet" href="schedule.css?v180920<?//=rand(1,999999)?>">
      <style>
      b{color: red;}
      </style>
    </head>

    <body>
      <header>
        <?php require('nav.inc.php');?>
      </header>


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
          <form action="schedule.php" method="post" id="calendar">
          <div class="col s12 m6 offset-m1 pull-m3 header-block-content-w" style="z-index:88888888;">
            <div class="row space-top-1 row-margin-b0">
              
              <div class="col s11 col-centered" id="private">
                <div class="input-field col s6">
                  <label><span>Step 1</span> 選擇上課日期</label>
                  <input type="text" class="datepicker" name="date" value="<?=$in['date']?>">
                  
                </div>
                <div class="input-field col s6">
                  <select class="icons expertise" name="expertise">
                    <option value="sb" data-icon="" <?=($in['expertise']=='sb')?'selected':''?>>單板</option>
                    <option value="ski" data-icon=""<?=($in['expertise']=='ski')?'selected':''?>>雙板</option>
                  </select>
                  <label><span>Step 2</span> 選擇課程種類</label>
                </div>
                <div class="input-field col s6">
                  <select class="icons park" name="park" id="park">
                    <option value="any"><?=($in['park']=='any')?'不限':'🔙其它雪場'?></option>
                    <?php foreach ($distinctParks as $name => $park) { ?>
                      <option value="<?=$name?>" data-icon="https://diy.ski/photos/<?=$name?>/<?=$name?>.jpg"
                        <?=($in['park']==$name)?'selected':''?>><?=$parkInfo[$name]['cname']?></option>
                    <?php } ?>
                    <?php if($in['park']!='any' && sizeof($distinctParks)==0){ ?>
                      <option value="" selected>此日期<?=$parkInfo[$in['park']]['cname']?>無課程</option>
                    <?php } ?>
                  </select>
                  <label><span>Step 3</span> 選擇雪場</label>
                  <div style="font-size:0.8rem; line-height:0.8rem; color:red; padding:0rem; margin-top: -0.2rem;text-align:left;">
                    <a href="#" id="follow"
                       date="<?=$in['date']?>" expertise="<?=$in['expertise']?>" park="<?=$in['park']?>">💟以此條件追蹤開課通知.</a>
                  </div>
                </div>
                <div class="input-field col s6">
                  <select class="icons instructor" multiple name="instructor[]" id="instructor">
                    <option value="any" selected="selected">不限</option>
                    <?php foreach ($distinctInstructors as $name => $instructor) { 
                                  if(in_array($name,['phil','wawa','eden','tommy','sophia'])) continue;
                      ?>
                      <option value="<?=$name?>" data-icon="https://diy.ski/photos/<?=$name?>/<?=$name?>.jpg?v221121"
                        <?=(in_array($name, $in['instructor']))?'selected':''?>><?=$instructorInfo[$name]['cname']?></option>
                    <?php } ?>
                  </select>
                  <label><span>4</span> 選擇教練(可複選)</label>
                  <div style="font-size:0.8rem; line-height:0.8rem; color:red; padding:0rem; margin-top: -0.2rem;">＊請選擇才能顯示該教練的全部開課</div>
                </div>
              </div>
            </div>
          </div>
          </form>
          <!--form-->
        </div>

        <form action="booking.php" method="post" id="orderForm">
          <input type="hidden" name="order" id="order">
          <input type="hidden" name="from" value="booking">
        </form>

        <!--class table-->
         <div class="row space-top-1">
           <div class="col s12 m10 col-centered container-xl">
            <h5 class="hide-on-small-only">挑選課程</h5>
            <p class="hide-on-small-only space-2">請先由此點擊挑選您專屬的課程</p>
            <p class="center-align" ><a href="https://diy.ski/instructorList.php" target="_blank">👉點此看教練介紹～</a></p>
              <table class="class-table">
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
          if($park!='doraemon-x'){
          echo "\t\t\t
                <div schedule=\"x={$extraInfo['sidx']},d={$date},s={$slot},p={$park},i={$instructor},e={$extraInfo['expertise']}{$extraInfo['rule']}\">";
?>
                      <div class="class area-color-1"><!--色塊-->
                        <div class="class-m hide-on-med-and-up"><!--手機-->
                          <div class="avatar-img" style="background-image: url('https://diy.ski/photos/<?=$instructor?>/<?=$instructor?>.jpg?v221121');">
                            <p class="coach-name"><?=$parkInfo[$park]['abbr']?></p>
                            <div class="overlay"><i class="material-icons">check</i></div>
                          </div>
                        </div>

                        <div class="class-d hide-on-small-only"><!--桌機-->
                          <div class="avatar-img">
                            <div class="overlay"><i class="material-icons">check</i></div>
                            <img src="https://diy.ski/photos/<?=$instructor?>/<?=$instructor?>.jpg?v221121" alt="">
                          </div>
                          <p><?=$parkInfo[$park]['cname']?></p>
                        </div>
                      </div>
<?php
          echo  "</div>\n";
          } // end if park exception 
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

              <table>
                <tr>
                  <td class="center">
                    <button class="btn btn-primary" id="reservationBtn" style="font-size:0.8rem;">找不到合適課程? 試試向教練申請開課吧~</button>
                    <!--<p class="center" style="font-size:1rem; color:#619AEC;">找不到合適的課程嗎? <br>近期將開放申請開課功能，還請等候～</p>-->
                  </td>
                </tr>
              </table>

            </div>
         </div>
                 <div class="row count-block">
          <div class="col s12 m10 col-centered container-xl">
            <h5 class=" hide-on-small-only">己選課程</h5>
            <p class=" hide-on-small-only space-2">於課程選取完成後即可點選「確定訂課」。</p>
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
                    <small class="font-primary"> <i class="material-icons">info</i> 提醒您！可於下一步設定上課人數與觀看上課時間。</small>
                  </div>
                </div>
                <div class="col s12 m3">
                  <div id="bookingBtnDiv">
                    <button class="btn btn-primary right" id="bookingBtn"><i class="material-icons">payment</i> 確定訂課</button>
                  </div>
                </div>
              </div>
            </div>
          </div>          
        </div>
      </main>
      <div style="margin: 0px auto;">
      <button class="btn btn-outline btn-outline-primary" onclick="javascript: document.location.href='https://diy.ski';"><i class="material-icons">home</i> 回首頁</button>
      </div>
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
      <script src="skidiy.data.php?v=<?=rand(1,999999)?>"></script>
      <script src="skidiy.func.php?v=<?=rand(1,999999)?>"></script>
      <script>
      $(document).ready(function(){
        $('.class .material-icons').hide();
        $('#bookingBtnDiv').hide();

        $("[schedule]").each(function(){
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
        });

        var today = (+new Date()); //Date.now() milliseconds 微秒數        
        var act_date = new Date(today + (86400000 * 3));
        //alert(act_date);
        $('.sidenav').sidenav();
        $('.datepicker').datepicker({
          minDate: act_date,
          selectMonths: true, // Creates a dropdown to control month
          selectYears: 100, // Creates a dropdown of 15 years to control year
          format: 'yyyy-mm-dd',
          setDefaultDate: false,
          defaultDate: new Date(),
        });
        $('select').formSelect();
        $('.datepicker,.expertise,select.park').on('change', function(){
          $('#calendar').submit();
        });

        $('select.instructor').on('change',function(){
          //alert(checkRuleLessons());
          //_d('>>'+checkRuleLessons());
          if(!checkRuleLessons()){
            /*
            var schedule = $(this).attr('schedule');//_d(schedule);
            var lessonArr = regexpRule.exec(schedule);//_d(lessonArr);
            var lesson = arr2obj(lessonArr);//_d(lesson);
            alert(lesson.instructor);
            */
            alert('提醒您！必須先完成目前教練的選課，才可以繼續選取其他教練的課程唷～');
          }else{
            showLessons();
          }
        });
        $('#follow').on('click', follow);
        $('#bookingBtn').on('click', booking);
        $('#reservationBtn').on('click', function(){
          document.location.href='reservation.php?date=<?=$in['date']?>&expertise=<?=$in['expertise']?>&park=<?=$in['park']?>';
        });

        $('[schedule]').on('click',function(){//訂課與否
          var thisPark = getParkinSchecule($(this).attr('schedule'));
          if(!checkSamePark(thisPark)){//檢查是否訂課在同一雪場
            alert("同一次訂課只能選擇一個雪場喔～\n您目前訂課雪場為："+parkInfo[bookedPark]['cname']);
            return false;
          }

          $(this).toggleClass('booked');
          $(this).find('.material-icons').toggle();
          $(this).find('.class-m').toggleClass('class-m-active').find('.coach-name').toggle();
          $(this).find('.class-d').toggleClass('class-d-active');
          rulebookable = checkRuleLessons();
          showSummary();
        });

        showLessons();
        showSummary();
      });
      </script>

    </body>
  </html>