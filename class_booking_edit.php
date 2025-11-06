<?php
require('includes/sdk.php');    
if(isset($_SESSION['account'])){
      $ACCOUNT = new MEMBER();
      $account_info['email'] = $_SESSION['account'];  
      $R=$ACCOUNT->get_account($account_info);
}else{
      $arg['order_id']   = isset($_REQUEST['id']) ? $_REQUEST['id']:'';  
      _go('account_login.php?from=MYORDER&id='.urlencode($arg['order_id'])); 
      //_go('account_login.php');
}
//echo 'id='.$_REQUEST['id'];
$filters = array(
    'act'         => FILTER_SANITIZE_STRING,   
    'ino'         => FILTER_SANITIZE_STRING,    
    'id'          => FILTER_SANITIZE_STRING,
    'payment'     => FILTER_SANITIZE_STRING,
);//_v($_POST);
$in = filter_var_array(array_merge($_REQUEST,$_POST), $filters);//_v($_POST['payment']);exit();


$ko = new ko();
$insuranceFUNC = new INSURANCE();
$orderFUNC = new ORDER();

$instructorInfo = $ko->getInstructorInfo();
$parkInfo = $ko->getParkInfo();//_v($parkInfo);exit();
$order_id = crypto::dv($in['id']);
//echo 'order_id='.$order_id.'<br>';
// read order info
$order = $ko->getOneOrderInfo(['oidx'=>$order_id]);//_v($order);exit();
// 取得保單資料
$insuranceList = $insuranceFUNC->get_list($order_id);


if(empty($order['oidx'])){
  echo $order_id.'Access error!!';
  exit();
}

// 保單處理 =================================================================
$insuranceFUNC = new INSURANCE();// 保單處理

//_d('debug');
if($in['act']=='iupdate' && !empty($in['ino'])){
  if(0){ // debug
    _d($_REQUEST['ino']);
    _d(urldecode($_REQUEST['ino']));
    _d($in['ino']);
  }
  $del_no = crypto::dv($in['ino']);  // 避免browser 自動補 urlencode; ex: = --> %3d
  if(is_numeric($del_no)){
    //echo "oidx:".$order_id.'<br>';
    //echo "del:".$del_no;
    $insuranceFUNC->delete($del_no);
    $insuranceList = $insuranceFUNC->get_list($order_id);
  }else{
    // 解密錯誤
    if(0){
    _d($_REQUEST['ino']);
    _d(urldecode($_REQUEST['ino']));
    _d($in['ino']);    
    echo "error:".$del_no;
    }
  } 
}
if($in['act']=='isubmit'){
  //echo 'update'.$order_id;
  $insuranceFUNC->update_status_by_oidx($order_id, 'submit_request');  // 送核
  $order = $ko->getOneOrderInfo(['oidx'=>$order_id]); // reload
}
if($in['act']=='iself'){  // 自行投保
  $idata['insurance_byself'] = 'Y';
  $orderFUNC->update($order_id,$idata); // 自行投保
  $order = $ko->getOneOrderInfo(['oidx'=>$order_id]); // reload
}
if($in['act']=='anti_iself'){  // 取消自行投保
  $idata['insurance_byself'] = 'Cancel';
  $orderFUNC->update($order_id,$idata); // 取消自行投保
  $order = $ko->getOneOrderInfo(['oidx'=>$order_id]); // reload
}


$query_arry['type']     ='OIDX_M';
$query_arry['oidx']     = $order_id;
$insuranceData        = $insuranceFUNC->get_list_by_query($query_arry);  //_v($insuranceData);  
if(count($insuranceData)==1){
  $insuranceResult = $insuranceData[0];
}else{
  $insuranceResult['inusrance_num']     = 0;
  $insuranceResult['status'] = 'init';  
}

//_v($insuranceResult);
//echo $insuranceResult['inusrance_num'];
// 保單處理 ================================================================= end

