<?php
require('../includes/auth.php');
require('../includes/sdk.php');

$filters = array(
    'date'        =>  FILTER_SANITIZE_STRING,
);//_v($_POST);
$in = filter_var_array(array_merge($_REQUEST,$_POST), $filters);//_v($in);//exit();
$in['date'] = empty($in['date']) ? date('Y-m-d') : $in['date'];
$ko = new ko();

$loggedInstructor = $_SESSION['SKIDIY']['instructor'];
$instructor = $ko->getInstructorInfo(['type'=>'instructor','name'=>$loggedInstructor]);

$in['instructor'] = $loggedInstructor;
$calendar = $ko->getSchedulesbkInst($in);//_v($calendar);exit();

$distinctParks = $ko->distinctParkName($calendar);
$parkInfo = $ko->getParkInfo();//_v($parkInfo);
?>
<!DOCTYPE html>
  <html>
    <head>
      <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover, user-scalable=false"/>
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-rc.2/css/materialize.min.css">
      <link rel="stylesheet" href="assets/css/custom.min.css?v180819a">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
      <link rel="stylesheet" href="schedule.css?v<?=rand(1,999999)?>">
    </head>

    <body>
    <?php require('menu.php'); ?>


      <main>
        <br>
        <!--form-->
        <form action="schedules.php" method="post" id="calendar">
        <div class="row" style="margin-bottom:0px;">
          <div class="col s6 input-field" style="margin-bottom:0px;">
            <label>開課日期</label>
            <input type="text" class="datepicker" name="date" value="<?=$in['date']?>">
          </div>
          <div class="col s6 input-field" style="margin-bottom:0px;">
            <select class="icons park" name="park" id="park">
              <option value="any">不限</option>
                <?php foreach ($distinctParks as $name => $park) {
                  if(empty($park)) continue;//停課disable, 雪場會是空的
                ?>
                <option value="<?=$name?>" data-icon="https://diy.ski/photos/<?=$name?>/<?=$name?>__s.jpg"><?=$parkInfo[$name]['cname']?></option>
              <?php } ?>
            </select>
            <label>開課雪場</label>
          </div>

          <div style="display:none;">
            <div class="row">
              <div class="input-field col s12">
                <select class="icons instructor" multiple name="instructor[]" id="instructor">
                  <option value="<?=$loggedInstructor?>" selected="selected"><?=$loggedInstructor?></option>
                </select>
              </div>
            </div>
          </div>
        </div>
        </form>

        <!--class table-->
         <div class="row">
           <div class="col s12 m10 col-centered">
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
            <p>1st</p>
            <p>2st</p>
            <p>3st</p>
            <p>4st</p>
          </td>";
  }

  echo "\t<td>\n\t\t<div class=\"dateSchedule {$today}\">\n";
  echo "\t\t\t<div class=\"date\">{$d}<br>{$WD[$dd]}</div>\n";
    foreach ($s as $slot => $lesson) {
      foreach ($lesson as $park => $instructors) {
        foreach ($instructors as $instructor => $extraInfo) {
            if($extraInfo['expertise']=='ski')    {$exp='s';   $exp_d = $extraInfo['expertise'];}
            if($extraInfo['expertise']=='sb')     {$exp='b';   $exp_d = $extraInfo['expertise'];}
            if($extraInfo['expertise']=='both')   {$exp='o';   $exp_d = $extraInfo['expertise'];}


          echo "\t\t\t
                <div oidx=\"{$extraInfo['oidx']}\" gidx=\"{$extraInfo['gidx']}\"
                  schedule=\"x={$extraInfo['sidx']},d={$date},s={$slot},p={$park},i={$instructor},e={$extraInfo['expertise']}{$extraInfo['rule']}\">";
?>
                      <div class="class area-color-1" ><!--色塊-->
                        
                        <div sch_oidx_m="<?=$extraInfo['oidx']?>" sch_gidx_m="<?=$extraInfo['gidx']?>" class="class-m hide-on-med-and-up" style="background: #000000"><!--手機-->
                          <?php if($extraInfo['expertise']==='disable'){ ?>
                            <div class="avatar-img" style="background-image: url('https://diy.ski/photos/disabled.jpg');">
                              <p class="coach-name"> </p>
                            </div>
                            <img src="" alt="">
                          <?php }else{ ?>
                            <!--<div class="avatar-img" style="background-image: url('https://diy.ski/photos/black.jpg');">-->
                              <div class="avatar-img" >
                              <p class="coach-name"><?=mb_substr($parkInfo[$park]['cname'],0,1,"UTF-8")?><?=$exp?></p>
                              <div class="overlay"><i class="material-icons">check</i></div>
                            </div>
                          <?php } ?>
                        </div>

                        <div sch_oidx_d="<?=$extraInfo['oidx']?>" sch_gidx_d="<?=$extraInfo['gidx']?>" class="class-d hide-on-small-only" ><!--桌機-->
                          <?php if($extraInfo['expertise']==='disable'){ ?>
                            <div class="avatar-img">
                              <img src="https://diy.ski/photos/disabled.jpg" alt="">
                            </div>
                            <p class="coach-name">已停課</p>
                          <?php }else{ ?>
                            <div class="avatar-img">
                              <div class="overlay"><i class="material-icons">check</i></div>
                              <img src="https://diy.ski/photos/<?=$park?>/<?=$park?>__s.jpg" alt="">
                            </div>
                            <p><?=$parkInfo[$park]['cname'].' '.strtoupper($exp_d);?></p>
                          <?php } ?>
                        </div>

                      </div><!--色塊-->
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

         <div class="row count-block">
          <div class="col s12 m10 col-centered container-xl">
            <div class="card-panel">
              <div class="row row-margin-0">
                <div class="col s12">
                  <div id="orderInfo"><h6>開課資訊</h6></div>
                  <form action="lessonSet.php" method="post" id="lessonFrm">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="type" value="" id="setType">
                  <input type="hidden" name="idx" value="" id="setIdx">
                  <input type="hidden" name="date" value="<?=$in['date']?>">
                  </form>
                </div>
                <div class="col s12 m3">
                  <button class="btn btn-primary right" id="deleteBtn" style="display:none;"><i class="material-icons">delete</i> 刪除</button>
                </div>
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

      
      <!--JavaScript at end of body for optimized loading-->
      <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-rc.2/js/materialize.min.js"></script>
      <script src="bkInst.func.php?v<?=rand(0,99999)?>"></script>
      <script>
      $(document).ready(function(){
        // 桌機s
        $("[sch_oidx_d]").each(function(){        
          //套已訂課樣式
          if($(this).attr('sch_oidx_d')!=0){            
            $(this).css('background','#faa');
          }
          if($(this).attr('sch_oidx_d')==0 && $(this).attr('sch_gidx_d')!=0){
            $(this).css('background','#5ab2ec');
          }
        });
        // 手機
        $("[sch_oidx_m]").each(function(){        
          //套已訂課樣式
          if($(this).attr('sch_oidx_m')!=0){            
            $(this).css('background','#faa');
          }
          if($(this).attr('sch_oidx_m')==0 && $(this).attr('sch_gidx_m')!=0){
            $(this).css('background','#5ab2ec');
          }
        });        

        $("[schedule]").each(function(){
          //套堂次位置
          var schedule = $(this).attr('schedule');
          var regexp = /s=([1-4]),/gi;
          var slot = regexp.exec(schedule);
          if(slot == null){_a(schedule);}//異常
          var css = 'slot'+slot[1];
          $(this).addClass(css);//_d(schedule+'='+css);
          //套條件開課顏色
          var ruleRegexp = /ri=(\d+),/gi;
          var rid = ruleRegexp.exec(schedule);//_d(rid);
          if(rid != null){_d(schedule);
            rid[1] = rid[1]%10;//_d(ruleColor[rid[1]]+'='+rid[1]);
            //$(this).css('border-bottom', '3px solid ' + ruleColor[rid[1]]);
            $(this).addClass('area-color-'+rid[1]);
          }
          //套已訂課樣式
          if($(this).attr('oidx')!=0){
            //$(this).css('border-bottom','2px solid #F00');
          }
          if($(this).attr('oidx')==0 && $(this).attr('gidx')!=0){
            //$(this).css('border-bottom','2px solid #00F');
          }
        });//each schedule


      });//ready

      $('.sidenav').sidenav();
      $('.datepicker').datepicker({
          selectMonths: true, // Creates a dropdown to control month
          selectYears: 100, // Creates a dropdown of 15 years to control year
          format: 'yyyy-mm-dd'
      });
      $('select').formSelect();
      $('.datepicker').on('change', function(){
          $('#calendar').submit();
      });

      $('select.park').on('change',function(){
        $('#orderInfo').html('開課資訊');
        showPark();
      });

      $('[schedule]').on('click',function(e){
        e.preventDefault();
        var oidx = $(this).attr('oidx');
        var gidx = $(this).attr('gidx');
        var schedule = $(this).attr('schedule');_d(schedule);
        var lesson = lessonArr = null;
        var type = msg = '';

        if(regexpDisable.exec(schedule)){
          type = 'disable';
          lessonArr = regexpDisable.exec(schedule);
        }else if(regexpRule.exec(schedule)!=null){
          type = 'rule';
          lessonArr = regexpRule.exec(schedule);
          if(gidx!=0){
            type = 'group';
          }
        }else if(regexpCust.exec(schedule)!=null){//_a(regexpRule);
          type = 'fixed';
          lessonArr = regexpCust.exec(schedule);
        }else{
          _a(schedule+"\n錯誤！！");
        }//_d(type);_d(lessonArr);
        lesson = arr2obj(lessonArr);//_d(lesson);return;

        if(oidx!=0){
          $('#orderInfo').html('待顯示訂單資訊 #'+oidx);
          $('#deleteBtn').hide();
          var orderInfo = getOrderInfo(oidx);
          return true;
        }//oidx

        switch(type){
          case 'fixed':
            msg = '<h6>指定開課<h6>' + lesson['date'] + '第' +lesson['slot'] + '堂，在 ' + lesson['park'] + '，' 
                  + lesson['expertise'] + '課程。';
            $('#setType').val('fixed');
            $('#setIdx').val(lesson.sidx);
            break;
          case 'rule':
            msg = '<h6>條件開課<h6>' + lesson['ruleStart'] + '～' + lesson['ruleEnd'] + ' ' + lesson['expertise'] + ' 課程。<br>' 
                    + '在 ' + lesson['park'] + '，' + lesson['ruleDay'] + '天需選' + lesson['ruleCnt'] + '堂課。';
            $('#setType').val('rule');
            $('#setIdx').val(lesson.ruleIdx);
            break;
          case 'group':
            msg = '<h6>團體開課<h6>' + lesson['ruleStart'] + '～' + lesson['ruleEnd'] + '<br>' 
                    + '在 ' + lesson['park'] + '，' + lesson['ruleDay'] + '天。';
            $('#setType').val('group');
            $('#setIdx').val(lesson.ruleIdx);
            break;
          case 'disable':
            msg = '<h6>該堂已停課<h6>';
            $('#setType').val('disable');
            $('#setIdx').val(lesson.sidx);
            break;
        }//switch
        $('#orderInfo').html(msg);
        $('#deleteBtn').show();
      });

      $('#deleteBtn').on('click', function(){
        $('#lessonFrm').submit();
      });

      <?php
      if(isset($_REQUEST['msg'])){
        if(isset($SYSMSG[$_REQUEST['msg']])){
          echo "alert('{$SYSMSG[$_REQUEST['msg']]}');";
        }
      }
      ?>
      // $('.class .material-icons').hide();
      // $('.class').click(function () {
      //   $(this).find('.class-m').toggleClass('class-m-active').find('.coach-name').toggle();
      //   $(this).find('.class-d').toggleClass('class-d-active');
      //   $(this).find('.material-icons').toggle();
      //   return false;
      // });
      </script>

    </body>
  </html>