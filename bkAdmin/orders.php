<?php
require('../includes/sdk.php');
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

    if(isset($_REQUEST['soidx'])){
      $orderFUNC = new ORDER();
      $orders = $orderFUNC->getOrders($_REQUEST['soidx']);
    }else{
      $orders = (empty($in['year'])) ? [] : $ko->getOrders($in);//_j($orders);//exit();
    }

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
        <h5>訂單資訊</h5>
    </blockquote>

    <!--form-->
    <form action="?" method="post" id="filter">     
    <div class="row">
      <div class="input-field col s3">
        <select class="icons year" name="year" id="year" >
          <option value="">請選擇</option>
          <option value="all" <?=($in['year']=='all')?'selected':''?> >不限</option>
          <?php for($y=(int)date('Y')+1;$y>=2018;$y--){ ?>
            <option value="<?=$y?>" <?=($in['year']==$y)?'selected':''?>><?=$y?></option>
          <?php } ?>
        </select>
        <label><span></span> 上課年份</label>
      </div>

      <div class="input-field col s3">
        <select class="icons month" name="month" id="month">
          <option value="all">不限</option>
          <?php for($m=1 ;$m<=12; $m++){ ?>
          <option <?=($in['month']==$m)?'selected':''?> value="<?=$m?>" ><?=$m;?></option>
          <?php } ?>
        </select>
        <label><span></span> 上課月份</label>
      </div>

      <div class="input-field col s6">
        <select name="status" id="status">
          <option <?=(Payment::PAYMENT_SUCCESS==$in['status'])?'selected':''?> value="<?=Payment::PAYMENT_SUCCESS?>" ><?=Payment::STATUS_NAME[Payment::PAYMENT_SUCCESS]?></option>
          <option <?=(Payment::PAYMENT_CREATED==$in['status'])?'selected':''?> value="<?=Payment::PAYMENT_CREATED?>" ><?=Payment::STATUS_NAME[Payment::PAYMENT_CREATED]?></option>
          <option <?=(Payment::PAYMENT_TIMEOUT==$in['status'])?'selected':''?> value="<?=Payment::PAYMENT_TIMEOUT?>" ><?=Payment::STATUS_NAME[Payment::PAYMENT_TIMEOUT]?></option>
          <option <?=(Payment::PAYMENT_CANCELING==$in['status'])?'selected':''?> value="<?=Payment::PAYMENT_CANCELING?>" ><?=Payment::STATUS_NAME[Payment::PAYMENT_CANCELING]?></option>
          <option <?=(Payment::PAYMENT_CANCEL==$in['status'])?'selected':''?> value="<?=Payment::PAYMENT_CANCEL?>" ><?=Payment::STATUS_NAME[Payment::PAYMENT_CANCEL]?></option>
          <option <?=(Payment::PAYMENT_CANCELED==$in['status'])?'selected':''?> value="<?=Payment::PAYMENT_CANCELED?>" ><?=Payment::STATUS_NAME[Payment::PAYMENT_CANCELED]?></option>
          <option <?=(Payment::PAYMENT_REFUND==$in['status'])?'selected':''?> value="<?=Payment::PAYMENT_REFUND?>" ><?=Payment::STATUS_NAME[Payment::PAYMENT_REFUND]?></option>
          <option <?=(Payment::PAYMENT_FAILURE==$in['status'])?'selected':''?> value="<?=Payment::PAYMENT_FAILURE?>" ><?=Payment::STATUS_NAME[Payment::PAYMENT_FAILURE]?></option>
          <option <?=(Payment::PAYMENT_NOSHOW==$in['status'])?'selected':''?> value="<?=Payment::PAYMENT_NOSHOW?>" ><?=Payment::STATUS_NAME[Payment::PAYMENT_NOSHOW]?></option>
          <option <?=('all'==$in['status'])?'selected':''?> value="all">🌀 不限</option>
        </select>
        <label><span></span>交易狀態</label>
      </div>

      <div class="input-field col s3">
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
            if(!$ko->instructorObsolete($name)){
          ?>
            <option value="<?=$name?>" <?=($in['instructor']==$name)?'selected':''?>><?=$inst['cname']?></option>
          <?php 
            }
          } // end of foreach 
          ?>
        </select>
        <label><span></span> 選擇教練</label>
      </div>

      <div class="input-field col s3">
        <button class="btn btn-primary" type="submit" id="query">查詢</button>
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
    $multi_instructors_list='';
    $micnt=0;
    $cnt = $total = $lessons =$total_price_by_order = $totalFee = 0; 
    foreach ($orders as $oidx => $n) {
      $cnt++;
      $order = $ko->getOneOrderInfo(['oidx'=>$oidx]);
      $student = $ko->getMembers(['idx'=>$order['student']]);
      if(empty($student[0])){
        echo "#{$oidx}-{$order['student']} not found!";
      }else{
        $student = $student[0];//_v($student);
      }
      $acceptHistory = '';
      if($in['instructor']=='virtual'){
        $needArrange = $ko->acceptionHistory($oidx, $acceptHistory);
      }
      $distinctInstructors = [];

    ?>
              
      <tr class="divider">
        <td>
          <b><?=empty($n['date'])?'':$n['date']?></b><br>
          <!--訂單設定-->
          <?php if(!empty($order['noshow'])){ ?>
            <span style="color:red;"><b>學生 No Show!!!</b></span><br>
          <?php }?>

          <?php if(in_array($order['status'], ['canceling'])){//取消訂單 ?>
            <a class="confirm" href="orderEdit.php?action=canceled&oidx=<?=$order['oidx']?>&year=<?=$in['year']?>&month=<?=$in['month']?>&park=<?=$in['park']?>&instructor=<?=$in['instructor']?>&status=<?=$in['status']?>">🚫取消訂單</a>
            <br><br>
            <a class="confirm" href="orderEdit.php?action=success&oidx=<?=$order['oidx']?>&year=<?=$in['year']?>&month=<?=$in['month']?>&park=<?=$in['park']?>&instructor=<?=$in['instructor']?>&status=<?=$in['status']?>">✅回復訂單</a>
          <?php }else if(in_array($order['status'], ['create','success'])){//取消訂單 ?>
            <a class="confirm" href="orderEdit.php?action=canceled&oidx=<?=$order['oidx']?>&year=<?=$in['year']?>&month=<?=$in['month']?>&park=<?=$in['park']?>&instructor=<?=$in['instructor']?>&status=<?=$in['status']?>">🚫取消訂單</a>
          <?php }else if($order['status']=='canceled' && empty($order['noshow'])){ ?>
            <a class="confirm" href="orderEdit.php?action=refund&oidx=<?=$order['oidx']?>&year=<?=$in['year']?>&month=<?=$in['month']?>&park=<?=$in['park']?>&instructor=<?=$in['instructor']?>&status=<?=$in['status']?>">🔄刷退訂單</a>
          <?php } ?>
          <br>
          <?php if(isset($order['extraInfo'])){ ?>
          <?=$order['extraInfo']['classType']?><?=($order['extraInfo']['arranged']?'<br><b>有經管理員排課</b>':'')?>
          <?php } ?>
          <br><?=$acceptHistory?>
          <?php if($in['instructor']=='virtual' && $needArrange===false){ ?>
            <br><a href="orderArrange.php?oidx=<?=$oidx?>" target="_blank">🔀重排</a>
          <?php } ?>
        </td>
        <td colspan="3" style="text-align: right !important;">
          訂單編號：<?=$order['orderNo']?>
          <?php
            if(!empty($order['refer'])){
              echo "<span style=\"color:blue;\"><b>｜{$order['refer']}介紹</span>";
            }
          ?>
          <br>
          訂課時間：<?=$order['createDateTime']?><br>
          <?=Payment::STATUS_NAME[$order['status']]?> #<?=$oidx?><br>
          <?=$order['insuranceChecked']?'<cB>✅保險已確認</cB>':'<cR>🚫保險未確認</cR>'?>
        </td>
      </tr>
                
                <?php foreach ($order['schedule'] as $n => $o) {//_v($o); 
                  if($o['instructor']==$in['instructor']||$in['instructor']=='all'){
                    $lessons+=1;//_v($o);
                    if(empty($o['park'])){echo $o['oidx'].',';}
                    $total+=$o['fee'] - ($parkInfo[$o['park']]['insurance']*$o['studentNum']); // 算法有錯 2020.01.18.mj
                    //echo '-'.($parkInfo[$o['park']]['insurance']*$o['studentNum']);
                    $totalFee += $parkInfo[$o['park']]['base'] + $parkInfo[$o['park']]['unit']*$o['studentNum'];//只累計學費
                  }
                  
                  // if(in_array($o['park'], ['nozawa','shiga','hakuba','tsugaike'])){
                  //    $parkInfo[$o['park']]['deposit'] = 6000;
                  // }
                  $_payment = $o['fee'] - $parkInfo[$o['park']]['deposit'] - ($parkInfo[$o['park']]['insurance']*$o['studentNum']);
                  //echo "{$oidx},{$_payment} = {$o['fee']}-{$parkInfo[$o['park']]['deposit']}-{$parkInfo[$o['park']]['insurance']}*{$o['studentNum']}<hr>";
                  $distinctInstructors[$o['instructor']] = empty($distinctInstructors[$o['instructor']]) ? $_payment : $distinctInstructors[$o['instructor']]+$_payment;
                ?>
                  <tr>
                    <td><b><?=substr($o['date'],5)?></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<sub>第</sub><cB><?=$o['slot']?></cB><sub>堂</sub><br><?=$parkInfo[$o['park']]['timeslot'][$o['slot']]?></td>
                    <td>
                      <?=$parkInfo[$o['park']]['cname']?><br>
                      <?=ucfirst($o['instructor'])?> / <?=strtoupper($o['expertise'])?>
                    </td>
                    <td>
                      <?=$o['studentNum']?><sub>位</sub>
                    </td>
                    <td>
                      <p class="price right"><?=(isset($o['noshow']) && $o['noshow']==1)?'已取消課程（NoShow）':''?><?=number_format($o['fee'])?><sub><?=$order['currency']?></sub></p>
                    </td>
                  </tr>
                <?php }//foreach ?>

                 <tr>
                   <td rowspan="6" colspan="2" style="vertical-align:top !important; padding:0.3rem;">
                    <div class="card-panel">
                      <cB><?=empty($student['name'])?'姓名： n/a ':$student['name'] ?></cB>聯絡資訊<br>
                      郵: <?=empty($student['email'])?'Email: n/a':$student['email'] ?><br>
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
                    <cR>保險員備註:</cR><br>
                    <?=empty($order['insuranceMemo'])?'無。':$order['insuranceMemo']?><br>

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
                   <td>訂金</td><td><p class="price right"><?=number_format($order['prepaid'])?><sub><?=$order['currency']?></sub></p></td>
                 </tr>
                 <tr>
                   <td>刷卡<sub><b><?=$order['exchangeRate']?></b></sub></td><td><p class="price right"><?=number_format($order['paid'])?><sub>NTD</sub></p></td>
                 </tr>
                 <tr>
                   <td>尾款</td><td><p class="price right"><b><u><?=number_format($order['payment']+$order['specialDiscount'])?></u></b><sub><?=$order['currency']?></sub></p></td>
                 </tr>
                 <?php if(sizeof($distinctInstructors)>1){ ?>
                 <tr>
                  <td colspan="4" style="text-align:right;"><cR>各別尾款</cR> ｜ <span style="font-size:1rem; font-weight:bold;">
                    <?php
                      $_checkPayment = 0;
                      foreach ($distinctInstructors as $_instructor => $_payment) {
                        if($_checkPayment==0) $frist_instructor = ucfirst($_instructor);
                        echo  sprintf("%s: <cB>%s</cB> &nbsp; ｜ ", ucfirst($_instructor), number_format($_payment)."<sub>{$order['currency']}</sub>");
                        $_checkPayment += $_payment;
                      }
                      if($_checkPayment != ($order['payment']+$order['specialDiscount'])){
                          $micnt++;
                          $multi_instructors_list .= $micnt.': '.$order['oidx'].' - '.$frist_instructor.' - '.$o['date']."<br>";                        
                        echo '<br><cR>請由第一位上課教練全收尾款'.number_format($order['payment']+$order['specialDiscount']).'，其中包含因人數異動後代收(墊) $'.number_format($order['payment']+$order['specialDiscount']-$_checkPayment).' 訂金，並交給後續教練各別尾款。</cR>';
                      }
                    ?>
                  </span></td>                  
                 </tr>
                 <?php }?>

    <?php 

        $total_price_by_order += ($order['price']-$order['discount']+$order['specialDiscount']);
      }//foreach orders 
    ?>
    <tr>
      <td colspan="4">
        <!--
        <p class="right" style="font-size:1.2rem;">共<b><?=$cnt?></b>筆訂單, <b><?=number_format($lessons)?></b>堂課, 總金額 <b>$<?=number_format($total)?></b></p>
        <br>-->
        <p class="right" style="font-size:1.2rem;">
          共<b><?=$cnt?></b>筆訂單, <b><?=number_format($lessons)?></b>堂課, 總金額 <b>$<?=number_format($total_price_by_order)?></b><br>
          <!--總學費(不含保險) <b>$<?=number_format($totalFee)?>--></b>
        </p>
      </td>
    </tr>
    <tr>
      <td colspan="4">
                      <?php                 
                      if(isset($_REQUEST['soidx']))    {
                        $status_str['collecting']     = '<font color=#02c736>團員資料填寫中...</font>';
                        //$status_str['submit_request'] = '<font color=#02c736>資料審核中...</font>';
                        $status_str['submit_request'] = '<font color=#02c736>團員資料已填寫齊全</font>';
                        $status_str['allow']          = '<font color=#4287f5>✅保險已核准</font>';
                        $status_str['deny']           = '<font color=#ff0000>🚫保險未核准</font>';
                        $status_str['Y']              = '<font color=#4287f5>✅保險已核准</font>';
                        $status_str['N']              = '<font color=#ff0000>🚫保險未核准</font>';  
                        $status_str['order_canceled']= '<font color=#ff0000>🚫訂單取消</font>';

                        $order_id = $order['oidx']; // 退刷訂單可能取不到$order['schedule'], 
                        $insuranceFUNC = new INSURANCE();
                        $insuranceList = $insuranceFUNC->get_list($order_id);
                        //_v($insuranceList);
                        
                        $c=0;
                        $insurance_total_people=0;
                        echo "<cB>團員保單資料:</cB>";
                        if(count($insuranceList)>0){                                     
                              echo '<table>';
                              echo '<tr><td>姓名</td><td>生日</td><td>曾經上課的天數</td><td>滑雪天數</td><td>連續轉彎</td><td>核保狀態</td></tr>';
                              echo '<font color="#9c9b9a">';
                              foreach ($insuranceList as $key => $value) {
                                $c++;

                                //echo '<tr><td>'.$value['pcname'].'</td><td>'.$value['birthday'].'</td><td>'.$SKI_LEVEL_CLASS_DAYS[$value['c_days']].'</td><td>'.$SKI_LEVEL_PLAY_DAYS[$value['ski_days']].'</td><td>'.$SKI_LEVEL_CONT_TURN[$value['cont_turn']].'</td></tr>';
                                if(!isset($value['c_days']))    $value['c_days']=0;
                                if(!isset($value['ski_days']))  $value['ski_days']=0;
                                if(!isset($value['cont_turn'])) $value['cont_turn']=0;
                                echo '<tr><td>'.$value['pcname'].'</td><td>'.substr($value['birthday'],0,4).'</td><td>'.$SKI_LEVEL_CLASS_DAYS[$value['c_days']].'</td><td>'.$SKI_LEVEL_CLASS_DAYS[$value['ski_days']].'</td><td>'.$SKI_LEVEL_CONT_TURN[$value['cont_turn']].'</td><td>'.$status_str[$value['status']].'</td></tr>';
                                
                                $sec_idx_no = crypto::ev($value['idx']);//_d($sec_idx_no);
                                //echo $sec_idx_no.'<br>';
                                //echo $in['id'].'<br>'
                                if($value['master']=='Y'){
                                  $insurance_total_people = $value['inusrance_num'];
                                }

                                //echo $c.'. '.$value['pcname'].','.$value['birthday'].','.$value['twid'].'<br>';
                                
                              }
                              echo '</font><br>';
                              echo '</table><tr>';

                              if($insurance_total_people  > count($insuranceList)){
                                $remind = $insurance_total_people - count($insuranceList);
                                echo '<font color="#ff0000">注意： 尚有 '.$remind.' 位團員尚未填寫保單！</font><br>';
                              }
                        }else{
                          if($order['insurance_byself']=='Y'){
                            echo '<br><cR>注意： 學生已自行投保！！</cR><br>';
                          }else{
                            echo '<br><cR>注意： 學生尚未填寫任何保險資料！！</cR><br>';
                          }
                        }

                      }
                      ?>
      </td>
    </tr>

    </tbody>
    </table>
    <?  //$multi_instructors_list; ?>
  </main>


      <!--JavaScript at end of body for optimized loading-->
      <!--<script src="https://diy.ski/assets/js/materialize.min.js"></script>-->
      <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-rc.2/js/materialize.min.js"></script>
      <script src="https://diy.ski/assets/js/select_workaround.js"></script>

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
          if(!confirm('你真的確定要回復/取消/退訂?')){
            return false;
          }
        });
      });
      </script> 


    </body>
</html>