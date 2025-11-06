<?php
require('/var/www/html/1819/includes/sdk.php');

//申請逾時
$ACCOUNT = new MEMBER();
$db = new DB();

$start = '2020-12-01';

$sql = "select o.`oidx` from `orders` as o left join `schedules` as s on s.`oidx`=o.`oidx` 
		where o.`status`='success' and o.`gidx`=0 and date(o.`createDateTime`)>='{$start}'
		and s.`date` is null";
$res = $db->query('SELECT', $sql);_v($res);

//
if(sizeof($res)){
	echo "Warning....\n";
	$msg = '';
	foreach ($res as $r) {
		$msg .= "#{$r['oidx']}, ";
	}

	$subject = "🏂 訂單(課程)異常～";
	$content = "異常編號 {$msg}\r\n\r\n";
	$content.= "請儘速確認～～～";
	$ok = $ACCOUNT->send_mail([
		'email' 	=> ['admin@diy.ski','mjskidiy@gmail.com','eric@inncom.cloud'],
		'subject'	=> $subject,
		'content'	=> $content,
	]);
	//寄給管理者
}




