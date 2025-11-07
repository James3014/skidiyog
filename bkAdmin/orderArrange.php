<?php
require('../includes/sdk.php');

$filters = array(
    'oidx'        =>  FILTER_SANITIZE_FULL_SPECIAL_CHARS,
);
$in = filter_var_array(array_merge($_REQUEST,$_POST), $filters);//_v($in);exit();

$ko = new ko();
$order = $ko->getOneOrderInfo(['oidx'=>$in['oidx']]);//_v($order);
if(empty($order['schedule'][0]['date'])){
  echo 'Order date error!!';exit();
}
if(empty($order['schedule'][0]['expertise'])){
  echo 'Order expertise error!!';exit();
}
if(empty($order['schedule'][0]['park'])){
  echo 'Order park error!!';exit();
}


$in['date'] = $order['schedule'][0]['date'];
$in['expertise'] = $order['schedule'][0]['expertise'];
$in['park'] = $order['schedule'][0]['park'];
$in['instructor'] = [];//_v($in);

$parkInfo = $ko->getParkInfo();
$instructorInfo = $ko->getInstructorInfo(['type'=>'reservation','jobType'=>'fulltime']);//_j($instructorInfo);exit();
$calendar = $ko->getSchedulesResv($in, $instructorInfo, $distinctParks, true);//_j($calendar);//exit();
$distinctParks = $ko->distinctParkName($calendar);//_v($distinctParks);//exit();
$distinctInstructors = $ko->distinctInstructorName($calendar);

foreach ($distinctInstructors as $instructor) {
  foreach ($order['schedule'] as $s) {
    if(empty($calendar[$s['date']][$s['slot']][$s['park']][$instructor])){
      $distinctInstructors[$instructor] = '沒有空堂';
    }
  }
}

$needArrange = $ko->acceptionHistory($in['oidx'], $acceptHistory);
//_j($distinctInstructors);exit();

?>
<!DOCTYPE html>
<html>
    <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover, user-scalable=false"/>
      
      <!--Import materialize.css-->
      <link rel="stylesheet" href="https://diy.ski/assets/css/materialize.min.css">
      <!--Import custom.css-->
      <link rel="stylesheet" href="https://diy.ski/assets/css/custom.min.css">
      <!--Import jQuery-->
      <script src="https://diy.ski/assets/js/jquery.min.js"></script>
      
    <style type="text/css">
    body{
      font-size: 1rem;
    }
    th,tr,td{
      padding: 0.2rem;
    }
    .card-panel{
      padding: 0.4rem;
    }
    cB{color: blue; font-weight: bold;}
    cR{color: red;}
    sup{
      font-weight: bold;
      color: blue;
      font-size: 0.8rem;
    }
    .input-field>label{
      font-size: 0.8rem;
    }
    a{
      text-decoration: underline;
      font-size: 1rem;
    }
    </style>
    </head>
    <body>
    <?php require('menu.php'); ?>

    <table>
    <thead>
      <tr style="background-color: #ffcc00;">
        <td colspan="4">訂單編號：#<?=$in['oidx']?></td>
      </tr>
      <tr>
        <th width="37%"><p class="left">日期 / 堂次</p></th>
        <th width="35%"><p class="left">雪場 / 種類</p></th>
        <th width="8%"><p class="left">人數</p></th>
        <th width="20%"><p class="right">金額</p></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($order['schedule'] as $s) { ?>
        <tr>
          <td><?=$s['date']?> <sub>第</sub><?=$s['slot']?><sub>堂</sub></td>
          <td><?=$s['park']?> / <?=$s['expertise']?></td>
          <td><?=$s['studentNum']?> 位</td>
          <td><p class="right"><?=number_format($s['fee'])?><sub><?=$order['currency']?></sub></p></td>
        </tr>
      <?php } ?>
      <tr>
        <td colspan="4">
        學生備註：<?=$order['requirement']?><br>
        管理員備註：<?=$order['note']?><br>
        系統備註：<?=$order['memo']?><br>
        </td>
      </tr>
    </tbody>
    </table>

    <table>
      <tr style="background-color: #CCC;">
        <td colspan="2">申請紀錄：<?=$acceptHistory?></td>
      </tr>
      <?php foreach ($distinctInstructors as $instructor => $availability) { ?>
      <tr style="height:3rem;">
        <td><p class="right"><?=$instructor?></p></td>
        <td>
        <?php if($instructor==$availability){ ?>
          <a class="apply" href="orderApply.php?oidx=<?=$in['oidx']?>&instructor=<?=$instructor?>">👉開課申請</a>
        <?php }else{ ?>
          🚫<?=$availability?>
        <?php } ?>
        </td>
      </tr>
      <?php } ?>
      
    </table>




      <!--JavaScript at end of body for optimized loading-->
      <script src="https://diy.ski/assets/js/materialize.min.js"></script>
      <!--custom js-->
      <script src="https://<?=domain_name?>/assets/js/custom.js"></script>      

      <script>
      function _d(d){console.log(d)}
      function _a(a){alert(a)}      
      </script>


      <script>
      $(document).ready(function(){
        $('a.apply').on('click', function(e){
          return confirm('確定要寄出開課申請？');
        });
      });
      </script> 


    </body>
</html>