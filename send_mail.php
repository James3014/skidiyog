<?php
require('includes/sdk.php'); 
$ACCOUNT = new MEMBER();

$mail_info['email'] = array("mauji168@gmail.com","maoji.wang@zyxel.com.tw","ms000630@gmail.com");
$mail_info['subject'] = 'SKIDIY會員Email 多人寄送測試'.date('Y-m-d H:m:s');
$mail_info['content'] = "SKIDIY會員您好,請您點擊以下連結以完成Email驗證流程, 謝謝您～\r\n\r\n\r\n\r\n\r\nSKIDIY\r\n自助滑雪\r\nadmin@diy.ski\r\nhttps://diy.ski";
$ACCOUNT->send_mail($mail_info);
echo 'send completed';


?>