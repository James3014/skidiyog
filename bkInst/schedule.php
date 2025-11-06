<?php
//require('../includes/auth.php');
require('../includes/sdk.php');
require('../includes/cauth.php'); // cookie AUTH
$filters = array(
    'date'        =>  FILTER_SANITIZE_STRING,
    'week'        =>  FILTER_SANITIZE_STRING,
    'cdate'       =>  FILTER_SANITIZE_STRING, // shift date from
    'sdate'       =>  FILTER_SANITIZE_STRING, // selected date
    'asInstructor'=>  FILTER_SANITIZE_STRING, // for admin
);//_v($_POST);
$in = filter_var_array(array_merge($_REQUEST,$_POST), $filters);//_v($in);//exit();
$in['date'] = empty($in['date']) ? date('Y-m-d') : $in['date'];
$in['date'] = empty($in['sdate']) ? $in['date'] : $in['sdate']; // 有選取日期時，保存該日期

$ko = new ko();

if(in_array($_SESSION['SKIDIY']['instructor'], ['ko','james','jeter'])){//管理者可切換教練
  $loggedInstructor = empty($in['asInstructor']) ? $_SESSION['SKIDIY']['instructor'] : $in['asInstructor'];
  $allInstructors = $ko->getInstructorInfo();
}else{
  $loggedInstructor = $_SESSION['SKIDIY']['instructor'];  
}

$instructor = $ko->getInstructorInfo(['type'=>'instructor','name'=>$loggedInstructor]);
if(empty($instructor)){
  echo 'Not available!';
  unset($_SESSION['SKIDIY']);
  session_destroy();
  exit();
}

$in['instructor'] = $loggedInstructor;//_v($in);
$calendar = $ko->getSchedulesbkInst($in);//_v($calendar);//exit();

$distinctParks = $ko->distinctParkName($calendar);
$parkInfo = $ko->getParkInfo();//_v($parkInfo);

// *********************************** //
// Define at scheduleSet.php
if(isset($_SESSION['INPUT_CACHE']['Schedule_fixed'])){
    //echo 'slot:'.$_SESSION['INPUT_CACHE']['Schedule_fixed']['slot'] ;
}

$tmpAccess = ['jeter','james','amber','joy','tong',
              'liaooo','falco','fenris','always','john',
              'peiwen'];//暫時性限制
?>

