<?php
require('../includes/auth.php');
require('../includes/sdk.php');

$filters = array(
    'oidx'			=> FILTER_SANITIZE_STRING,
);//_v($_POST);
$in = filter_var_array(array_merge($_REQUEST,$_POST), $filters);//_v($in);//exit();

$ko = new ko();

$order = $ko->getOneOrderInfo(['oidx'=>$in['oidx']]);//_v($order);exit();
if(count($order)==0) {_d('無效訂單！(NULL)'); exit();}
//$order['detail'] = json_decode($order['detail'], true);//_v($order);exit();
$parkInfo = $ko->getParkInfo();//_v($parkInfo);
$student = $ko->getMembers(['idx'=>$order['student']]);
$student = $student[0];//_v($student);
//header('Content-Type: application/json; charset=utf-8');
//echo json_encode($order, JSON_UNESCAPED_UNICODE);exit();
$distinctInstructors = [];
header('Content-Type: text/html; charset=utf-8');
$order_id = $order['oidx']; // 退刷訂單可能取不到$order['schedule'], 
$insuranceFUNC = new INSURANCE();
$insuranceList = $insuranceFUNC->get_list($order_id);
if ($insuranceList) {
  // $teaching_link="https://teaching.diy.ski/snow/studentLookup.php?oidx=".$order['oidx']."&token=".md5("newdiyski".$order['oidx']);
  $ratingEvaluationURL = 'https://www.withcj.fun/instructor/ratingEvaluation.php';
  $lesson = $order['schedule'][0];
  if (strtotime($lesson['date'])-time() < 3*24*60*60) {
    $last = array_pop($order['schedule']);
    array_push($order['schedule'], $last);
    $students = count($insuranceList);
    $studentInfo = '';
    foreach ($insuranceList as $s) {
      //_v($student);
      $studentInfo .= "@{$s['pcname']}@{$s['email']}";
    }
    $token = md5('newdiyski'.$order_id);
    $ratingEvaluationLink = "<a href=\"{$ratingEvaluationURL}?info={$order_id}@{$lesson['park']}@{$lesson['instructor']}@{$lesson['expertise']}@{$lesson['date']}@{$last['date']}@{$students}{$studentInfo}&token={$token}\" target=\"_blank\">👉教練評量</a>";
  } else {
    $ratingEvaluationLink = '尚未開放評量';
  }
} else {
  $ratingEvaluationLink = '保單尚未填寫無法評量';
}
$firstDate = null;
if ($order['status']===Payment::PAYMENT_CANCELED||
            $order['status']===Payment::PAYMENT_TIMEOUT||
            $order['status']===Payment::PAYMENT_FAILURE||
            $order['status']===Payment::PAYMENT_REFUND) {
  $class_type = '';
}else{
  $class_type = $order['extraInfo']['classType'];
}

