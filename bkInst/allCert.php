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
$instructors = $ko->getInstructorInfo();//_v($instructors);
$parkInfo = $ko->getParkInfo();//_v($parkInfo);

$db = new db();
$today = date('Y-m-d');
$note = 'No Lesson';
$base58 = new StephenHill\Base58();
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
    footer {page-break-after: always;}
    </style>
    </head>
    <body>
      <main>
      
<?php $c=0; foreach ($instructors as $n => $i) { if(count($i['parks'])==0||in_array($i['name'], ['virtual','skidiy'])) continue; $refer = $base58->encode((string)($i['idx']*11+100)); ?>
      <div class="row" style="margin-top:3rem;">
        <div class="col s12">
          <table>
            <tr>
              <td style="width:50%; text-align:center;">
                <img src="/assets/images/logo-skidiy.png" style="width:100%; background-color:#1B2442 !important; border-radius: 3px;-webkit-print-color-adjust: exact;"><br>
                <!--<img src="https://diy.ski/photos/<?=$i['name']?>/<?=$i['name']?>.jpg" style="border-radius: 0.6rem; width:100%;">-->
                <img src="https://diy.ski/photos/certid/<?=$i['name']?>.jpg" style="border-radius: 0.6rem; width:90%;"><br>
                
               

              </td>
              <td style="width:50%; text-align:center; vertical-align:top; padding-top:2rem;">                
                <b style="font-size:1.4rem;">2019~20 Winter</b><br>
                <span style="font-size:1rem;">勤務期間：<?=$i['since']?>年から</span>
                
                <h3><?=ucfirst($i['official_cname'])?></h3>
                <h4><?=ucfirst($i['official_ename'])?></h4>
                <!--<h3><?=ucfirst($i['cname'])?></h3>-->
                <div style="border: 1px solid #AAA; padding: 0.2rem;">
                  <b><?=$today?></b>
                </div> 
                <div style="padding: 0.1rem 0; font-size:1.0rem;">
                  <b><?=$note?></b>
                </div>
                <div style="padding: 0.2rem 0; font-size:0.8rem;">
                  <b>https://diy.ski/refer/<?=$refer?></b>
                </div>
                <span style="color: #619AEC; font-size:1.8rem;">SKIDIY Staff</span>
              </td>
            </tr>
          </table>
          <?=($c+1).'. '.$i['name']?>
        </div>
      </div>
      <?=(($c+1)%2==0)?'<footer></footer>':''?>
<?php $c++;} ?>
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