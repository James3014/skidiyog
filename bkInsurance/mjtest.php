<?php
require('/var/www/html/1819/includes/sdk.php');
$db = new DB();
$MEMBER_FUNC = new MEMBER();
$ORDER_FUNC = new ORDER();
$INSURANCE_FUNC = new INSURANCE();
$KO_FUNC = new KO();


if(1){
$privateKey = "1234567812345678";
$iv     = "1234567812345678";
$data   = "Test String";


$privateKey = "CD9FAFBE9C1531BFC832";
$iv     = "1234567812345678";

//加密
/*
$encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $privateKey, $data, MCRYPT_MODE_CBC, $iv);
echo(base64_encode($encrypted));
echo 'mj<hr>';
*/
//echo 'iv size='.mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC)."<br>";

$mac_address = '201913184444';
$serial_number = 'S201913184444';
$tmpkey =$serial_number.$mac_address;
echo $tmpkey."<br>";
$key_len= strlen($tmpkey);
$key_tail_5 = substr($tmpkey, -5);    // 尾巴取5個
$key_head_3 = substr($tmpkey, 0,3);     // 頭取3個
echo "head 3 = ".$key_head_3."<br>";
echo "tail 5 = ".$key_tail_5."<hr>";
$key_midle  = substr($tmpkey, 3,$key_len-3); // 頭-3
//echo $key_midle."<br>";
$key_midle  = substr($key_midle, 0,-5); // 尾-3

echo $key_midle."<br>";

$key = strtolower($key_tail_5.$key_midle.$key_tail_5);
$key = md5($key);
echo "result: ".$key."<br>";


$privateKey = $key;
$privateKey = 'cC/CnuWJp3tC4LJU5vi3dieiAKzBvBB6JM5Byw=='; // MZC only
$mzc_key    = $privateKey;
$privateKey = pack('H*', base64_decode($privateKey));
echo "key=".$privateKey;

//exit();


//解密
$encryptedData="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";
$enc_msg = $encryptedData;

//echo "data(".strlen($encryptedData).")<br>";
$encryptedData=base64_decode($encryptedData);
echo "data(".strlen($encryptedData).")<br>";

//echo $encryptedData."<br>";
$iv = substr($encryptedData, 0,16);     // 頭取16個
echo "iv=<br>".$iv."<br>";

$encryptedData= substr($encryptedData, 16 ) ;    // 尾巴取 len-16 個
//echo "data(".strlen($encryptedData).")=<br>".$encryptedData."<br>";




$decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $privateKey, $encryptedData, MCRYPT_MODE_CBC, $iv);
echo '<br><br>start<hr>'.$decrypted;

echo '<hr>end';


dv($enc_msg,$mzc_key);
}

function dv($txt,$key){
    $ciphertext_dec = base64_decode($txt);
    $iv = substr($ciphertext_dec, 0, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC));
    $ciphertext_dec = substr($ciphertext_dec, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC));
    return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, pack('H*', $key), $ciphertext_dec, MCRYPT_MODE_CBC, $iv));
  }

exit();

