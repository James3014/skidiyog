<?php
require('../includes/sdk.php');
require('../includes/cauth.php'); // cookie AUTH
require('../vendor/autoload.php');

$ko = new ko();
$loggedInstructor = $_SESSION['SKIDIY']['instructor'];//_d($loggedInstructor);
$instructor = $ko->getInstructorInfo(['type'=>'instructor','name'=>$loggedInstructor]);//_v($instructor);
$parkInfo = $ko->getParkInfo();//_v($parkInfo);

$inst = $instructor[$loggedInstructor];
$db = new DB();
$sql = "SELECT * FROM `follow` 
		WHERE `deleted`=0 
    AND (
			`instructors` LIKE '%\"{$inst['name']}\"%' OR `instructors` LIKE '%\"any\"%'
		) AND (
			`expertise` = '{$inst['expertise']}' OR '{$inst['expertise']}' = 'both'
)";//_d($sql);

$info = $db->query('SELECT', $sql);//_v($info);
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
      <?php require('menu.php'); ?>
      <div class="row">
        <div class="col s12">
        <h4>學生追蹤資訊</h4>
        <?php if(count($info)>0){ ?>
			<table>
				<thead><th style="text-align:center;">日期</th><th>雪場</th><th>課程</th><th>教練</th></thead>
	        <?php foreach ($info as $i) {
	        	$startDate = date('m/d', strtotime('-3 days', strtotime($i['date'])));
	        	$endDate = date('m/d', strtotime('+3 days', strtotime($i['date'])));
	        	$inst = implode(', ', json_decode($i['instructors'], true));
	        	$inst = ($inst=='any') ? '不限' : $inst;
	        	$park = ($i['park']=='any'?'不限':$parkInfo[$i['park']]['cname']);
	        ?>
	        	<tr>
	        		<td style="text-align:center;"><?=$startDate?><br>~<br><?=$endDate?></td>
	        		<td><?=$park?></td>
	        		<td><?=strtoupper($i['expertise'])?></td>
	        		<td><?=$inst?></td>
	        	</tr>	
			<?php }//foreach ?>
			</table>
        <?php }else{ ?>
        	無相關學生追蹤資訊
        <?php } ?>
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