if($in['act']=='edit'){//update student
  $ko->log([
    'severity'  =>  'orderChangeStudent',
    'user'      =>  $R['idx'],
    'oidx'      =>  $order_id,
    'resp'      =>  json_encode($order, JSON_UNESCAPED_UNICODE),
    'msg'       =>  $_POST['payment'],
  ]);
  $payment = json_decode($_POST['payment'], true);
  $lessons = $payment['lessons'];
  //處理JS小數點
  $payment['paid'] = round($payment['paid']);//_j($payment);exit();

  //改人數
  foreach ($lessons as $s) {
    $ko->updateSchedule([
      'studentNum'=> $s['students'],
      'fee'       => $s['fee'],
    ],['sidx'=>$s['sidx']]);
  }
  //改訂單
  $ko->updateOrder([
    'price'   => $payment['price'],
    'discount'=> $payment['discount'],
    'payment' => $payment['payment'],
  ],['oidx'=>$order_id]);

  //重新讀取訂單
  $order = $ko->getOneOrderInfo(['oidx'=>$order_id]);

  //發通知信
  $ko->notify([
    'oidx'            => $order_id,
    'type'            => 'orderChangeStudent',
    'createDateTime'  => date('Y-m-d H:i:s'),
  ]);
}


//echo $order['status'];
//$order['detail'] = json_decode($order['detail'], true);    
$orderSheet = $order['schedule']; 
//_v($orderSheet);

$lastDate = end($orderSheet);//_v($lastDate);
reset($orderSheet);
$disabled = (strtotime($lastDate['date'])<=time()) ? 'disabled' : '';
//echo $order_id;
//echo  '0'.$disabled;
if($order_id==8721 || $order_id==8429 || $order_id==9834 || $order_id==9871 || $order_id==9875){
  $disabled = 'disabled'; // 因教練更新Levelfee 造成有些訂單費用受影響，針對已手動調整過的訂單，限制不可在修改
} 
if($order['specialDiscount']!=0 || $order['lock']=='Y' || $order['lock']=='sars'){
  //if($order_id!=9037) //debug
  $disabled = 'disabled'; 
  //1. specialDiscount 不為0的就鎖定.
  //2. 在新增DB一欄位紀錄不能改人數的訂單.
}

$debug=0;
if($debug==1 && ($order_id==11440 || $order_id==10705) ) {
  $disabled = ''; //echo 'debug';
  //_v($orderSheet);
}// for debug