/*
$days_before = 10;
$sql='SELECT distinct s.oidx FROM skidiy.schedules s where s.date > now() and s.oidx>0 and datediff(s.date,now()) <= '.$days_before.';';
$result = $db->query('SELECT', $sql);

echo '保單14天提醒檢查<br>';
$c=0;
foreach ($result as $key => $val) {
   //echo $key.'. '.$val['oidx'].'<br>';  
   $q['oidx']=$val['oidx'];  
   $q['type']='insuranceNotify_v2';  
   // 保險狀態未核准
   if($ORDER_FUNC->insurance_notify_check($q) == 0 && $ORDER_FUNC->insurance_status_check($val['oidx'])==0 )  {
    $c++;
    $notify_data['type']='insuranceNotify_v2';
    $notify_data['sent']=0;
    $notify_data['resp']=1;
    $notify_data['oidx']=$q['oidx'];
    $KO_FUNC->notify($notify_data);
    _d('master insert >> '.$q['oidx'].'<br>');



      // 其他團員處理
    //==================================================================================
        $order_id = $q['oidx'];
        $insuranceFUNC = new INSURANCE();
        $insuranceList = $insuranceFUNC->get_list($order_id);
  
        $cnt=0;
        $insurance_total_people=0;
        $insurance_info='';
        if(count($insuranceList)>0){
          foreach ($insuranceList as $key => $value) {
            $cnt++;
            $insurance_info .= $c.". ".$value['pcname']." | ".$value['birthday']." | ".$value['twid']."\r\n";                            
            if($value['master']=='Y'){
              $insurance_total_people = $value['inusrance_num'];
            }else{// 其他團員
          // send mail
              $sec_idx  = crypto::ev($value['idx']);                
              $sec_oidx = crypto::ev($order_id);  
              $modify_link = 'https://diy.ski/insurance_fapply.php?id='.$sec_oidx.'&qid='.$sec_idx.'&m=m';
              $mail_info['email']    = 'mauji168@gmail.com'; // $value['email']
              $mail_info['subject']  = "🏂  {$value['pcname']} 您好, SKIDIY提醒您即將上課（訂單編號：#".$order_id."）";
              $mail_info['content']  = $value['pcname']." 您好,\r\n提醒您即將上課，您先前填寫的保險資料如下:\r\n\r\n";

              $mail_info['content'] .= "姓名: ".$value['pcname'].", 出生日期: ".$value['birthday'].", 身分證:".$value['twid']." \r\n";  
              $mail_info['content'] .= "以上若有錯誤，請儘速點擊下列連結修改，謝謝。\r\n";
              $mail_info['content'] .= $modify_link;

              echo 'others send mail directly';
              $MEMBER_FUNC->send_mail($mail_info);

            }
          }
          if($insurance_total_people  > count($insuranceList)){
            $remind = $insurance_total_people - count($insuranceList);                                                
          }             
        }else{
          $remind = 0;
        }
        //================================================================================== 
   }else{
    // insert
   }
} 
echo '保單14天提醒檢查.........共計：'.$c.' 筆 notify,  done<br>';
*/

if(0){
  $sql='SELECT * FROM skidiy.insuranceInfo;';
  $result = $db->query('SELECT', $sql);
  echo 'start to update the date of insurance tb<br>';
  foreach ($result as $key => $val) {
     echo $key.'. update order:'.$val['oidx'].', date='.$ORDER_FUNC->schedule_class_date($val['oidx']).'<br>';  
     $data['class_date']  = $ORDER_FUNC->schedule_class_date($val['oidx']);
     $where['oidx']       = $val['oidx'];
     $INSURANCE_FUNC->update_import($data,$where);
  }
}




// 針對台灣用戶 發信 通知 保險平臺上線
if(0){
  $query_arry['ccode']=886;
  $query_arry['odate']=date('Y-m-d');
	
  $Result = $MEMBER_FUNC->get_mail_list_by_order($query_arry); 
  //_v($Result);
        $c=0;
        foreach ($Result as $key => $val) {
          
          //echo $key.'. Sent: '.$val['email'].'-'.$val['oidx'].'<br>';
          
          $c=$c+1;
          if(1){
            //$mail_info['email']   =  $val['email'];
            $name = '';
            if(strlen($val['name'])>0) $name='『'.$val['name'].'』 ';
            //$mail_info['email']     =  'mauji168@gmail.com';
            $mail_info['email']     =  $val['email'];
            $mail_info['subject']   ='SKIDIY 保單資料填寫提醒'.$val['name'];
            $mail_info['content']  =$name."您好,\r\n我們的新保險後台已完成，若要填寫保險資料\r\n";
            $mail_info['content'] .="請先登入SKIDIY後，至『帳號』-> 『訂單資訊』 點擊對應訂單後 \r\n";          
            $mail_info['content'] .="您可在【保單填寫】內，完成自己或是其他團員的內容填寫 \r\n";
            $mail_info['content'] .="並請於上課前兩週完成填寫且送出核保，以利後續投保作業，謝謝。\r\n\r\n";
            $mail_info['content'] .="提醒您: 若您先前已在舊表單上填寫過資料，亦請再重新填寫一次唷。 \r\n";
            $mail_info['content'] .="我的訂單資訊列表: https://diy.ski/my_order_list.php \r\n";
            $mail_info['content'] .="以上若有疑問，可回信至 admin@diy.ski，我們將盡快協助，謝謝。";

            //echo $mail_info['content'];
            if($c>5){
              $MEMBER_FUNC->send_mail($mail_info);   
              echo $c.'. Sent: '.$val['email'].'-'.$name."\r\n";
            }else{
              echo "Done\r\n";
            }
          } 
          
          

        }        

     
}
?>