<?php
require('../includes/auth.php');
require('../includes/sdk.php');
$loggedInstructor = $_SESSION['SKIDIY']['instructor'];

$ko = new ko();
$instructor = $ko->getInstructorInfo(['type'=>'instructor', 'name'=>$loggedInstructor]);//_v($instructor);
$parkInfo = $ko->getparkInfo();//_v($parkInfo);
?>

<!DOCTYPE html>
<html>
    <head>
    <?php require('head.php'); ?>
    </head>
    <body>
    <?php require('menu.php'); ?>

    
    <?php if($instructor[$loggedInstructor]['jobType']=='support'){ ?>
      <blockquote>
      <span>暫不開放</span>
    </blockquote>
    <?php }else{ ?>

    <!--開課雪場-->
    <blockquote>
      <h5>1. 本季上課雪場</h5>
      <span>這些雪場將預設開放給學生申請開課。</span>
    </blockquote>
    <form id="lesson_set_form" action="lessonSet.php" method="post">
    <input type="hidden" name="action" value="park">
    <div class="row">
      <div class="input-field col s9">
        <select id="1_park" class="icons" multiple name="park[]">
          <?php foreach ($parkInfo as $name => $park) {
            $selected = empty($instructor[$loggedInstructor]['parks']) ? null : (in_array($name,$instructor[$loggedInstructor]['parks']) ? 'selected' : null);
          ?>
            <option value="<?=$name?>" data-icon="https://diy.ski/photos/<?=$name?>/<?=$name?>.jpg" <?=$selected?> ><?=$park['cname']?></option>
          <?php } ?>
        </select>
        <label>開課雪場</label>
      </div>
      <div class="input-field col s3">
        <button id="lesson_set" class="btn waves-effect waves-light" type="button">設定</button>
      </div>
    </div>
    </form>



    <!--指定開課-->
    <br>
    <blockquote>
      <h5>2. 指定開課</h5>
      <span>指定開課、停課、空堂日期。<br>開課為開放給學生訂課、停課為不允許任何排課，空堂為接受學生詢問開課。</span>
    </blockquote>
    <form id="a_lesson_create_form" action="lessonSet.php" method="post">
    <input type="hidden" name="action" value="fixed">
    <div class="row">
      <div class="input-field col s4">
        <input id="2_sdate" type="text" class="datepicker" name="start" value="">
        <label>起始日期</label>
      </div>
      <div class="input-field col s4">
        <input id="2_edate" type="text" class="datepicker" name="end" value="">
        <label>結束日期</label>
      </div>
      <div class="input-field col s4">
        <select id="2_park" class="icons" name="park">
        <?php foreach ($parkInfo as $name => $park) {
            if(!in_array($name, $instructor[$loggedInstructor]['parks'])) continue;
        ?>
            <option value="<?=$name?>"><?=$park['cname']?></option>
        <?php } ?>
        </select>
        <label>開課雪場</label>
      </div>
    </div>
    <div class="row">
      <div class="input-field col s3">
        <select id="2_class_slot" class="icons" name="slot[]" multiple>
          <option value="1">1st</option>
          <option value="2">2nd</option>
          <option value="3">3rd</option>
          <option value="4">4th</option>
        </select>
      </div>
      <div class="input-field col s3">
        <select id="2_exp" class="icons" name="expertise">
        <?php if(in_array($instructor[$loggedInstructor]['expertise'],['sb','both'])){ ?>
          <option value="sb">SB</option>
        <?php } ?>
        <?php if(in_array($instructor[$loggedInstructor]['expertise'],['ski','both'])){ ?>
          <option value="ski">SKI</option>
        <?php } ?>
        <?php if(in_array($instructor[$loggedInstructor]['expertise'],['both'])){ ?>
          <option value="both">不限</option>
        <?php } ?>
        </select>
      </div>
      <div class="input-field col s3">
        <select class="icons" name="type">
          <option value="enable">開課</option>
          <option value="disable">停課</option>
          <option value="empty">空堂</option>
        </select>
      </div>
      <div class="input-field col s3">
        <button id="a_lesson_create" class="btn waves-effect waves-light" type="button" >新增</button>
      </div>
    </div>
    </form>



    <!--條件開課-->
    <br>
    <blockquote>
      <h5>3. 條件開課</h5>
      <span>設定幾天內滿幾堂可被訂課</span>
    </blockquote>
    <form id="c_lesson_form" action="lessonSet.php" method="post">
    <input type="hidden" name="action" value="rule">
    <div class="row">
      <div class="input-field col s4">
        <input id="3_sdate" type="text" class="datepicker" name="start" value="">
        <label>起始日期</label>
      </div>
      <div class="input-field col s4">
        <input id="3_edate" type="text" class="datepicker" name="end" value="">
        <label>結束日期</label>
      </div>
      <div class="input-field col s4">
        <select id="3_park" class="icons park" name="park[]" multiple>
        <?php foreach ($parkInfo as $name => $park) {
            if(!in_array($name, $instructor[$loggedInstructor]['parks'])) continue;
        ?>
            <option value="<?=$name?>"><?=$park['cname']?></option>
          <?php } ?>
        </select>
        <label>雪場</label>
      </div>
    </div>
    <div class="row">
      <div class="input-field col s3">
        <input id="c_days" type="text" name="days" value="">
        <label>連續幾天</label>
      </div>
      <div class="input-field col s3">
        <input id="class_cnt" type="text" name="lessons" value="">
        <label>至少幾堂</label>
      </div>
      <div class="input-field col s3">
        <select id="3_exp" class="icons" name="expertise">
        <?php if(in_array($instructor[$loggedInstructor]['expertise'],['sb','both'])){ ?>
          <option value="sb">SB</option>
        <?php } ?>
        <?php if(in_array($instructor[$loggedInstructor]['expertise'],['ski','both'])){ ?>
          <option value="ski">SKI</option>
        <?php } ?>
        <?php if(in_array($instructor[$loggedInstructor]['expertise'],['both'])){ ?>
          <option value="both">不限</option>
        <?php } ?>
        </select>
      </div>
      <div class="input-field col s3">
        <button id="c_lesson_create" class="btn waves-effect waves-light right-align" type="button">新增</button>
      </div>
    </div>
    </form>



    <!--團體開課-->
    <br>
    <blockquote>
      <h5>4. 團體開課</h5>
      <span>指定日期開團體課程</span>
    </blockquote>
    <form id="g_lesson_form" action="lessonSet.php" method="post">
    <input type="hidden" name="action" value="group">
    <div class="row">
      <div class="input-field col s4">
        <input id="4_sdate" type="text" class="datepicker" name="start" value="">
        <label>起始日期</label>
      </div>
      <div class="input-field col s4">
        <input id="4_edate" type="text" class="datepicker" name="end" value="">
        <label>結束日期</label>
      </div>
      <div class="input-field col s4">
        <select id="4_park" class="icons park" name="park" id="park">
        <?php foreach ($parkInfo as $name => $park) {
            if(!in_array($name, $instructor[$loggedInstructor]['parks'])) continue;
        ?>
            <option value="<?=$name?>"><?=$park['cname']?></option>
          <?php } ?>
        </select>
        <label>雪場</label>
        </select>
      </div>
    </div>
    <div class="row">
      <div class="input-field col s4">
        <input id="man_min" type="text" name="min" value="2">
        <label>人數下限</label>
      </div>
      <div class="input-field col s4">
        <input id="man_max" type="text" name="max" value="6">
        <label>人數上限</label>
      </div>
      <div class="input-field col s4">
        <select id="4_exp" name="expertise">
        <?php if(in_array($instructor[$loggedInstructor]['expertise'],['sb','both'])){ ?>
          <option value="sb">SB</option>
        <?php } ?>
        <?php if(in_array($instructor[$loggedInstructor]['expertise'],['ski','both'])){ ?>
          <option value="ski">SKI</option>
        <?php } ?>
        </select>
        <label>SB/SKI</label>
      </div>
    </div>

    <div class="row">
      <div class="input-field col s4">
        <input id="fee" type="text" name="fee" value="">
        <label>＄學費</label>
      </div>
      <div class="input-field col s4">
        <input id="prepaid" type="text" name="prepaid" value="">
        <label>＄訂金</label>
      </div>
      <div class="input-field col s4">
        <select id="currency" name="currency">
          <option value="JPY">日幣 JPY</option>
          <option value="NTD">台幣 NTD</option>
          <option value="AUD">澳幣 AUD</option>
        </select>
        <label>幣別</label>
      </div>
    </div>

    <div class="row">
      <div class="input-field col s12">
        <input id="lesson_title" type="text" name="title" value="">
        <label>課程標題</label>
      </div>
      <div class="input-field col s9">
        <textarea id="lesson_memo" class="materialize-textarea" name="content"></textarea>
        <label>課程說明</label>
      </div>
      <div class="input-field col s3">
        <button id="g_lesson_create" class="btn waves-effect waves-light" type="button">新增</button>
      </div>
    </div>
    </form>

    <?php }//not support ?>
    
      <?php require('foot.php'); ?>
      <script>
      $(document).ready(function(){
        $('.sidenav').sidenav();
        $('select').formSelect();
        $('.datepicker').datepicker({
          selectMonths: true, // Creates a dropdown to control month
          selectYears: 1, // Creates a dropdown of 15 years to control year
          format: 'yyyy-mm-dd'
        });

        $('#lesson_set').on('click', function(){
          if( $('#1_park').val().length ===0 ){
               alert('尚未設定開課雪場！');  
          }else{
              $('#lesson_set_form').submit();
          }           
        });

        $('#a_lesson_create').on('click', function(){          
          //alert($('#2_class_slot').val());
          if( !$('#2_sdate').val() || !$('#2_edate').val() || !$('#2_park').val() || $('#2_class_slot').val().length ===0 || !$('#2_exp').val() ){
            alert('指定開課： 資料填寫不完整！');  
          }else{
            var startDate = $('#2_sdate').val().replace(/-/g,'/');
            var endDate = $('#2_edate').val().replace(/-/g,'/');

            if(startDate > endDate){
               alert('指定開課： 起始日期 > 結束日期！');  
            }else{
              $('#a_lesson_create_form').submit();
            }            
          }
        });     

        $('#c_lesson_create').on('click', function(){          
          //alert($('#2_class_slot').val());
          if( !$('#3_sdate').val() || !$('#3_edate').val() || !$('#c_days').val() || !$('#class_cnt').val() || $('#3_park').val().length ===0  || $('#3_exp').val().length ===0  ){
            alert('條件開課： 資料填寫不完整！');  
          }else{
            var startDate = $('#3_sdate').val().replace(/-/g,'/');
            var endDate = $('#3_edate').val().replace(/-/g,'/');

            if( $('#class_cnt').val() > ($('#c_days').val() * 4) ){
               alert('總開課堂數大於設定天數！！');
            }else if(startDate > endDate){
               alert('條件開課： 起始日期 > 結束日期！');  
            }else{
              $('#c_lesson_form').submit();
            }            
          }
        }); 

        $('#g_lesson_create').on('click', function(){          
          //alert($('#2_class_slot').val());
          if( !$('#4_sdate').val() || !$('#4_edate').val() || !$('#man_min').val() || !$('#man_max').val() || !$('#fee').val() || !$('#prepaid').val() || !$('#currency').val() || !$('#lesson_memo').val() || !$('#lesson_title').val() || $('#4_park').val().length ===0  || $('#4_exp').val().length ===0  ){
            alert('團體開課： 資料填寫不完整！');  
          }else{
            var startDate = $('#4_sdate').val().replace(/-/g,'/');
            var endDate   = $('#4_edate').val().replace(/-/g,'/');

            if(startDate > endDate){
               alert('團體開課： 起始日期 > 結束日期！');  
            }else{
              $('#g_lesson_form').submit();
            }            
          }
        });         

           
        

      });
      <?php
      if(isset($_REQUEST['msg'])){
        if(isset($SYSMSG[$_REQUEST['msg']])){
          echo "alert('{$SYSMSG[$_REQUEST['msg']]}');";
        }
      }
      ?>
      </script>
    </body>
</html>
