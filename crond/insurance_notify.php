<?php
require('/var/www/html/1819/includes/sdk.php');
// for running by corndtab: per day am 5:00

$db = new DB();
$MEMBER_FUNC = new MEMBER();
$ORDER_FUNC = new ORDER();
$INSURANCE_FUNC = new INSURANCE();
$KO_FUNC = new KO();


//================================================== 出發前 10 天保單檢查，發給主揪 (每天檢查一次，並塞入 notify table)
$days_before = 10;
$sql='SELECT distinct s.oidx FROM skidiy.schedules s where s.date > now() and s.oidx>0 and datediff(s.date,now()) <= '.$days_before.';';
$result = $db->query('SELECT', $sql);
$c=0;
foreach ($result as $key => $val) {
   //echo $key.'. '.$val['oidx'].'<br>';  
   
   $q['oidx']=$val['oidx'];  
   $q['type']='insuranceNotify_v2';  
   $member_info = $MEMBER_FUNC ->get_memberinfo_by_order($q['oidx']);
   //echo "country code=".$member_info['country']."\r\n";
   //if($ORDER_FUNC->insurance_notify_check($q) == 0 && $ORDER_FUNC->insurance_status_check($val['oidx'])==0 && $member_info['country']=='886')  { // 確保只會安插一次
   //if($INSURANCE_FUNC->check_order_status($q['oidx'])==INSURANCE_STATUS_COLECTING && $member_info['country']=='886')  { // 每天安插一次 
   if(($INSURANCE_FUNC->check_order_status($q['oidx'])==INSURANCE_STATUS_INTERNAL_ERR
    || $INSURANCE_FUNC->check_order_status($q['oidx'])==INSURANCE_STATUS_COLECTING 
    ||  $INSURANCE_FUNC->check_order_status($q['oidx'])==INSURANCE_STATUS_NULL_DATA ) 
    && $ORDER_FUNC->SelfInsurance_Check($q['oidx']) == 0 
    && $member_info['country']=='886' 
     )  { // 每天安插一次  
      $c++;
   		// 主啾處理   		
	    $notify_data['type']='insuranceNotify_v2';
	    $notify_data['sent']=0;
	    $notify_data['resp']=1;
	    $notify_data['oidx']=$q['oidx'];
	    $notify_data['createDateTime']=date('Y-m-d H:i:s');
	    $KO_FUNC->notify($notify_data); //<--- 注意：這邊只會發信到主揪 ｍａｉｌ
	    echo $c.' 10 days reminding ['.date('Y-m-d H:i:s').'] Add Oidx to notify table: '.$q['oidx']."\r\n";	    
   }else{
    // insert
   }
}



//================================================== 出發前 7 天保單檢查，發給jake (每天檢查一次)
$imail = 'jasmine082077@gmail.com';
//$imail = 'mauji168@gmail.com';
$adminmail = 'mjskidiy@gmail.com';
//==================================================
$days_before = 7;
$sql='SELECT distinct s.oidx FROM skidiy.schedules s where s.date > now() and s.oidx>0 and datediff(s.date,now()) <= '.$days_before.' order by s.date;';
$result = $db->query('SELECT', $sql);


