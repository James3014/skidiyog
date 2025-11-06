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
$instructors = $ko->getInstructorInfo(['type'=>'instructor','name'=>$loggedInstructor]);

$in['instructor'] = $loggedInstructor;
$calendar = $ko->getSchedulesbkInst($in);//_v($calendar);

$distinctParks = [];
foreach ($calendar as $date => $slots) {//_v($slots);
  foreach ($slots as $slot => $parks) {//_v($parks);
    foreach ($parks as $park => $lessons) {//_d($park);
      $distinctParks[$park] = $park;
    }
  }
}//_v($distinctParks);
$parkInfo = $ko->getParkInfo();//_v($parkInfo);
?>


  <!DOCTYPE html>
  <html>
    <head>
      <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/css/materialize.min.css">
      <link rel="stylesheet" href="https://ko.diy.ski/skidiy.css?v<?=rand(1,999999)?>">
      <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    </head>

    <body>
    <?php require('menu.php'); ?>

    <!--form-->
    <form action="schedules.php" method="post" id="calendar">
    <div class="row">
      <div class="input-field col s6">
        <input type="text" class="datepicker" name="date" value="<?=$in['date']?>">
        <label>選擇顯示日期</label>
      </div>
      <div class="input-field col s6">
        <select class="icons park" name="park" id="park">
          <option value="any">不限</option>
          <?php foreach ($distinctParks as $name => $park) { ?>
            <option value="<?=$name?>" data-icon="https://diy.ski/photos/<?=$name?>/<?=$name?>.jpg"><?=$parkInfo[$name]['cname']?></option>
          <?php } ?>
        </select>
        <label>選擇開課雪場</label>
      </div>
    </div>

    <div style="display:none;">
    <div class="row">
      <div class="input-field col s12">
        <select class="icons instructor" multiple name="instructor[]" id="instructor">
          <option value="<?=$loggedInstructor?>" selected="selected"><?=$loggedInstructor?></option>
        </select>
        <label>Step4. 選擇有開課的教練(可複選)</label>
      </div>
    </div>
    </div>

    </form>
    <!--form-->

    <div class="row" id="message">
      <div class="col s9">
        <div id="orderInfo"></div>
      </div>
    </div>

    <form action="booking.php" method="post" id="orderForm">
      <input type="hidden" name="order" id="order">
    </form>

      <?php require('calendar.php'); ?>

      <!--JavaScript at end of body for optimized loading-->
      <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/js/materialize.min.js"></script>
      <script src="https://ko.diy.ski/skidiy.func.php?v=<?=rand(1,999999)?>"></script>

      <script>
      $(document).ready(function(){
        $("[schedule]").each(function(){
          var schedule = $(this).attr('schedule');
          var regexp = /s=([1-4]),/gi;
          var slot = regexp.exec(schedule);
          if(slot == null){_a(schedule);}//異常
          var css = 'slot'+slot[1];
          $(this).addClass(css);
          
          var ruleRegexp = /ri=(\d+),/gi;
          var rid = ruleRegexp.exec(schedule);//_d(rid);
          if(rid != null){//_d(schedule);
            rid[1] = rid[1]%10;//_d(ruleColor[rid[1]]+'='+rid[1]);
            $(this).css('border-bottom', '3px solid ' + ruleColor[rid[1]]);
          }
        });

        $("[oidx]").each(function(){
          var oidx = $(this).attr('oidx');
          var schedule = $(this).attr('schedule');
          if(oidx!=0){
            $(this).css('background-color', '#999');
          }
        });

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

        $('[schedule]').on('click',function(){
          var oidx = $(this).attr('oidx');
          if(oidx!=0){
            $('#orderInfo')
            $('#orderInfo').html('待顯示訂單資訊 #'+oidx);
          }else{
            $('#orderInfo').html('待顯示開課資訊');
          }
        });

        $('select.park').on('change',function(){
          showLessons();
        });

        showLessons();
        //showSummary();
      });
      </script>
    </body>
  </html>
