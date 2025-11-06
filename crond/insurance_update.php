<?php
require('/var/www/html/1819/includes/sdk.php');
// for running by corndtab: per day am 5:00

$db = new DB();
$MEMBER_FUNC = new MEMBER();
$ORDER_FUNC = new ORDER();
$INSURANCE_FUNC = new INSURANCE();
$KO_FUNC = new KO();


//================================================== update 保單 上課日期 
  $sql="SELECT * FROM skidiy.insuranceInfo where status!='order_canceled' and (class_date is null or class_date >= now()) ;";
  $result = $db->query('SELECT', $sql);
  echo "Start to update the date and days for insurance tb\r\n";
  foreach ($result as $key => $val) {
     
     $data['class_date']  = $ORDER_FUNC->schedule_class_date($val['oidx']);
     $data['class_days']  = $ORDER_FUNC->schedule_class_days($val['oidx']);
     $where['oidx']       = $val['oidx'];
     $INSURANCE_FUNC->update_import($data,$where);
     echo $key.'. Update Insurance: order='.$val['oidx'].', date='.$data['class_date'].", days=".$data['class_days']." \r\n";  
  }
  echo "\r\n";
  echo 'Last log datetime:'.date('Y-m-d H:i:s')."\r\n";

//================================================== update 保單 狀態 （ｅｘ：取消訂單）
  $sql="SELECT * FROM skidiy.orders where status='canceled' or status='noshow' ;";
  $result = $db->query('SELECT', $sql);
  echo "Start to update the  status for insurance tb\r\n";
  foreach ($result as $key => $val) {
    $data2['status']  = 'order_canceled'; // 保單取消
    $where['oidx']   = $val['oidx'];
    $INSURANCE_FUNC->update_import($data2,$where);
    echo $key.'. Cancel Insurance:'.$val['oidx']."....canceled \r\n";  
  }
  echo "\r\n";
  echo 'Last log datetime:'.date('Y-m-d H:i:s')."\r\n";


//================================================== update 定單 上課日期 
  $sql="SELECT * FROM skidiy.orders where oidx>= 8000 and (day1_class is null or day1_class='0000-00-00' or day1_class >= now()) and status='success' order by oidx desc;";
  $result = $db->query('SELECT', $sql);
  echo "Start to update the date  for orders tb\r\n";
  foreach ($result as $key => $val) {     
     $data3['day1_class']  = $ORDER_FUNC->schedule_class_date($val['oidx']);
     $ORDER_FUNC->update($val['oidx'],$data3);
     echo $key.'.Order IDX: '.$val['oidx'].', DAY1 Class Date='.$data3['day1_class']." \r\n";  
  }  
  echo "\r\n";
  echo 'Last log datetime:'.date('Y-m-d H:i:s')."\r\n";


//================================================== 出發前 7 天保單 資料已填寫完成，但尚未送出核保按鈕 (auto change from collecting to submit_request)
  $days_before = 7;
  $sql='SELECT distinct s.oidx FROM skidiy.schedules s where s.date > now() and s.oidx>0 and datediff(s.date,now()) <= '.$days_before.' order by s.date;';
  $result = $db->query('SELECT', $sql);
  $c=0;
  foreach ($result as $key => $val) {
     //echo $key.'. '.$val['oidx'].'<br>';  
     $q['oidx']=$val['oidx'];  
     $q['type']='insuranceNotify_v2';  
     $member_info = $MEMBER_FUNC ->get_memberinfo_by_order($q['oidx']);

     if(($INSURANCE_FUNC->check_order_status($q['oidx'])==INSURANCE_STATUS_COLECTING_DONE )&& $member_info['country']=='886')  { // 每天安插一次 
            $c++;
            $c_date = $ORDER_FUNC->schedule_class_date($q['oidx']);
            //$mail_info['content'] .= $c." 訂單編號: ".$q['oidx']." ,上課日期： ".$c_date." ,姓名： ".$member_info['name']." ,聯繫電話：".$member_info['phone']." ,信箱：".$member_info['email']."\r\n";
            
            echo $c.'資料已填寫完成 ['.date('Y-m-d H:i:s').'] , class_date='.$c_date.' , oidx = '.$q['oidx']."\r\n";
            $INSURANCE_FUNC->update_status_by_oidx($q['oidx'],'submit_request');
     }else{
      // insert
     }
  }


?>