<!DOCTYPE html>
<html>
    <head>
    <?php require('head.php'); ?>

    <style>
    .input-field>label, .input-field label.active{
      font-size: 1rem;
    }
    .btn{
      padding-left: 1rem !important;
      padding-right: 1rem !important;
      border-radius: 6px;
    }
    cR{color: red;}
    cB{color: blue;}
    </style>
    </head>
    <body>
    

    <?php if(!in_array($loggedInstructor, $INST2223)){ ?>
      <blockquote>
      <span>暫不開放</span>
    </blockquote>
    <?php }else{ ?>
      <main>
        <!-- workaroumd for the css/rwd issue -->
        <?php require('menu.php'); ?> 
        <h4>🏂  教練雪季排程</h4>
        <!--form-->
        <form action="schedule.php" method="post" id="calendar">
        <div class="row" style="margin-bottom:0px;">
          
          <?php if(in_array($_SESSION['SKIDIY']['instructor'], ['ko','james','jeter'])){ ?>
          <div class="col s12 input-field" style="margin-bottom:0px;">
            <select class="icons" name="asInstructor" id="asInstructor">
              <option value="">--- 請選擇 ---</option>
              <?php foreach($allInstructors as $optInst){
                $selected = ($optInst['name']==$in['asInstructor']) ? 'selected' : '';
                //if(!in_array($optInst['name'], ['phil','wawa','eden','tommy','sophia','funseeker1','funseeker2'])){
                if(!$ko->instructorObsolete($optInst['name'])){
              ?>
                <option value="<?=$optInst['name']?>" <?=$selected?>><?=$optInst['name']?></option>
              <?php
                } // end of if
               } 
              ?>
            </select>
            <label>切換教練</label>
          </div>
          <?php } ?>

          <div class="col s6 input-field" style="margin-bottom:0px;">
            <label>顯示排程日期</label>
            <input type="text" class="datepicker" id="thisDate" name="date" value="<?=$in['date']?>">
          </div>
          <div class="col s6 input-field" style="margin-bottom:0px;">
            <select class="icons park" name="park" id="park">
              <option value="any">不限</option>
                <?php foreach ($distinctParks as $name => $park) {
                  if(empty($park)) continue;//停課disable, 雪場會是空的
                ?>
                <option value="<?=$name?>" data-icon="https://diy.ski/photos/<?=$name?>/<?=$name?>__s.jpg"><?=$parkInfo[$name]['cname']?></option>
              <?php } ?>
              <option value="group">團體課</option>
            </select>
            <label>顯示排課雪場</label>
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
  $mj = $cnt%7;
  if($cnt <= 7){ // first week
    $label_privous_week = "";
    $asInstLink = isset($allInstructors) ? ('&asInstructor='.$loggedInstructor) : '';
    if($cnt%7==1) {
      $label_privous_week = '<a href="?week=p&sdate='.$in['date'].'&cdate='.$date.$asInstLink.'"> << </a>';
      $start_date = $date;
    }  
    $label_next_week = "";
    if($cnt%7==0) $label_next_week    = '<a href="?week=n&sdate='.$in['date'].'&cdate='.$start_date.$asInstLink.'"> >> </a>'; 
  }else{
    $label_next_week = "";
    $label_privous_week = "";
  }

  echo "\t<td>\n\t\t<div class=\"dateSchedule {$today}\">\n";
  echo "\t\t\t<div class=\"date\">{$d}<br>{$label_privous_week} {$WD[$dd]} {$label_next_week}</div>\n";
    foreach ($s as $slot => $lesson) {
      foreach ($lesson as $park => $instructors) {
        foreach ($instructors as $inst => $extraInfo) {
            if($extraInfo['expertise']=='ski')    {$exp='s';   $exp_d = $extraInfo['expertise'];}
            if($extraInfo['expertise']=='sb')     {$exp='b';   $exp_d = $extraInfo['expertise'];}
            if($extraInfo['expertise']=='both')   {$exp='o';   $exp_d = $extraInfo['expertise'];}


          echo "\t\t\t
                <div oidx=\"{$extraInfo['oidx']}\" gidx=\"{$extraInfo['gidx']}\" studentNum=\"{$extraInfo['studentNum']}\" 
                  schedule=\"x={$extraInfo['sidx']},d={$date},s={$slot},p={$park},i={$inst},e={$extraInfo['expertise']}{$extraInfo['rule']}\">";
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
                              <p class="coach-name"><?=($park=='nakazato')?'中':$parkInfo[$park]['abbr']?><?=$exp?></p>
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
                            <p><?=$parkInfo[$park]['cname'].' '.strtoupper($exp_d);?><br>&#8986;<?=isset($parkInfo[$park]['timeslot'][$slot])?$parkInfo[$park]['timeslot'][$slot]:'整天'?></p>
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
         <!--class table-->

         <!--message table-->
         <div class="row count-block">
          <div class="col s12 m10 col-centered container-xl">
            <div class="card-panel">
              <div class="row row-margin-0">
                <div class="col s12 m12 l12">
                  <div id="orderInfo"><h6>課程資訊</h6></div>
                  <form action="scheduleSet.php" method="post" id="lessonFrm">
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
        <!--message table-->



    <?php if($loggedInstructor==$_SESSION['SKIDIY']['instructor']){//避免Admin設定錯人. ?>

    <!--開課雪場-->
    <h4>🏂 <?=ucfirst($instructor[$loggedInstructor]['name'])?> 教練開課設定</h4>
    <blockquote>
      <h5>1. 本季上課雪場</h5>
      <span>這些雪場將預設開放給學生申請開課。</span>
    </blockquote>
    <form id="lesson_set_form" action="scheduleSet.php" method="post">
    <input type="hidden" name="action" value="park">
    <input type="hidden" name="date" value="<?=$in['date']?>">
    <div class="row">
      <div class="input-field col s9">
        <select id="1_park" class="icons" multiple name="park[]" <?=((in_array($instructor[$loggedInstructor]['name'], $PARKLIMITED)||in_array($instructor[$loggedInstructor]['name'], $PARK_Hokkaido_LIMITED)/*||$instructor[$loggedInstructor]['jobType']!='fulltime'*/) ? 'disabled' : null)?>>
          <?php foreach ($parkInfo as $name => $park) {
            if(in_array($name,['angelgrandia','niseko'])) continue;
            $selected = empty($instructor[$loggedInstructor]['parks']) ? null : (in_array($name,$instructor[$loggedInstructor]['parks']) ? 'selected' : null);
          ?>
            <option value="<?=$name?>" data-icon="https://diy.ski/photos/<?=$name?>/<?=$name?>.jpg" <?=$selected?> ><?=$park['cname']?> <?=ucfirst($park['name'])?></option>
          <?php } ?>
        </select>
        <label>開課雪場</label>
      </div>
      <div class="input-field col s3">
      <?php if(! (in_array($instructor[$loggedInstructor]['name'], $PARKLIMITED)||in_array($instructor[$loggedInstructor]['name'], $PARK_Hokkaido_LIMITED)/*||$instructor[$loggedInstructor]['jobType']!='fulltime'*/) ){ ?>
        <button id="lesson_set" class="btn waves-effect waves-light" type="button">設定</button>
      <?php } ?>
      </div>
    </div>
    </form>



    <!--指定開課-->
    <br>
    <blockquote>
      <h5>2. 指定開課</h5>
      <span>指定開課、停課、空堂日期。<br>開課為開放給學生訂課、停課為不允許任何排課，空堂為接受學生詢問開課。</span>
    </blockquote>
    <form id="a_lesson_create_form" action="scheduleSet.php" method="post">
    <input type="hidden" name="action" value="fixed">
    <input type="hidden" name="date" value="<?=$in['date']?>">
    <div class="row">
      <div class="input-field col s4">
        <input id="2_sdate" type="text" class="datepicker" name="start" value="<?=$in['date']?>">
        <label>起始日期</label>
      </div>
      <div class="input-field col s4">
        <input id="2_edate" type="text" class="datepicker" name="end" value="<?=$in['date']?>">
        <label>結束日期</label>
      </div>
      <div class="input-field col s4">
        <select id="2_park" class="icons" name="park">
        <?php foreach ($parkInfo as $name => $park) {
            $park_select_flag = ($name == $_SESSION['INPUT_CACHE']['Schedule_fixed']['park'])?'selected':'';
            if(!in_array($name, $instructor[$loggedInstructor]['parks'])) continue;
        ?>
            <option value="<?=$name?>" <?=$park_select_flag?> ><?=$park['cname']?></option>
        <?php } ?>
        </select>
        <label>開課雪場</label>
      </div>
    </div>    
    <div class="row">
      <div class="input-field col s3">
        <select id="2_class_slot" class="icons" name="slot[]" multiple>
<?php
if(isset($_SESSION['INPUT_CACHE']['Schedule_fixed'])){    
    $slot = $_SESSION['INPUT_CACHE']['Schedule_fixed']['slot'] ;
    for($i=1;$i<=4;$i++){
        if($i == $slot){
            echo '<option value="'.$i.'" selected >'.$i.'st</option>';
        }else{
            echo '<option value="'.$i.'">'.$i.'st</option>';
        }
    }
}else{
?>        
          <option value="1">1st</option>
          <option value="2">2nd</option>
          <option value="3">3rd</option>
          <option value="4">4th</option>
<?php
    }
?>          
        </select>
      </div>
      <div class="input-field col s3">
        <select id="2_exp" class="icons" name="expertise">
        <?php if(in_array($instructor[$loggedInstructor]['expertise'],['sb','both'])){ 
              $exp_select_flag = ($instructor[$loggedInstructor]['expertise'] == $_SESSION['INPUT_CACHE']['Schedule_fixed']['exp'])?'selected':'';
        ?>
          <option value="sb" <?=$exp_select_flag ?> >SB</option>
        <?php } ?>
        <?php if(in_array($instructor[$loggedInstructor]['expertise'],['ski','both'])){ 
              $exp_select_flag = ($instructor[$loggedInstructor]['expertise'] == $_SESSION['INPUT_CACHE']['Schedule_fixed']['exp'])?'selected':'';
        ?>
          <option value="ski" <?=$exp_select_flag ?> >SKI</option>
        <?php } ?>
        <?php if(in_array($instructor[$loggedInstructor]['expertise'],['both'])){ 
              $exp_select_flag = ($instructor[$loggedInstructor]['expertise'] == $_SESSION['INPUT_CACHE']['Schedule_fixed']['exp'])?'selected':'';
        ?>
          <option value="both" <?=$exp_select_flag ?> >不限</option>
        <?php } ?>
        </select>
      </div>
      <div class="input-field col s3">
        <select class="icons" name="type">
        <?php if( (0||$instructor[$loggedInstructor]['jobType']=='fulltime'||!in_array($instructor[$loggedInstructor]['name'], $PARKLIMITED))
          && (1||in_array($loggedInstructor, $tmpAccess) )
        ){//全職才可開課 ?>
        <?php $type_select_flag = ($_SESSION['INPUT_CACHE']['Schedule_fixed']['type'] == 'enable')?'selected':'';  ?>
          <option value="enable" <?=$type_select_flag?> >開課</option>
        <?php } ?>

        <?php $type_select_flag = ($_SESSION['INPUT_CACHE']['Schedule_fixed']['type'] == 'disable')?'selected':'';  ?>  
          <option value="disable" <?=$type_select_flag?>  >停課</option>
        <?php $type_select_flag = ($_SESSION['INPUT_CACHE']['Schedule_fixed']['type'] == 'empty')?'selected':'';  ?>    
          <option value="empty" <?=$type_select_flag?> >空堂</option>
        </select>
      </div>
      <div class="input-field col s3">
        <button id="a_lesson_create" class="btn waves-effect waves-light" type="button" >新增</button>
      </div>
    </div>
    </form>


    <?php if(($instructor[$loggedInstructor]['jobType']=='fulltime'||in_array($instructor[$loggedInstructor]['name'], $PARKLIMITED))
      && (1||in_array($loggedInstructor, $tmpAccess)) && !in_array($loggedInstructor, ['xiaotong'])
    ){//全職才可條件,團體 ?>
    <!--條件開課-->
    <br>
    <blockquote>
      <h5>3. 條件開課</h5>
      <span>設定幾天內滿幾堂可被訂課</span><br>
      <span style="color:red; font-size:1rem;">
        下列雪場為一天兩堂課，每一堂課是3小時，請勿設定一天至少三堂以上。<br>
        湯澤區、神樂、苗場、北海道區、野澤、藏王、白馬區、妙高區。
      </span>
    </blockquote>
    <form id="c_lesson_form" action="scheduleSet.php" method="post">
    <input type="hidden" name="action" value="rule">
    <input type="hidden" name="date" value="<?=$in['date']?>">
    <div class="row">
      <div class="input-field col s4">
        <input id="3_sdate" type="text" class="datepicker" name="start" value="<?=$in['date']?>">
        <label>起始日期</label>
      </div>
      <div class="input-field col s4">
        <input id="3_edate" type="text" class="datepicker" name="end" value="<?=$in['date']?>">
        <label>結束日期</label>
      </div>
      <div class="input-field col s4">
        <select id="3_park" class="icons park" name="park[]" multiple>
            <option value="all">全選</option>
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


    <?php if(0){ ?>
    <!--團體開課-->
    <br>
    <blockquote>
      <h5>4. 團體開課</h5>
      <span>指定日期開團體課程</span>
    </blockquote>
    <form id="g_lesson_form" action="scheduleSet.php" method="post">
    <input type="hidden" name="action" value="group">
    <input type="hidden" name="date" value="<?=$in['date']?>">
    <div class="row">
      <div class="input-field col s4">
        <input id="4_sdate" type="text" class="datepicker" name="start" value="<?=$in['date']?>">
        <label>起始日期</label>
      </div>
      <div class="input-field col s4">
        <input id="4_edate" type="text" class="datepicker" name="end" value="<?=$in['date']?>">
        <label>結束日期</label>
      </div>
      <div class="input-field col s4">
        <select id="4_park" class="icons park" name="park" id="park">
        <?php foreach ($parkInfo as $name => $park) {
            if(!in_array($name, $instructor[$loggedInstructor]['parks'])) continue;
        ?>
            <option value="<?=$name?>"><?=$park['cname']?></option>
          <?php } ?>
            <option value="others">其它</option>
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
    <?php }//if 0 ?>
    </form>

    <?php }//全職才可條件,團體 ?>

    <?php }//避免Admin設定錯人. ?>

</main>

    <?php }//not a support instructor ?>
    
      <?php //require('menuBottom.php'); ?>
      <?php require('foot.php'); ?>
      <script>
      var selected_parks = '';

      $(document).ready(function(){
        $('#3_park').on('change', function(){//新增全選功能
          selected_parks = $(this).children("option");
          if(selected_parks[0].selected===true){
            for(var i=0; i<selected_parks.length; i++){
              selected_parks[i].selected=true;
            }
            $('select').formSelect();//refresh
          }
        });
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

        $('#2_sdate').change(function(){        
          if( $('#2_sdate').val().length >=0 ){
               $('#2_edate').val($('#2_sdate').val());
               $("#2_edate").datepicker("setDate", $('#2_sdate').val());            
          } 
        }); 

        $('#3_sdate').change(function(){
          if( $('#3_sdate').val().length >=0 ){
               //alert($('#2_sdate').val());
               $('#3_edate').val($('#3_sdate').val());
               $("#3_edate").datepicker("setDate", $('#3_sdate').val());   
          } 
        });

        $('#4_sdate').change(function(){
          if( $('#4_sdate').val().length >=0 ){
               //alert($('#2_sdate').val());
               $('#4_edate').val($('#4_sdate').val());
               $("#4_edate").datepicker("setDate", $('#4_sdate').val());   
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


      <!--schedules-->
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
          var regexp = /s=([1-9]),/gi;
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

      $('#thisDate,#asInstructor').on('change', function(){
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
        var schedule = $(this).attr('schedule');//_d(schedule);
        var lesson = lessonArr = null;
        var type = msg = '';
        var studentNum = $(this).attr('studentNum');//_d(studentNum);

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
          getOrderInfo(oidx);
          return true;
        }//oidx

        if(gidx!=0 && studentNum!=0){//_d('團體已訂課');
          $('#deleteBtn').hide();
          getGroupOrderInfo(gidx);
          return true;
        }

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
      </script>

    </body>
</html>