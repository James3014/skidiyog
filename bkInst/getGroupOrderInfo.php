<?php
require('../includes/auth.php');
require('../includes/sdk.php');

$filters = array(
    'gidx'			=> FILTER_SANITIZE_STRING,
);//_v($_POST);
$in = filter_var_array(array_merge($_REQUEST,$_POST), $filters);//_v($in);//exit();

$ko = new ko();
$info = $ko->getGroupOrderInfo($in['gidx']);//_v($info);//exit();

header('Content-Type: text/html; charset=utf-8');
$students_info="";
?>
			        <table class="order">
                <thead>
                  <tr>
                      <td colspan="4"><b><?=$info['group']['title']?></b> @ <?=ucfirst($info['group']['park'])?> / <?=strtoupper($info['group']['expertise'])?></td>
                  </tr>
                  <tr>
                      <td colspan="4">
                        <?=$info['group']['start']?> ~ <?=$info['group']['end']?> 
                        目前共<span style="color:red;"><?=sizeof($info['students'])?></span>位報名
                      </td>
                  </tr>
                  <tr style="background-color: #ffcc00">
                      <th width="20%"><p class="left">#訂單編號</p></th>
                      <th width="20%"><p class="left">學生名稱</th>
                      <th width="60%"><p class="left">學生聯絡資訊</p></th>
                  </tr>
                </thead>
                <tbody>
                <?php foreach ($info['students'] as $n => $s) {$n++;//_v($s); ?>
                  <tr style="border-bottom:0px !important; border-top:1px solid #AAA;">
                    <td>#<?=$s['oidx']?></td>
                    <td><b><?=$s['name']?></b></td>
                    <td rowspan="2">
                      <?=empty($s['email'])?null:"{$s['email']}"?>
                      <?=empty($s['phone'])?null:"<br>Phone: {$s['phone']}"?>
                      <?=empty($s['line'])?null:"<br>LINE: {$s['line']}"?>
                      <?=empty($s['fbid'])?null:"<br>FBID: {$s['fbid']}"?>
                      <?=empty($s['wechat'])?null:"<br>WeChat: {$s['wechat']}"?>
                    </td>
                  <tr style="border-bottom:0px !important;">
                    <td style="color:blue;"><sub>訂金</sub><br><b><?=number_format($s['prepaid'])."</b><sub>{$s['currency']}</sub>"?></td>
                    <td style="color:blue;"><sub>尾款</sub><br><b><?=number_format($s['payment'])."</b><sub>{$s['currency']}</sub>"?></td>
                  </tr>
                  <?php 
                      if($n==1){
                        $hash = $s['oidx'];
                        $students_info = $s['oidx'].'@'.$s['name'];
                      }else{
                        $students_info .= '@'.$s['oidx'].'@'.$s['name'];
                      }
                    }//foreach 
                    //echo $students_info;
                  ?>
                  <tr style="background-color: #ffcc00">
                    <td colspan="4">課程內容</td>
                  </tr>
                  <tr>
                    <td colspan="4"><pre><?=$info['group']['content']?></pre></td>
                  </tr>
                  <tr>
                  <?php                    
                    $detail_info = ucfirst($info['group']['park']).'@'.ucfirst($info['group']['instructor']).'@'.strtoupper($info['group']['expertise']).'@'.$info['group']['start'].'@'.$info['group']['end'].'@'.sizeof($info['students']).'@'.$students_info;
                    $teaching_link="https://teaching.diy.ski/snow/teachingRecord.php?info=".$detail_info."&token=".md5("newdiyski".$hash);
                    //echo $detail_info;
                  ?>                    
                    <td colspan="4"><a target="_blank" href="<?=$teaching_link?>">Teaching </a></td>
                  </tr>  
                </tbody>
              </table>
              <!--<button class="btn btn-primary right" id="cancelGroupBtn"><i class="material-icons">delete</i> 取消團體課</button>-->