$mail_info['email']    = $imail;
$mail_info['subject']  = "重要提醒： 🏂  SKIDIY 學員七日即將上課，保險資料不齊（尚未填寫任何團員資料）";
$mail_info['content']  = ">>>>>>>>>>>>>>> 以下學生即將於7日內上課，目前尚未填寫任何保險資料！！ <<<<<<<<<<<<<< \r\n\r\n";
$c=0;
foreach ($result as $key => $val) {
   //echo $key.'. '.$val['oidx'].'<br>';    
   $q['oidx']=$val['oidx'];  
   $q['type']='insuranceNotify_v2';  
   $member_info = $MEMBER_FUNC ->get_memberinfo_by_order($q['oidx']);
   //echo "country code=".$member_info['country']."\r\n";
   //if($ORDER_FUNC->insurance_notify_check($q) == 0 && $ORDER_FUNC->insurance_status_check($val['oidx'])==0 && $member_info['country']=='886')  { // 確保只會安插一次
   if(($INSURANCE_FUNC->check_order_status($q['oidx'])==INSURANCE_STATUS_NULL_DATA ) 
    && $ORDER_FUNC->SelfInsurance_Check($q['oidx']) == 0 
    && $member_info['country']=='886' 
   )  { // 每天安插一次 
          $c++;
          $c_date = $ORDER_FUNC->schedule_class_date($q['oidx']);
          $mail_info['content'] .= $c." 訂單編號: ".$q['oidx']." ,上課日期： ".$c_date." ,姓名： ".$member_info['name']." ,聯繫電話：".$member_info['phone']." ,信箱：".$member_info['email']."\r\n";
          echo $c.' 全部未填 ['.date('Y-m-d H:i:s').'] Notify to Jake , class_date='.$c_date.' , oidx = '.$q['oidx']."\r\n";      
   }else{
    // insert
   }
}
$mail_info['content'] .= "\r\n請儘速聯繫學生並進一步確認核保事宜，謝謝。\r\n";
if($c>0){ // 有學員資訊才發信
  $MEMBER_FUNC->send_mail($mail_info);  
  $mail_info['email']    = $adminmail;
  $MEMBER_FUNC->send_mail($mail_info);  // 副本給管理者
}
//exit();

//================================================== 出發前 7 天保單檢查，發給jake (每天檢查一次)
$days_before = 7;
$sql='SELECT distinct s.oidx FROM skidiy.schedules s where s.date > now() and s.oidx>0 and datediff(s.date,now()) <= '.$days_before.' order by s.date;';
$result = $db->query('SELECT', $sql);


$mail_info['email']    = $imail;
$mail_info['subject']  = "重要提醒： 🏂  SKIDIY 學員七日即將上課，保險資料不齊（僅填寫部分團員資料）";
$mail_info['content']  = "以下學生即將於7日內上課，目前所填寫的保險資料仍然不齊全！！\r\n\r\n";
$c=0;
foreach ($result as $key => $val) {
   //echo $key.'. '.$val['oidx'].'<br>';  
   $q['oidx']=$val['oidx'];  
   $q['type']='insuranceNotify_v2';  
   $member_info = $MEMBER_FUNC ->get_memberinfo_by_order($q['oidx']);
   //echo "country code=".$member_info['country']."\r\n";
   //if($ORDER_FUNC->insurance_notify_check($q) == 0 && $ORDER_FUNC->insurance_status_check($val['oidx'])==0 && $member_info['country']=='886')  { // 確保只會安插一次
   if(($INSURANCE_FUNC->check_order_status($q['oidx'])==INSURANCE_STATUS_COLECTING )&& $member_info['country']=='886')  { // 每天安插一次 
          $c++;
          $c_date = $ORDER_FUNC->schedule_class_date($q['oidx']);
          $mail_info['content'] .= $c." 訂單編號: ".$q['oidx']." ,上課日期： ".$c_date." ,姓名： ".$member_info['name']." ,聯繫電話：".$member_info['phone']." ,信箱：".$member_info['email']."\r\n";
          
          echo $c.'部分未填 ['.date('Y-m-d H:i:s').'] Notify to Jake , class_date='.$c_date.' , oidx = '.$q['oidx']."\r\n";
   }else{
    // insert
   }
}
$mail_info['content'] .= "\r\n請儘速聯繫學生並進一步確認核保事宜，謝謝。\r\n";
if($c>0){ // 有學員資訊才發信
  $MEMBER_FUNC->send_mail($mail_info);   
  $mail_info['email']    = $adminmail;
  $MEMBER_FUNC->send_mail($mail_info);  // 副本給管理者
}
//exit();



?>