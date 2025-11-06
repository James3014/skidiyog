<?php
//require('../includes/auth.php');
require('../includes/sdk.php');
require('../includes/cauth.php');
$insuranceFUNC = new INSURANCE();
$loggedInstructor = $_SESSION['SKIDIY']['instructor'];//教練登入帳號

    $filters = array(
        'year'          =>  FILTER_SANITIZE_STRING,
        'month'         =>  FILTER_SANITIZE_STRING,        
        'park'          =>  FILTER_SANITIZE_STRING,
        'instructor'    =>  FILTER_SANITIZE_STRING,
        'status'        =>  FILTER_SANITIZE_STRING,
    );//_v($_POST);
    $in = filter_var_array(array_merge($_REQUEST,$_POST), $filters);//_v($in);//exit();
    $in['instructor'] = $loggedInstructor;

    $ko = new ko();

    $parkInfo = $ko->getParkInfo();//_v($parkInfo);
    $instructor = $ko->getInstructorInfo(['type'=>'instructor','name'=>$loggedInstructor]);//_v($instructor);

    $orders = (empty($in['year'])) ? [] : $ko->getOrders($in);//_j($orders);exit();
?>

<!DOCTYPE html>
<html>
    <head>
    <?php require('head.php'); ?>
    <style type="text/css">
    table.order{
      font-size: 1rem;
      width: 98%;
      margin: 0 auto;
      border: 1px solid #CCC;
    }
    table.order td, 
    table.order th{
      padding: 3px;
      border-radius: 0px;
    }
    tr.divider td{
      padding: 1rem 0.4rem;
      background-color: #ffcc00;
    }
    .card-panel{
      padding: 0.4rem;
    }
    cB{color: blue;}
    cR{color: red;}
    sup{
      font-weight: bold;
      color: blue;
      font-size: 0.8rem;
    }
    .input-field>label, .input-field label.active{
      font-size: 1rem;
    }
    </style>
    </head>
    <body>
    <main>  
    <?php require('menu.php'); ?>

    <blockquote>
        <h5>訂單資訊</h5>
    </blockquote>

    <!--form-->
    <form action="?" method="post" id="filter">     
    <div class="row">
      <div class="input-field col s3">
        <select class="icons year" name="year" id="year">
          <option value="">請選擇</option>
          <option value="all" <?=($in['year']=='all')?'selected':''?>>不限</option>
          <?php for($y=(int)date('Y')+1;$y>=2018;$y--){ ?>
            <option value="<?=$y?>" <?=($in['year']==$y)?'selected':''?>><?=$y?></option>
          <?php } ?>
        </select>
        <label><span></span> 年份</label>
      </div>

      <div class="input-field col s2">
        <select class="icons month" name="month" id="month">
          <option value="all">不限</option>
          <?php for($m=1 ;$m<=12; $m++){ ?>
          <option <?=($in['month']==$m)?'selected':''?> value="<?=$m?>" ><?=$m;?></option>
          <?php } ?>
        </select>
        <label><span></span> 月份</label>
      </div>

      <div class="input-field col s3">
        <select class="icons" name="park" id="park">
          <option value="all">不限</option>
          <?php foreach ($parkInfo as $name => $park) {
            if(!in_array($name, $instructor[$loggedInstructor]['parks'])) continue;
          ?>
          <option value="<?=$name?>" <?=($in['park']==$name)?'selected':''?>><?=$park['cname']?></option>
        <?php } ?>
        </select>
        <label><span></span> 選擇雪場</label>
      </div>

      <div class="input-field col s4">
        <select name="status" id="status">
          <option <?=(Payment::PAYMENT_SUCCESS==$in['status'])?'selected':''?> value="<?=Payment::PAYMENT_SUCCESS?>" ><?=Payment::STATUS_NAME[Payment::PAYMENT_SUCCESS]?></option>
          <option <?=(Payment::PAYMENT_CREATED==$in['status'])?'selected':''?> value="<?=Payment::PAYMENT_CREATED?>" ><?=Payment::STATUS_NAME[Payment::PAYMENT_CREATED]?></option>
          <!--<option <?=(Payment::PAYMENT_TIMEOUT==$in['status'])?'selected':''?> value="<?=Payment::PAYMENT_TIMEOUT?>" ><?=Payment::STATUS_NAME[Payment::PAYMENT_TIMEOUT]?></option>-->
          <option <?=(Payment::PAYMENT_CANCELING==$in['status'])?'selected':''?> value="<?=Payment::PAYMENT_CANCELING?>" ><?=Payment::STATUS_NAME[Payment::PAYMENT_CANCELING]?></option>
          <!--<option <?=(Payment::PAYMENT_CANCELED==$in['status'])?'selected':''?> value="<?=Payment::PAYMENT_CANCELED?>" ><?=Payment::STATUS_NAME[Payment::PAYMENT_CANCELED]?></option>
          <option <?=(Payment::PAYMENT_FAILURE==$in['status'])?'selected':''?> value="<?=Payment::PAYMENT_FAILURE?>" ><?=Payment::STATUS_NAME[Payment::PAYMENT_FAILURE]?></option>
          <option <?=('all'==$in['status'])?'selected':''?> value="all">🌀 不限</option>-->
        </select>
        <label><span></span>交易狀態</label>
      </div>

    </div>
    </form>
    <!--form--> 
    

    <table class="order">
    <thead>
      <tr>
        <th width="30%"><p class="left">日期<br>時間/堂次</p></th>
        <th width="40%"><p class="left">雪場<br>教練/種類</p></th>
        <th width="10%"><p class="left">人數</p></th>
        <th width="20%"><p class="right">金額</p></th>
      </tr>
    </thead>
    <tbody>

    <?php
    $total = $lessons = $totalFee = 0; 
    foreach ($orders as $oidx => $n) {
      $order = $ko->getOneOrderInfo(['oidx'=>$oidx]);

      $student = $ko->getMembers(['idx'=>$order['student']]);
      $student = $student[0];//_v($student);
      $distinctInstructors = [];
    ?>
              
      <tr class="divider">
        <td>
          <b><?=empty($n['date'])?'':$n['date']?></b><br>
          <?=$order['extraInfo']['classType']?><?=($order['extraInfo']['arranged']?'<br><b>有經管理員排課</b>':'')?>
        </td>
        <td colspan="3" style="text-align: right !important;">
          訂單編號:<?=$order['orderNo']?>
          <?php
            if(!empty($order['refer'])){
              echo "<span style=\"color:blue;\"><b>｜{$order['refer']}介紹</span>";
            }
          ?>
          <br>
          #<?=$oidx?> <?=Payment::STATUS_NAME[$order['status']]?><br>
          訂課時間：<?=$order['createDateTime']?><br>
          <!--<?=$order['insuranceChecked']?'<cB>✅保險已確認</cB>':'<cR>🚫保險未確認</cR>'?>-->
          保險狀態(Beta Testing)：<?=$INSURANCE_STATUS_LABEL[$insuranceFUNC->check_order_status($order['oidx'])];?>

        </td>
      </tr>
                
                <?php foreach ($order['schedule'] as $n => $o) {
                  if($o['instructor']==$loggedInstructor){
                    $lessons+=1;//_v($o);
                    //$total+=$o['fee'] - ($parkInfo[$o['park']]['insurance']*$o['studentNum']);
                    $totalFee += $parkInfo[$o['park']]['base'] + $parkInfo[$o['park']]['unit']*$o['studentNum'];//只累計學費
                  }
                  // if(in_array($o['park'], ['nozawa','shiga','hakuba','tsugaike'])){
                  //    $parkInfo[$o['park']]['deposit'] = 6000;
                  // }
                  $_payment = $o['fee'] - $parkInfo[$o['park']]['deposit'] - ($parkInfo[$o['park']]['insurance']*$o['studentNum']);
                  $distinctInstructors[$o['instructor']] = empty($distinctInstructors[$o['instructor']]) ? $_payment : $distinctInstructors[$o['instructor']]+$_payment;
                  $firstDate = empty($firstDate) ? $o['date'] : $firstDate;
                ?>

                  <tr>
              
                    <td><b><?=substr($o['date'],5)?></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<sub>第</sub><cB><?=$o['slot']?></cB><sub>堂</sub><br><?=$parkInfo[$o['park']]['timeslot'][$o['slot']]?></td>
                    <td>
                      <?=$parkInfo[$o['park']]['cname']?><br>
                      <?=ucfirst($o['instructor'])?>/<?=strtoupper($o['expertise'])?>
                    </td>
                    <td>
                      <?=$o['studentNum']?><sub>位</sub>
                    </td>
                    <td>
                      <p class="price right"><?=$o['noshow']?'已取消課程（NoShow）':''?><?=number_format($o['fee'])?><sub><?=$order['currency']?></sub></p>
                    </td>
                  </tr>
                  <?php }//foreach ?>
                 <tr>
                   <td rowspan="6" colspan="2" style="vertical-align:top !important; padding:0.3rem;">
                    <div class="card-panel">
                      <cB><?=$student['name']?></cB>聯絡資訊<br>
                      <?php if( (strtotime($firstDate)-(31*24*60*60)) <= time() ){ ?>
                      郵: <a href="mailto:<?=$student['email']?>"><?=$student['email']?></a><br>
                      <?=empty($student['phone'])?'':"電: {$student['country']},{$student['phone']}"?>
                      <?=empty($student['fbid'])?'':"賴: {$student['line']}"?>
                      <?=empty($student['fbid'])?'':"臉: {$student['fbid']}"?>
                      <?=empty($student['fbid'])?'':"微: {$student['wechat']}"?>
                      <?php }else{ ?>
                        <cB>上課前一個月開放學生聯絡資訊.</cB>
                      <?php } ?>
                    </div>
                    <cB>學生備註:</cB><br>
                    <?=$order['requirement']?><br><br>
                    <cR>管理員備註:</cR><br>
                    <?=empty($order['note'])?'無。':$order['note']?><br>
                   </td>
                   <td>學費</td>
                    <td><p class="price right"><?=number_format($order['price'])?><sub><?=$order['currency']?></sub></p>
                   </td>
                 </tr>
                 <tr>
                   <td>折扣</td><td><p class="price right"><?=number_format($order['discount'])?><sub><?=$order['currency']?></sub></p></td>
                 </tr>
                 <tr>
                   <td>特費</td><td><p class="price right"><?=number_format($order['specialDiscount'])?><sub><?=$order['currency']?></sub></p></td>
                 </tr>
                 <tr>
                   <td>訂金<td><p class="price right"><?=number_format($order['prepaid'])?><sub><?=$order['currency']?></sub></p></td>
                 </tr>
                 <tr>
                   <td>刷卡<sub><b><?=$order['exchangeRate']?></b></sub><td><p class="price right"><?=number_format($order['paid'])?><sub>NTD</sub></p></td>
                 </tr>
                 <tr>
                   <td>尾款</td><td><p class="price right"><b><?=number_format($order['payment']+$order['specialDiscount'])?></b><sub><?=$order['currency']?></sub></p></td>
                 </tr>

                 <?php if(sizeof($distinctInstructors)>1){ ?>
                 <tr>
                  <td colspan="4" style="text-align:right;"><cR>各別尾款</cR> ｜ <span style="font-size:1rem; font-weight:bold;">
                    <?php
                      $_checkPayment = 0;
                      foreach ($distinctInstructors as $_instructor => $_payment) {
                        echo  sprintf("%s: <cB>%s</cB> &nbsp; ｜ ", ucfirst($_instructor), number_format($_payment)."<sub>{$order['currency']}</sub>");
                        $_checkPayment += $_payment;
                      }
                      if($_checkPayment != ($order['payment']+$order['specialDiscount'])){
                        echo '<br><cR>請由第一位上課教練全收尾款'.number_format($order['payment']+$order['specialDiscount']).'，其中包含因人數異動後代收(墊) $'.number_format($order['payment']+$order['specialDiscount']-$_checkPayment).' 訂金，並交給後續教練各別尾款。</cR>';
                      }
                    ?>
                  </span></td>
                 </tr>
                 <?php }?>

    <?php }//foreach orders ?>
    <tr>
      <td colspan="4">
        <p class="right" style="font-size:1.2rem;">
          共<b> <?=$lessons?></b> 堂課, 總學費(不含保險) <b>$<?=number_format($totalFee)?></b><br>
        </p>
      </td>
    </tr>
    </tbody>
    </table>
    
    <?php require('foot.php'); ?>
    </main>

      <!--JavaScript at end of body for optimized loading-->
      <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-rc.2/js/materialize.min.js"></script>
      <!-- add by mj for ios 13.x  select known issue-->
      <script src="https://diy.ski/assets/js/select_workaround.js"></script>      
      
      <!--custom js-->
      <script src="https://<?=domain_name?>/assets/js/custom.js"></script>      

      <script>
      function _d(d){console.log(d)}
      function _a(a){alert(a)}      
      </script>

      <script>
      $(document).ready(function(){
        $('#year, #month, #park, #status').on('change', function(){
          $('#filter').submit();
        });
      });
      </script> 


    </body>
</html>