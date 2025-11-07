<?php
require('../includes/sdk.php');
require('../includes/auth.php'); // Admin authentication check
    $filters = array(
        'year'          =>  FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        'month'         =>  FILTER_SANITIZE_FULL_SPECIAL_CHARS,        
        'park'          =>  FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        'instructor'    =>  FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        'status'        =>  FILTER_SANITIZE_FULL_SPECIAL_CHARS,
    );//_v($_POST);
    $in = filter_var_array(array_merge($_REQUEST,$_POST), $filters);//_v($in);//exit();

    $ko = new ko();
    $parkInfo = $ko->getParkInfo();//_v($parkInfo);
    $instructorInfo = $ko->getInstructorInfo();

    $orders = (empty($in['year'])) ? [] : $ko->getOrdersGroup($in);//_j($orders);//exit();
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
    table.order{
      font-size: 1rem;
      width: 98%;
      margin: auto;
      border: 1px solid #CCC;
    }
    table.order td, 
    table.order th{
      padding: 3px;
      border-radius: 0px;
    }
    tr.divider td{
      padding: 0.4rem 0.4rem;
      background-color: #ffcc00;
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
      font-size: 0.8rem;
    }
    cR{color:red;}
    cB{color:blue;}
    </style>
    </head>
    <body>
    <main>
    <?php require('menu.php'); ?>

    <blockquote>
        <h5>團體課程訂單資訊</h5>
    </blockquote>

    <!--form-->
    <form action="?" method="post" id="filter">     
    <div class="row">
      <div class="input-field col s4">
        <select class="icons year" name="year" id="year">
          <option value="">請選擇</option>
          <option value="all" <?=($in['year']=='all')?'selected':''?>>不限</option>
          <?php for($y=(int)date('Y')+1;$y>=2018;$y--){ ?>
            <option value="<?=$y?>" <?=($in['year']==$y)?'selected':''?>><?=$y?></option>
          <?php } ?>
        </select>
        <label><span></span> 年份</label>
      </div>

      <div class="input-field col s4">
        <select class="icons month" name="month" id="month">
          <option value="all">不限</option>
          <?php for($m=1 ;$m<=12; $m++){ ?>
          <option <?=($in['month']==$m)?'selected':''?> value="<?=$m?>" ><?=$m;?></option>
          <?php } ?>
        </select>
        <label><span></span> 月份</label>
      </div>

      <div class="input-field col s4">
        <button class="btn btn-primary" type="submit" id="query">查詢</button>
      </div>

      <div class="input-field col s6">
        <select class="icons" name="park" id="park">
          <option value="all">不限</option>
          <?php foreach ($parkInfo as $name => $park) { ?>
            <option value="<?=$name?>" <?=($in['park']==$name)?'selected':''?>><?=$park['cname']?></option>
          <?php } ?>
        </select>
        <label><span></span> 選擇雪場</label>
      </div>

      <div class="input-field col s6">
        <select class="icons" name="instructor" id="instructor">
          <option value="all">不限</option>
          <option value="virtual" <?=($in['instructor']=='virtual')?'selected':''?>>未定？</option>
          <?php foreach ($instructorInfo as $name => $inst) {
            if(empty($inst['parks'])) continue;
          ?>
            <option value="<?=$name?>" <?=($in['instructor']==$name)?'selected':''?>><?=$inst['cname']?></option>
          <?php } ?>
        </select>
        <label><span></span> 選擇教練</label>
      </div>

      

      

    </div>
    </form>
    <!--form-->     
    
    <style>
    .group td{
      vertical-align: top;
    }
    .canceled{
      font-weight: bold;
      color: red;
      font-size: 1rem;
    }
    </style>
    <table class="order group" style="margin-top:0;">
    <thead>
      <tr>
        <th>日期</th>
        <th>雪場</th>
        <th>教練</th>
        <th>課程名稱</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
    <?php
    foreach ($orders as $order) {
      $lesson = $order['lesson'];
      $orders = $order['orders'];
      $cnt = sizeof($orders);
    ?>
    <tr style="background-color: #ffcc00; height:2rem; color:<?=($cnt)?'blue':''?>;">
      <td style="width:4rem;"><?=substr($lesson['start'],-5)?><br><?=substr($lesson['end'],-5)?></td>
      <td style="width:4rem;"><?=ucfirst($parkInfo[$lesson['park']]['cname'])?><br><?=(empty($lesson['open'])?'(關閉)':'(開啟)')?></td>
      <td style="width:6rem;"><?=$instructorInfo[$lesson['instructor']]['cname']?><br><?=strtoupper($lesson['expertise'])?></td>
      <td><?=$lesson['title']?><br>費用: $<?=number_format($lesson['fee'])?><sub><?=$lesson['currency']?></sub></td>
      <td style="width:1.2rem;"><b><?=$cnt?></b></td>
    </tr>
    <?php if($cnt>=1){ ?>
    <tr style="background-color: #ffcc00;">
      <td colspan="2">課程內容</td>
      <td colspan="3"><pre><?=$lesson['content']?></pre></td>
    </tr>
    <tr>
      <td colspan="5" style="padding-bottom:2rem; background-color: #EEE;">
        <table style="width:96%; margin:auto;">
          <thead>
            <tr>
              <th style="width:6rem;">訂單編號</th>
              <th>學生</th>
              <th style="width:4rem;">訂金</th>
              <th style="width:4rem;">尾款</th>
            </tr>
          </thead>
        <?php foreach ($orders as $o) {
          $student = $ko->getMembers(['idx'=>$o['student']]);
          $student = $student[0];//_v($student);
        ?>
          <tr>
            <td>#<?=$o['oidx']?><br><?=($o['status']=='canceled')?'<span class="canceled">已取消</span>':''?></td>
            <td><?=$student['name']?><br><?=$student['email']?></td>
            <td><?=number_format($o['prepaid'])?></td>
            <td><?=number_format($o['payment'])?></td>
          </tr>
        <?php } ?>
        </table>
      </td>
    </tr>
    <?php }//報名學生 ?>
    <?php }//訂單 ?>
    </tbody>
    </table>
    </main>

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
        $('#query').on('click', function(){
          $('#filter').submit();
        });
        $('.confirm').on('click',function(){
          if(!confirm('你真的確定要取消/退訂?')){
            return false;
          }
        });
      });
      </script> 


    </body>
</html>