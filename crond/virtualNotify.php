<?php
require('/var/www/html/1819/includes/sdk.php');

//申請逾時
$db = new DB();
$ACCOUNT = new MEMBER();

$sql = "SELECT `oidx`, `park`, `date`, count(`oidx`) AS `cnt`, sum(`fee`) AS `fee` FROM `skidiy`.`schedules`
		WHERE `instructor`='virtual' AND `date` <= '2020-01-23' 
		GROUP BY `oidx` 
		ORDER BY `date` ASC;";//_d($sql);//exit();
$res = $db->query('SELECT', $sql);//_v($res);

$msg = '';
foreach ($res as $n => $r) {
	$msg .= "#{$r['oidx']}: {$r['date']} @ {$r['park']}, {$r['cnt']}堂課, 共{$r['fee']}.\n";
}//每筆訂單

if(strlen($msg)>0){
	$subject = "🏂 SKIDIY 尚未排課通知！";
	$content = "{$msg}\r\n\r\n";
	$ok = $ACCOUNT->send_mail([
		'email' 	=> ['admin@diy.ski'],
		'subject'	=> $subject,
		'content'	=> $content,
	]);
}