?>
			<table class="order">
                <thead>
                  <tr>
                      <td colspan="1">#<?=$order['oidx']?>| <?=$ratingEvaluationLink?></td>
                      <td colspan="3" style="text-align:right;"><?=$class_type?> <?=Payment::STATUS_NAME[$order['status']]?></td>
                  </tr>
                  <tr>
                      <th width="30%"><p class="left">日期<br>時間/堂次</p></th>
                      <th width="40%"><p class="left">雪場<br>教練/種類</p></th>
                      <th width="10%"><p class="left">人數</p></th>
                      <th width="20%"><p class="right">金額</p></th>
                  </tr>
                </thead>
                <tbody>
                <?php foreach ($order['schedule'] as $n => $o) {//_v($o);
                  //算尾款
                  // if(in_array($o['park'], ['nozawa','shiga','hakuba','tsugaike'])){
                  //    $parkInfo[$o['park']]['deposit'] = 6000;
                  // }
                  $_payment = $o['fee'] - $parkInfo[$o['park']]['deposit'] - ($parkInfo[$o['park']]['insurance']*$o['studentNum']);
                  $distinctInstructors[$o['instructor']] = empty($distinctInstructors[$o['instructor']]) ? $_payment : $distinctInstructors[$o['instructor']]+$_payment;
                  $firstDate = empty($firstDate) ? $o['date'] : $firstDate;
                ?>
                  <tr>
              
                    <td><?=substr($o['date'],5)?><br><?=$parkInfo[$o['park']]['timeslot'][$o['slot']]?><sub><?=$o['slot']?>th</sub></td>
                    <td><?=$parkInfo[$o['park']]['cname']?><br>
                      <div class="class">
                        <p><?=ucfirst($o['instructor'])?>/<?=strtoupper($o['expertise'])?></p>
                      </div>
                    </td>
                    <td>
                    	<?=$o['studentNum']?>
                    </td>
                    <td>
                      <p class="price right"><?=number_format($o['fee'])?><sub><?=$order['currency']?></sub></p>
                    </td>
                  </tr>
                  <?php }//foreach ?>
                 <tr>
                   <td rowspan="5" colspan="2">
                    學生備註:<br>
                    <?=$order['requirement']?><br><br>
                    管理備註:<br>
                    <cR><?=$order['note']?></cR><br>
                    </td><td>學費</td><td><p class="price right"><?=number_format($order['price'])?><sub><?=$order['currency']?></sub></p></td>
                 </tr>
                 <tr>
                   <td>折扣</td><td><p class="price right"><?=number_format(($order['discount']-$order['specialDiscount']))?><sub><?=$order['currency']?></sub></p></td>
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
                  <td colspan="4" style="text-align:right;"><cR>各別尾款</cR> ｜ <span style="font-weight:bold;">
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
                 <tr>
                   <td colspan="4">學生：<?=strtoupper($student['name'])?> 聯絡資訊<br>
                    <?php if( (strtotime($firstDate)-(8*7*24*60*60)) <= time() ){ ?>
                    <?=$student['email']?>, +<?=$student['country']?> <?=$student['phone']?><br>
                    FB: <?=$student['fbid']?>, Line ID: <?=$student['line']?>, WeChat: <?=$student['wechat']?><br>
                    <?php }else{ ?>
                      <cB>上課前一個月開放學生聯絡資訊.</cB>
                    <?php } ?>

                      <?php
                        $c=0;
                        $insurance_total_people=0;
                        
                        if(count($insuranceList)>0 && isset($last)){
                          echo "<br><br><cB>團員保單資料:</cB>";
                          echo '<table>';
                          echo '<tr><td>姓名</td><td>生日</td><td>曾經上課的天數</td><td>滑雪天數</td><td>連續轉彎</td><td>教學輔助連結</td></tr>';
                          echo '<font color="#9c9b9a">';
                          foreach ($insuranceList as $key => $value) {
                            $c++;

                            //echo '<tr><td>'.$value['pcname'].'</td><td>'.$value['birthday'].'</td><td>'.$SKI_LEVEL_CLASS_DAYS[$value['c_days']].'</td><td>'.$SKI_LEVEL_PLAY_DAYS[$value['ski_days']].'</td><td>'.$SKI_LEVEL_CONT_TURN[$value['cont_turn']].'</td></tr>';
                            if(!isset($value['c_days']))    $value['c_days']=0;
                            if(!isset($value['ski_days']))  $value['ski_days']=0;
                            if(!isset($value['cont_turn'])) $value['cont_turn']=0;

                            $recordUrl = 'https://www.withcj.fun/snow/teachingRecord.php';
                            $token = md5('newdiyski'.$order['oidx']);
                            $recordLink = "<a href=\"{$recordUrl}?info={$order['oidx']}@{$lesson['park']}@{$lesson['instructor']}@{$lesson['expertise']}@{$lesson['date']}@{$last['date']}@{$students}{$studentInfo}&token={$token}\" target=\"_blank\">👉教學輔助系統</a>";

                            echo '<tr><td>'.$value['pcname'].'</td><td>'.substr($value['birthday'],0,4).'</td><td>'.$SKI_LEVEL_CLASS_DAYS[$value['c_days']].'</td><td>'.$SKI_LEVEL_CLASS_DAYS[$value['ski_days']].'</td><td>'.$SKI_LEVEL_CONT_TURN[$value['cont_turn']].'</td><td>'.$recordLink.'</td></tr>';
                            
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
                          echo '<br><cB>注意： 學生尚未填寫任何保險資料！！</cB><br>';
                        }
                      ?>


                   </td>
                 </tr>
                </tbody>
              </table>