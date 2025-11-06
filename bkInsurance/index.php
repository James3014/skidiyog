<?php
require('../includes/sdk.php');
    $filters = array(
        'year'          =>  FILTER_SANITIZE_STRING,
        'month'         =>  FILTER_SANITIZE_STRING,        
        'park'          =>  FILTER_SANITIZE_STRING,
        'instructor'    =>  FILTER_SANITIZE_STRING,
        'status'        =>  FILTER_SANITIZE_STRING,
        'insurance'     =>  FILTER_SANITIZE_STRING,
    );//_v($_POST);
    $in = filter_var_array(array_merge($_REQUEST,$_POST), $filters);//_v($in);//exit();

    $ko = new ko();
    $parkInfo = $ko->getParkInfo();//_v($parkInfo);
    $in['park'] = 'all';
    $in['instructor'] = 'all';
    $in['status'] = Payment::PAYMENT_SUCCESS;

    $in['month'] = ($in['month']==0) ? 'all' : $in['month'];
    $orders = (empty($in['year'])) ? [] : $ko->getOrders($in);//_j($orders);//exit();
    $groups = $ko->getGroupOrders($in);//_j($groups);exit();
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
    .btn{
      padding: 0.4rem !important;
      border-radius: 6px;
    }
    </style>
    </head>
    <body>

    <header>
        <?php require('menu.php');?>
    </header>



    <blockquote>
        <h5>保險狀態&訂單資訊</h5>
    </blockquote>

    <!--form-->
    <form action="?" method="post" id="filter">
    <div class="row">
      <div class="input-field col s2">
        <select class="icons year" name="year" id="year">
          <option value="">請選擇</option>
          <?php for($y=date('Y')+1;$y>=2018;$y--){ ?>
            <option value="<?=$y?>" <?=($in['year']==$y)?'selected':''?>><?=$y?></option>
          <?php } ?>
        </select>
        <label><span></span>年份</label>
      </div>

      <div class="input-field col s2">
        <input id="lesson_title" type="number" name="month" value="<?=$in['month']?>" max="12" min="0">
        <label>月份</label>
      </div>

      <div class="input-field col s5">
        <select name="insurance" id="insurance">
          <option <?=($in['insurance']==0)?'selected':''?> value="0" >未確認</option>
          <option <?=($in['insurance']==1)?'selected':''?> value="1" >已確認</option>
          <option <?=($in['insurance']=='all')?'selected':''?> value="all">🌀 不限</option>
        </select>
        <label><span></span>確認狀態</label>
      </div>

      <div class="input-field col s3">
        <button id="filterBtn" class="btn waves-effect waves-light" type="button">查詢</button>
      </div>
      

    </div>
    </form>
    <!--form--> 
    團體課程<hr>
    <table class="order">
    <thead>
      <tr>
        <th width="10%"><p class="left">訂單編號</th>
        <th width="30%"><p class="left">上課日期</th>
        <th width="40%"><p class="left">課程名稱</th>
        <th width="20%"><p class="left">學生Email</p></th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($groups as $g) { ?>
      <tr>
        <td rowspan="2" align="center"><b>#<?=$g['oidx']?></b></td>
        <td>起:<?=$g['start']?><br>迄:<?=$g['end']?></td>
        <td><?=$g['title']?></td>
        <td><?=$g['email']?></td>
      </tr>
      <tr>
        <td colspan="3">
                    <form style="background-color: #FDA;" action="todb.php" id="configForm<?=$g['oidx']?>">
                      <input type="hidden" name="oidx" value="<?=$g['oidx']?>">
                      <input type="hidden" name="year" value="<?=$in['year']?>">
                      <input type="hidden" name="month" value="<?=$in['month']?>">
                      <input type="hidden" name="insurance" value="<?=$in['insurance']?>">
                      <input type="hidden" name="notify" value="no" id="notify<?=$g['oidx']?>">
                      <div class="row">
                        <div class="input-field col s2">
                          <select name="insuranceChecked" class="confirmSelect" oidx="<?=$g['oidx']?>">
                            <option <?=($g['insuranceChecked']==0)?'selected':''?> value="0" >No</option>
                            <option <?=($g['insuranceChecked']==1)?'selected':''?> value="1" >Yes</option>
                          </select>
                          <label><span></span>確認狀態</label>
                        </div>
                        <div class="input-field col s6">
                          <input type="text" name="insuranceMemo" placeholder="保險員備註" value="<?=$g['insuranceMemo']?>">
                        </div>
                        <div class="input-field col s4">
                          <button class="btn btn-primary setBtn" oidx="<?=$g['oidx']?>">儲存</button>
                          <?php if($g['insuranceChecked']==0){ ?>
                          <button class="btn btn-secondary emailBtn" oidx="<?=$g['oidx']?>">通知</button>
                          <?php } ?>
                        </div>
                      </div>
                    </form>
          </td>
        </tr>
    <?php } ?>
    </tbody>
    </table>
    

    私人課程<hr>
    <table class="order">
    <thead>
      <tr>
        <th width="40%"><p class="left">日期<br>時間/堂次</p></th>
        <th width="40%"><p class="left">雪場<br>教練/種類</p></th>
        <th width="20%"><p class="left">人數</p></th>
        <?php /*<th width="20%"><p class="right">金額</p></th>*/?>
      </tr>
    </thead>
    <tbody>
    <?php
    foreach ($orders as $oidx => $n) {
      $order = $ko->getOneOrderInfo(['oidx'=>$oidx]);
      $student = $ko->getMembers(['idx'=>$order['student']]);
      if(empty($student[0])){
        echo "{$oidx}";
      }
      $student = $student[0];//_v($student);
      if($order['schedule'][0]['instructor']=='virtual') continue;
    ?>
              
      <tr class="divider">
        <td>
          <b><?=empty($n['date'])?'':$n['date']?></b><br>
          <!--確認設定-->
          <?=($n['insuranceChecked'])?'保險已確認✅':'保險未確認❓';?><br>
          通知時間: <?=($n['insuranceLastNotify']=='0000-00-00 00:00:00')?'無。':$n['insuranceLastNotify']?>
        </td>
        <td colspan="3" style="text-align: right !important;">
          訂單編號：<?=$order['orderNo']?><br>
          訂課時間：<?=$order['createDateTime']?><br>
          <?=Payment::STATUS_NAME[$order['status']]?> #<?=$oidx?>
        </td>
      </tr>
                
                <?php foreach ($order['schedule'] as $n => $o) {//_v($o); ?>
                  <tr>
                    <td><b><?=substr($o['date'],5)?></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<sub>第</sub><cB><?=$o['slot']?></cB><sub>堂</sub><br><?=$parkInfo[$o['park']]['timeslot'][$o['slot']]?></td>
                    <td>
                      <?=$parkInfo[$o['park']]['cname']?><br>
                      <?=ucfirst($o['instructor'])?> / <?=strtoupper($o['expertise'])?>
                    </td>
                    <td>
                      <?=$o['studentNum']?><sub>位</sub>
                    </td>
                    <?php /*<td>
                      <p class="price right"><?=number_format($o['fee'])?><sub><?=$order['currency']?></sub></p>
                    </td>*/?>
                  </tr>
                <?php }//foreach ?>

                 <tr>
                   <!--rowspan="6" colspan="2"-->
                   <td colspan="4" style="vertical-align:top !important; padding:0.3rem;">
                    <div class="card-panel">
                      <cB><?=$student['name']?></cB>聯絡資訊<br>
                      郵: <?=$student['email']?><br>
                      <?=empty($student['phone'])?'':"電: {$student['country']},{$student['phone']}"?>
                      <?=empty($student['fbid'])?'':"賴: {$student['line']}"?>
                      <?=empty($student['fbid'])?'':"臉: {$student['fbid']}"?>
                      <?=empty($student['fbid'])?'':"微: {$student['wechat']}"?>
                    </div>
                    <cB>學生備註:</cB><br>
                    <?=$order['requirement']?><br><br>
                    <cR>管理員備註:</cR><br>
                    <?=empty($order['note'])?'無。':$order['note']?><br>
                    <cR>系統備註:</cR><br>
                    <?=empty($order['memo'])?'無。':$order['memo']?><br>

                    <form style="background-color: #FDA;" action="todb.php" id="configForm<?=$oidx?>">
                      <input type="hidden" name="oidx" value="<?=$oidx?>">
                      <input type="hidden" name="year" value="<?=$in['year']?>">
                      <input type="hidden" name="month" value="<?=$in['month']?>">
                      <input type="hidden" name="insurance" value="<?=$in['insurance']?>">
                      <input type="hidden" name="notify" value="no" id="notify<?=$oidx?>">
                      <div class="row">
                        <div class="input-field col s2">
                          <select name="insuranceChecked" class="confirmSelect" oidx="<?=$oidx?>">
                            <option <?=($order['insuranceChecked']==0)?'selected':''?> value="0" >No</option>
                            <option <?=($order['insuranceChecked']==1)?'selected':''?> value="1" >Yes</option>
                          </select>
                          <label><span></span>確認狀態</label>
                        </div>
                        <div class="input-field col s6">
                          <input type="text" name="insuranceMemo" placeholder="保險員備註" value="<?=$order['insuranceMemo']?>">
                        </div>
                        <div class="input-field col s4">
                          <button class="btn btn-primary setBtn" oidx="<?=$oidx?>">儲存</button>
                          <?php if($order['insuranceChecked']==0){ ?>
                          <button class="btn btn-secondary emailBtn" oidx="<?=$oidx?>">通知</button>
                          <?php } ?>
                        </div>
                      </div>
                    </form>

                   </td>
                   <?php /*<td>學費</td>
                    <td><p class="price right"><?=number_format($order['price'])?><sub><?=$order['currency']?></sub></p>
                   </td> */?>
                 </tr>
                 <?php /*
                 <tr>
                   <td>折扣</td><td><p class="price right"><?=number_format($order['discount'])?><sub><?=$order['currency']?></sub></p></td>
                 </tr>
                 <tr>
                   <td>特費</td><td><p class="price right"><?=number_format($order['specialDiscount'])?><sub><?=$order['currency']?></sub></p></td>
                 </tr>
                 <tr>
                   <td>訂金</td><td><p class="price right"><?=number_format($order['prepaid'])?><sub><?=$order['currency']?></sub></p></td>
                 </tr>
                 <tr>
                   <td>刷卡<sub><b><?=$order['exchangeRate']?></b></sub></td><td><p class="price right"><?=number_format($order['paid'])?><sub>NTD</sub></p></td>
                 </tr>
                 <tr>
                   <td>尾款</td><td><p class="price right"><b><u><?=number_format($order['payment']+$order['specialDiscount'])?></u></b><sub><?=$order['currency']?></sub></p></td>
                 </tr>
                 */ ?>

    <?php }//foreach orders ?>
    </tbody>
    </table>


      <!--JavaScript at end of body for optimized loading-->
      <script src="https://diy.ski/assets/js/materialize.min.js"></script>
      <!--custom js-->
      <script src="https://diy.ski/assets/js/custom.js"></script>

      <script>
      function _d(d){console.log(d)}
      function _a(a){alert(a)}
      <?php
      // if(!empty($_REQUEST['msg'])){
      //   if(isset($SYSMSG[$_REQUEST['msg']])){
      //     echo "alert('{$SYSMSG[$_REQUEST['msg']]}');";
      //   }else{
      //     echo "alert('{$_REQUEST['msg']}');"; 
      //   }
      // }
      ?>
      </script>


      <script>
      $(document).ready(function(){
        $('#filterBtn').on('click', function(){
          $('#filter').submit();
        });

        $('.confirmSelect').on('change',function(){
          var oidx = $(this).attr('oidx');//alert(oidx);
          $('#configForm'+oidx).submit();
        });

        $('.setBtn').on('click',function(e){
          e.preventDefault();//_d('send notify mail');
          var oidx = $(this).attr('oidx');//alert(oidx);
          $('#configForm'+oidx).submit();
        });

        $('.emailBtn').on('click',function(e){
          e.preventDefault();//_d('send notify mail');
          var oidx = $(this).attr('oidx');//alert(oidx);
          $('#notify'+oidx).val('yes');
          $('#configForm'+oidx).submit();
        });
      });
      </script> 


    </body>
</html>