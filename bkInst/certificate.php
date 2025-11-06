<?php
//require('../includes/auth.php');
require('../includes/sdk.php');
require('../includes/cauth.php'); // cookie AUTH
require('../vendor/autoload.php');

$filters = array(
    'none'        =>  FILTER_SANITIZE_STRING,
);//_v($_POST);
$in = filter_var_array(array_merge($_REQUEST,$_POST), $filters);//_v($in);//exit();


$ko = new ko();
$loggedInstructor = $_SESSION['SKIDIY']['instructor'];  
$instructor = $ko->getInstructorInfo(['type'=>'instructor','name'=>$loggedInstructor]);
$parkInfo = $ko->getParkInfo();//_v($parkInfo);

$db = new db();
$today = date('Y-m-d');
$sql = "SELECT * FROM `schedules` WHERE `instructor`='{$loggedInstructor}' AND `date`='{$today}' AND `oidx`!=0";//_d($sql);
$lessons = $db->query('SELECT',$sql);
$note='';
if(sizeof($lessons)>=1 && isset($lessons[0]['oidx'])){
  foreach ($lessons as $l) {
    $note .= ucfirst($l['park']).' '.$parkInfo[$l['park']]['timeslot'][$l['slot']].'<br>';
  }
  $note = substr($note, 0, -1);
}else{
  $note = 'No Lesson';
}

$base58 = new StephenHill\Base58();
$refer = $base58->encode((string)($instructor[$loggedInstructor]['idx']*11+100));
?>

<!DOCTYPE html>
<html>
    <head>
    <?php require('head.php'); ?>
    <style>
    cR{color: red;}
    cB{color: blue;}
    table{
      border: 1px #999 solid;
      box-shadow: 5px 5px #AAA;
    }
    td{
     border-bottom: 1px #999 solid; 
    }
    </style>
    </head>
    <body>
    

      <main>
      <?php 
        require('menu.php'); 
        $photo = '../photos/certid/'.$loggedInstructor.'.jpg';
        //echo $photo;
        if(file_exists($photo)){
          $link_path = 'certid/'.$loggedInstructor.'.jpg';
        }else{
          $link_path = $loggedInstructor.'/'.$loggedInstructor.'.jpg';
        }

      ?>
      <div class="row" style="margin-top:3rem;">
        <div class="col s12">
          <table>
            <tr>
              <td style="width:50%; text-align:center;">
                <img src="/assets/images/logo-skidiy.png" style="width:100%; background-color:#1B2442 !important; border-radius: 3px;"><br>
                <!--<img src="https://diy.ski/photos/<?=$loggedInstructor?>/<?=$loggedInstructor?>.jpg" style="border-radius: 0.6rem; width:100%;">-->
                <img src="https://diy.ski/photos/<?=$link_path?>" style="border-radius: 0.6rem; width:90%;">
                
              </td>
              <td style="width:50%; text-align:center; vertical-align:top; padding-top:2rem;">
                <b style="font-size:1.4rem;"><?=date('Y')?>~<?=((int)date('Y'))+1?> Winter</b><br>
                <span style="font-size:1rem;">勤務期間：<?=$instructor[$loggedInstructor]['since']?>年から</span>
                <h3><?=ucfirst($instructor[$loggedInstructor]['official_cname'])?></h3>
                <h4><?=ucfirst($instructor[$loggedInstructor]['official_ename'])?></h4>                
                <!--<h3><?=ucfirst($loggedInstructor)?></h3>-->
                <?php
                  if(strlen($instructor[$loggedInstructor]['official_cname'])==0 ){
                    echo '<h3>'.ucfirst($loggedInstructor).'</h3>';
                  }
                ?>
                <div style="border: 2px solid #AAA; padding: 0.6rem;">
                  <b><?=$today?></b>
                </div>
                <div style="padding: 1rem 0; font-size:1.4rem;">
                  <b><?=$note?></b>
                </div>
                <div style="padding: 1rem 0; font-size:1.2rem;">
                  <b>https://diy.ski/refer/<?=$refer?></b>
                </div>
                <span style="color: #619AEC; font-size:1.8rem;">SKIDIY Staff</span>
              </td>
            </tr>
          </table>
          
        </div>
      </div>
      </main>
      <!--JavaScript at end of body for optimized loading-->
      <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-rc.2/js/materialize.min.js"></script>
      
      <!--custom js-->
      <script src="https://<?=domain_name?>/assets/js/custom.js"></script>    
      <script>
      $(document).ready(function(){
        $('.sidenav').sidenav();
      });
      </script>

    </body>
</html>