?>
<!DOCTYPE html>
  <html>
    <head>
      <?php require('head.php'); ?>
      <script src='https://kit.fontawesome.com/a076d05399.js'></script>
    </head>

    <body>
      <header>
      <?php require('nav.inc.php');?>
      </header>


      <main>
        <div class="container-fuild">
          <a href="javascript:" id="return-to-top" class="waves-effect waves-light"><i class="material-icons">arrow_upward</i></button></a>

          
          <div class="row header-block-login">
            <div class="header-img-bottom">
              <img src="assets/images/header_img_bottom.png" alt="">
            </div>
            <img src="assets/images/header_login_main_img.jpg"> 
            <div class="col s10 push-s1  m6 push-m3  header-block-content">
                <p class="text-center"></p>
                <p class="resort-name">訂單資訊 <span></span></p>
                <p><?php echo $R['name']; ?></p>               
                <button id="myorder"  class="btn waves-effect waves-light btn-primary space-top-2" >回訂單列表 <i class="material-icons">exit_to_app</i></button>
              
                
              </div> 
          </div> 


        <!--class table-->
         <div class="row container-xl">
           <div class="col s12 m10 col-centered">
              <form action="?act=edit" method="post" id="paymentForm">
              <input type="hidden" name="payment" id="paymentData">
              <input type="hidden" name="id" value="<?=$in['id']?>">              
              <table class="booking-table">
                <tbody>
                  <?php if(empty($order['gidx'])){ ?>
                  <tr>
                      <th width="25%"><p class="left">日期<br>時間/堂次</p></th>
                      <th width="30%"><p class="left">雪場<br>教練/種類</p></th>
                      <th><p class="center">人數</p></th>
                      <th width="20%"><p class="right">金額</p></th>
                  </tr>
                  <?php }else{
                      $groupOrderInfo = $ko->getGroupOrderInfo($order['gidx']);
                  ?>
                  <tr>
                      <td colspan="4">
                        <h5>課程說明</h5>
                        <?php
                          $R['name'] = str_replace(' ','',$R['name']);
                          $teaching_link="https://".teaching_domain_name."/snow/studentLookup.php?email=".$R['email']."&name=".$R['name']."&token=".md5($R['email'].$R['name']);
                          $teaching = " <a href=\"{$teaching_link}\">👉教學紀錄</a>";
                        ?>
                        <a href="<?=$teaching?>"> >>教學查詢<< </a>
                        <p><?=($groupOrderInfo['group']['content'])?></p>
                      </td>
                  </tr>
                  <?php } ?>

                
                <?php foreach ($orderSheet as $n => $o) { ?>
                  <tr>
              
                    <td><p class="date"><?=substr($o['date'],5)?></p><?=$parkInfo[$o['park']]['timeslot'][$o['slot']]?><br><span class="badge badge-gray"><?=$o['slot']?>th</span></td>
                    <td><?=$parkInfo[$o['park']]['cname']?><br>
                      <div class="class">
                        <div class="class-d">
                          <div class="avatar-img">
                            <img src="https://diy.ski/photos/<?=$o['instructor']?>/<?=$o['instructor']?>.jpg" alt="">
                          </div>
                          <p><?=$o['instructor']?></p>
                        </div>
                        <span class="badge badge-gray"><?=strtoupper($o['expertise'])?></span>
                      </div>
                    </td>
                    <td>
                      <select <?=$disabled?> id="<?=$n?>" class="lesson" sidx="<?=$o['sidx']?>" date="<?=$o['date']?>" slot="<?=$o['slot']?>" park="<?=$o['park']?>" instructor="<?=$o['instructor']?>" expertise="<?=$o['expertise']?>" ruleId=0>
                        <option value="1" <?=($o['studentNum']==1)?'selected':'' ?> >1</option>
                        <option value="2" <?=($o['studentNum']==2)?'selected':'' ?> >2</option>
                        <option value="3" <?=($o['studentNum']==3)?'selected':'' ?> >3</option>
                        <option value="4" <?=($o['studentNum']==4)?'selected':'' ?> >4</option>
                        <option value="5" <?=($o['studentNum']==5)?'selected':'' ?> >5</option>
                        <option value="6" <?=($o['studentNum']==6)?'selected':'' ?> >6</option>
                      </select>
                    </td>
                    <td class="right" style="display: flex"><div id="fee<?=$n?>"><p class="price" style="align-items:center;"><?=number_format($o['fee'])?><span class="badge badge-primary"><?=$order['currency']?></span></p><br><?=$o['noshow']?'已取消課程':''?><?=($order['lock']=='sars')?'已延期':''?></div></td>
                  </tr>
                  <?php }//foreach ?>

                 <tr>
                   <td colspan="4">
                    <?php
                    
                      $status_str['init']           = "<font color=#ff0000>🚫 您尚未填寫任何資料! (請點擊上方<i style='font-size:24px' class='far'>&#xf328;</i>圖示進行保單填寫)</font>";
                      $status_str['collecting']     = '<font color=#02c736>團員資料填寫中...</font>';
                      //$status_str['submit_request'] = '<font color=#02c736>資料審核中...</font>';
                      $status_str['submit_request'] = '<font color=#02c736>團員資料已填寫齊全</font>';
                      $status_str['allow']          = '<font color=#4287f5>✅保險已核准</font>';
                      $status_str['deny']           = '<font color=#ff0000>🚫保險未核准</font>';
                      $status_str['Y']              = '<font color=#4287f5>✅保險已核准</font>';
                      $status_str['N']              = '<font color=#ff0000>🚫保險未核准</font>';  
                      $status_str['order_canceled']= '<font color=#ff0000>🚫訂單取消</font>';
                      if($insuranceResult['inusrance_num']==count($insuranceList))  $status_str['collecting']     = '<font color=#02c736>所有團員資料已填寫完成！（ 若是資料核對無誤，請點選下方送出核保按鈕 ）</font>';              
                      //if($_SESSION['account'] == 'ericko@inn-com.tw' || $_SESSION['account']=='liligogo523@gmail.com'){
                      if(1){  

                    ?>
                    
                   
                    <?php
                      $insurance_status = $insuranceFUNC->check_order_status($order['oidx']); 
                      if(isset($insuranceResult) && ($insuranceResult['status']=='Y' || $insuranceResult['status']=='allow' || $insuranceResult['status']=='order_canceled' )){
                        // show nothing for pass user or canceled order
                        echo '<H5>保單資訊</H5>'; // 已填寫完成
                        $status_label = $INSURANCE_STATUS_LABEL[$insurance_status];
                      }else{
                        if($order['insurance_byself']!='Y' ){ // skidiy 投保
                            $status_label = $INSURANCE_STATUS_LABEL[$insurance_status];      
                            //echo mj;                                          
                    ?>  
                     <H5>保單填寫</H5>
                      訂單本人保單填寫： 👉 <a href="insurance_apply.php?id=<?=urlencode($in['id']);?>" rel="nofollow" ><i style='font-size:24px' class='far'>&#xf328;</i></a>
                      （ 提醒您：若您需修改保單人數，可於左方您的保單中的「被保團員人數」欄裡更新唷～ ）<br>
                    <?php
                        }else{ // end of if($order['insurance_byself']!='Y' )
                          echo '<H5>保單資訊</H5>'; // 自行投保
                          $status_label = '您已選擇自行投保';
                        }
                      }     
                                  
                    ?>
                      <!--狀態： <?=$status_str[$insuranceResult['status']]; ?> ｜  -->
                      狀態： <font color=#4287f5><?=$status_label;?></font> 
                    <?php
                    /*
                        if($insurance_status==INSURANCE_STATUS_NULL_DATA && $order['oidx']==9146){

                        }
                        */
                        if($order['insurance_byself']=='Y' ){
                          echo '<br><button id="anti_insuranceSelfBT"  class="btn waves-effect waves-light btn-primary space-top-2 modal-trigger" type="button" name="action">取消自行投保！ <i class="material-icons">chevron_right</i></button><br>';  
                        }elseif($insurance_status==INSURANCE_STATUS_NULL_DATA ){
                          echo '<br><button id="insuranceSelfBT"  class="btn waves-effect waves-light btn-primary space-top-2 modal-trigger" type="button" name="action">不了！謝謝，我可自行投保！ <i class="material-icons">chevron_right</i></button><br>';                            
                        }

                        if($insuranceResult['inusrance_num']>0){
                    ?>
                        ｜ 應填寫資料： <?= $insuranceResult['inusrance_num']; ?> 位 ｜ 
                        已填寫資料： <?=count($insuranceList)?> 位 <br>
                        注意：所有保單資料將於上課前一週進行投保．
                    <?php                
                        }
                        //echo count($insuranceList);
                      }
                    ?>    
                      <br>

                      <?php
                        //_v($insuranceList);
                        $utility_func = new UTILITY();
                        $c=0;
                        //if((count($insuranceList)>0) && ($_SESSION['account'] == 'ericko@inn-com.tw' || $_SESSION['account'] == 'yihui.chen17@gmail.com' || $_SESSION['account']=='liligogo523@gmail.com')){
                        if(isset($insuranceList)){  
                          //echo 'xxxxxx';
                          echo '<font color="#9c9b9a">';
                          foreach ($insuranceList as $key => $value) {
                            $c++;
                            $sec_idx_no = crypto::ev($value['idx']);//_d($sec_idx_no);
                            //echo $sec_idx_no.'<br>';
                            //echo $in['id'].'<br>';
                            $note = '';  
                            if($insuranceResult['status']=='Y' || $insuranceResult['status']=='allow' || $insuranceResult['status']=='order_canceled' ){
                                // show nothing for pass user or canceled order                                              
                              if($value['status']=='Y' || $value['status']=='allow') $note.=' - 已核保 ✅';
                              if($value['master']=='Y') $note.=' (訂單本人)';
                              echo '姓名：'.$value['pcname'].' 生日：'.$value['birthday'].' 身份證：'.$utility_func->mask($value['twid'],null,strlen($value['twid'])-5).$note.'<br>';
                            }else{
                              if($value['master']=='Y'){
                                echo $c.'. <i class="material-icons">delete</i> ';
                                echo '<a href="insurance_apply.php?id='.urlencode($in['id']).'"><i class="material-icons">edit</i></a> ';                                
                                if($value['status']=='Y' || $value['status']=='allow') $note .=' - 已核保 ✅';
                                $note .=' (訂單本人)';
                              }else{
                                echo $c.'. <a href="?id='.urlencode($in['id']).'&act=iupdate&ino='.$sec_idx_no.'"><i class="material-icons">delete</i></a> ';
                                echo '<a href="insurance_fapply.php?m=mm&id='.urlencode($in['id']).'&qid='.$sec_idx_no.'"><i class="material-icons">edit</i></a> ';
                                if($value['status']=='Y' || $value['status']=='allow') $note.=' - 已核保 ✅';
                              }
                              $value['pcname'] = str_replace(' ','',$value['pcname']);
                              $teaching_link="https://".teaching_domain_name."/snow/studentLookup.php?email=".$R['email']."&name=".$value['pcname']."&token=".md5($R['email'].$value['pcname']);
                              $teaching = " <a href=\"{$teaching_link}\">👉教學紀錄</a>";
                              // CJ 評量系統
                              $selfEvaluationUrl = 'https://www.withcj.fun/instructor/selfEvaluation.php';
                              $token = md5('newdiyski'.$value['pcname']);
                              $selfEvaluationLink = "<a href=\"{$selfEvaluationUrl}?info={$value['pcname']}@{$value['email']}@{$order['schedule'][0]['expertise']}&token={$token}\" target=\"_blank\">&nbsp;&nbsp;&nbsp;👉課前請填自我評量</a>";
                              $evaluationUrl = 'https://www.withcj.fun/instructor/evaluation.php';
                              $token = md5('newdiyski'.$order_id);
                              $evaluationLink = "<a href=\"{$evaluationUrl}?info={$order_id}@{$value['pcname']}@{$value['email']}&token={$token}\" target=\"_blank\">&nbsp;&nbsp;&nbsp;👉課後教練評量</a>";

                              if(strtotime($lastDate['date'])<=time()){
                                $recordUrl = 'https://www.withcj.fun/snow/teachingRecord.php';
                                $token = md5('newdiyski'.$order_id);
                                $recordLink = "<a href=\"{$recordUrl}?info={$order_id}@{$value['pcname']}@{$value['email']}&token={$token}\" target=\"_blank\">&nbsp;&nbsp;&nbsp;👉教學輔助系統{$lastDate['date']}</a>";
                              }else{
                                $recordLink = '';
                              }

                              //echo '姓名：'.$value['pcname'].' 生日：'.$value['birthday'].' 身份證：'.$utility_func->mask($value['twid'],null,strlen($value['twid'])-5).$note.$teaching.'<br>';
                              echo '姓名：'.$value['pcname'].' 生日：'.$value['birthday'].' 身份證：'.$utility_func->mask($value['twid'],null,strlen($value['twid'])-5).$note.$selfEvaluationLink.$evaluationLink.$recordLink.'<br>';
                            }
                          }
                          echo '</font><br>';

                          if($insuranceResult['inusrance_num']  > count($insuranceList)){
                            $remind = $insuranceResult['inusrance_num'] - count($insuranceList);
                            echo '<font color="#ff0000">注意： 尚有 '.$remind.' 位團員尚未填寫保單！</font><br>';
                            echo '您可代為填寫 <a target="_blank" href="https://diy.ski/insurance_fapply.php?id='.urlencode($in['id']).'&m=ma" rel="nofollow" ><i class="material-icons">note_add</i></a> 或是，';
                            echo '請將投保鏈結分享給上課的每一個學員 <a target="_blank" href="http://line.naver.jp/R/msg/text/?SKIDIY保單填寫%0D%0Ahttps://diy.ski/insurance_fapply.php?id='.urlencode(urlencode($in['id'])).'" rel="nofollow" ><img height="25" src="assets/images/lineshare.png"></a><br>
                        並請於上課前兩週完成填寫，以利後續投保作業．<br><br>';
                          }

                          if($insuranceResult['status']=='Y' || $insuranceResult['status']=='allow' || $insuranceResult['status']=='order_canceled' ){
                              // 已通過核保 不提供編輯
                          //}else if($insuranceResult['inusrance_num'] >0  && ($insuranceResult['inusrance_num'] == count($insuranceList)) && $insuranceResult['status']=='collecting'){
                          }else if( $insuranceResult['inusrance_num'] == count($insuranceList) 
                            && ($insuranceFUNC->check_order_status($order_id)==INSURANCE_STATUS_COLECTING_DONE )                             
                          ) {  
                            echo '<button id="insuranceSummitBT"  class="btn waves-effect waves-light btn-primary space-top-2 modal-trigger" type="button" name="action">以上資料確認無誤，送出核保 <i class="material-icons">chevron_right</i></button><br>';
                          }
                        


                        }else{
                          //echo '.';
                        }
                      ?>
                      <?php
                          if($order['insurance_byself']!='Y' ){
                      ?>
<strong>提醒您：</strong> 如果您的行程本預定投保富邦產物旅平險，因為同一保險公司規定無法重複加保，
                      在我們投保後，您自行加保的保單會被富邦系統擋下，本次行程請改用「富邦產險以外」的旅平險。 <br>     
<a href="https://www.fubon.com/insurance/b2c/content/travel_coverage/index.html"><strong>>> 富邦旅平險相關條款可參考這唷 <<</strong></a><br>                                    
<br>
保險連絡窗口 : <br>
富邦人壽 - 謝淑君（Jasmine)<br>
Line ID : jasmine082077<br>
E-mail : jasmine082077@gmail.com<br>
Fax : (02)6608-7188<br>
<br>
                      <?php
                          }
                      ?>
                    <h5>費用總計</h5>
                     <div class="row sum-block">
                        <div class="col s12">
                          <div class="card-panel">
                            <div class="row flex-stretch">
                              <div class="col s12 m3 payment-sum">
                                <p class="col s4 m12"><i class="material-icons">ac_unit</i><br>學費總計</p>
                                <div id="price"><p class="num col s8 m12"><?=number_format($order['price'])?><small class="badge badge-primary"><?=$order['currency']?></small></p></div>
                                
                              </div>
                              <div class="col s12 m3 payment-sum">
                                <p class="col s4 m12"><i class="material-icons">card_giftcard</i><br>折扣優惠</p>
                                <div id="discount"><p class="num col s8 m12"><?=number_format($order['discount']-$order['specialDiscount'])?><small class="badge badge-primary"><?=$order['currency']?></small></p></div>
                                
                              </div>
                              <div class="col s12 m3 payment-sum">
                                <p class="col s4 m12"><i class="material-icons">payment</i><br>刷卡金額<br>預付訂金<br><small id="exchangeRate" class="font-primary">(匯率:<?=$order['exchangeRate']?>)</small></p>                              
                                <div id="prepaid" class="cR"><p class="num col s8 m12"><?=$order['prepaid']?><small class="badge badge-primary"><?=$order['currency']?></small></p></div>
                                <p class="col s4 m12"></p>
                                <div id="paid"><p class="num col s8 m12"><?=number_format($order['paid'])?><small class="badge badge-primary">NTD</small></p></div>
                                
                              </div>
                              <div class="col s12 m3 payment-sum">
                                <p class="col s4 m12"><i class="material-icons">attach_money</i><br>上課尾款</p>
                                <div id="payment"><p class="num col s8 m12"><?=number_format(($order['payment']+$order['specialDiscount']))?><small class="badge badge-primary"><?=$order['currency']?></small></p></div>
                                
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="row space-top-2">
                        <div class="input-field col s12 m12">說明事項：<br><?=$order['requirement']?></div>
                      </div>
                      <?php if(!empty($order['note'])){ ?>
                      <div class="row">
                        <div class="input-field col s12 m12" style="color:blue;">管理員備註：<br><?=$order['note']?></div>
                      </div>
                      <?php } ?>
                    </td>
                  </tr>
                </tbody>
              </table>
              </form>
            </div>

            <div class="row space-top-2 center">
            <div class="row">
              <div class="col s12 center-align">
              <h5>訂課注意事項</h5>
                <p class="text-left">尾款請準備日幣現金在上課時交給教練。</p>
                <p class="text-left">若於上課期間無故曠課，將沒收訂金賠償教練損失，除非提供相關證明，因天災、意外原因，非故意曠課，才會退還訂金。</p>
                <p class="text-left">此為自助行程，請提早在上課時間前抵達，以免影響上課時間，教練會按照時間準時上下課。</p>
                <p class="text-left">預定課程完成後若預取消，需遵守以下列條款。</p>
                <p class="text-left">&nbsp;&nbsp;&nbsp;🚨2個月前取消，訂金全額退費；</p>
                <p class="text-left">&nbsp;&nbsp;&nbsp;🚨1個月前取消，退還50%訂金；</p>
                <p class="text-left">&nbsp;&nbsp;&nbsp;🚨1個月內取消，訂金不退還。<br>(以上退還金額需扣除刷卡金額3%手續費後轉帳退回)</p>
              </ol>
              </div>
            </div>
            </div>
            <div class="row space-top-2 center">
            <div class="row">
              <div class="col s12 center-align">
                <?php if(empty($order['gidx'])&&empty($disabled)){ ?>
                  <button class="btn btn-primary modal-trigger" data-target="terms">修改上課人數 <i class="material-icons">keyboard_arrow_right</i></button>
                <?php } ?>
                <hr>
                <p class="text-left" style="color: blue;">＊若您欲變更上課日期/教練/雪場/SKI/SB還需要您寄信至 admin@diy.ski 我們會協助調整。</p>
                <br>
                <p class="text-left" style="color: blue;">＊若您因行程規劃或其它因素無法如期上課，請點擊下方『申請訂單取消』我們將進行退款作業。</p>
                <?php 
                  $class_date = $orderFUNC->schedule_class_date($order_id);
                  //echo $order_id.':'.$class_date;
                  if($order['status'] == 'success' &&  ($class_date >date('Y-m-d') ) ){ 

                ?>
                <button id="cancleBtn"  class="btn waves-effect waves-light space-top-2" >申請訂單取消 <i class="material-icons">exit_to_app</i></button>
                <?php } ?>
                 
              </div>
            </div>
            </div>
         </div>        
      </main>
      
      
      <footer>
        <div class="footer-copyright">
          <p class="center-align">© 2018 diy.ski</p>
        </div>
      </footer>

      <!-- Modal -->
      <div id="terms" class="modal modal-fixed-footer">
        <div class="modal-content">
          <div class="row center">
            <div class="col s11 col-centered">
              <i class="material-icons">error_outline</i>
              <h4>學生人數異動提醒</h4>
            </div>
          </div>
          <div class="row">
            <div class="col s11 col-centered">
              <h5>保險資料</h5>
              <p>有新加入的學員，請別忘了回訂單列表填寫保險資料喔！</p>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <div class="row">
            <label>
              <input type="checkbox" id="read"/>
              <span class="font-primary">我已瞭解</span>
            </label>
          </div>
          <button data-target="success-msg" class="waves-effect btn btn-primary align-center" id="ordereditBtn">確認並修改 <i class="material-icons">navigate_next</i></button>
        </div>
      </div>
      

      
      <!--JavaScript at end of body for optimized loading-->
      <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-rc.2/js/materialize.min.js"></script>
      
      <!--custom js-->
      <!--<script src="assets/js/custom.js"></script>-->

      <script src="skidiy.data.php"></script>
      <script src="skidiy.func.php?v<?=rand(1111,999999999)?>"></script>
      <script>
      function _d(d){console.log(d)}
      function _a(a){alert(a)}


      $(document).ready(function(){
        $('.sidenav').sidenav();
        $('.modal').modal();
        $('select').formSelect();

        <?php if(empty($order['gidx'])){ ?>
        $('.lesson').on('change', function(){        
          //calculateOrder(<?=$order['prepaid']?>);
          calculateOrder(<?=$order['prepaid']?>,<?=$order['specialDiscount']?>);
        });

        <?php if($order_id==8721 || $order_id==8429 || $order_id==9834 ||  $order_id==8502 || $order_id==9942 || $order_id==9261 || $order_id==9204  || $order['lock']=='Y' || $order['lock']=='sars' ){ 
        // #9204 加價  
        // 教練調整 Level fee, 受影響訂單 workaround 或是 課程手動加價 （如單堂整日上課價位不同）
        // read from DB directly  
        // don't calculate again for exception case  

        ?>
        <?php }else{ ?>   
        calculateOrder(<?=$order['prepaid']?>,<?=$order['specialDiscount']?>);
        <?php } ?>
        <?php } ?>
        
        $('#ordereditBtn').on('click', function(){
            if($('#read').prop('checked')){
              paymentForm.submit();
            }else{
              _a('請勾選我已瞭解，才可進行修改喔～');
            }
        });
        
        $('#cancleBtn').on('click', function(e){
            //if(confirm("確定申請訂單取消嗎？\n退款金額需扣除刷卡金額3%手續費後轉帳退回！\n還請Email您的退款帳號資訊至 admin@diy.ski，謝謝！")){
            if(confirm("確定申請訂單取消嗎？\n退款金額需扣除刷卡金額3%手續費後轉帳退回！")){
              window.location.replace('my_order_list.php?act=cancel&id=<?=urlencode($_REQUEST['id'])?>');
            }else{
              return false;
            }
        });

        $('#myorder').on('click', function(e){        
            window.location.replace('my_order_list.php');
        }); 


        $('#insuranceSummitBT').on('click', function(e){
            if(confirm('確定送出保單資料嗎？')){
              window.location.replace('?act=isubmit&id=<?=urlencode($_REQUEST['id'])?>');
            }else{
              return false;
            }
        });

        $('#insuranceSelfBT').on('click', function(e){
            if(confirm('您是否確定本次保單將自行投保？')){
              window.location.replace('?act=iself&id=<?=urlencode($_REQUEST['id'])?>');
            }else{
              return false;
            }
        });      

        $('#anti_insuranceSelfBT').on('click', function(e){
            if(confirm('取消自行投保，並改由 SKIDIY 所提供之核保服務 ？')){
              window.location.replace('?act=anti_iself&id=<?=urlencode($_REQUEST['id'])?>');
            }else{
              return false;
            }
        }); 
          
        /*
        $('#insuranceSummitBT').on('click', function(e){         
                e.preventDefault();
                $.ajax({
                    //url: "account_info.php?act=up_2fcheck",
                    url: "post-cgi.php?cmd=INSUSUMMIT",                    
                    type: "POST",
                    data: $('#insurance-form').serialize(),                   
                    success: function(resp){
                        //alert("Successfully submitted."+resp)
                            if(resp==101005){ // user profile verify & MAIL CHECK PASS
                                $('#email_verify').modal('open');
                            }else if(resp==101008){ // user profile save ok                              
                                 $('#success-msg').modal('open');                                 
                            }else if(resp==100006){
                                //$('#ERRMSG').text('資料填寫不完整');
                                $('#PERRMSG').text('資料填寫不完整');
                                $('#err_msg').modal('open'); 
                            }else{
                                $('#err_msg').modal('open');                                
                            }                         
                    }
                });
        });      
        */  
        

      });
      </script>      


    </body>
  </html>