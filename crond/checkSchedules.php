<?php
require('/var/www/html/1819/includes/sdk.php');

//申請逾時
$ACCOUNT = new MEMBER();
$db = new DB();

$start = '2020-12-01';
$end = '2021-04-15';

$sql = "SELECT * FROM `schedules` WHERE DATE(`date`) BETWEEN '{$start}' AND '{$end}'";//_d($sql);
$res = $db->query('SELECT', $sql);

$info = $dupS = $dupO = [];

$msg = '';
foreach ($res as $n => $s) {
	if($s['instructor']=='virtual') continue;
	if($s['instructor']=='skidiy' || $s['instructor']=='skidiy2') continue;

	if(!isset($info[$s['instructor']][$s['date']][$s['slot']])){
		$info[$s['instructor']][$s['date']][$s['slot']]['sidx'] = $s['sidx'];
		$info[$s['instructor']][$s['date']][$s['slot']]['msg'] = "{$s['instructor']},{$s['date']},{$s['slot']}";
	}else{
		$msg .= "重複開課 sidx={$s['sidx']} :: {$info[$s['instructor']][$s['date']][$s['slot']]['msg']}\n";
		echo $msg;
	}
}


if(!empty($msg)){
	$subject = "🏂 重複開課問題！！！";
	$content = "{$msg}\r\n\r\n";
	$content.= "請儘速確認～～～"; 
	$ok = $ACCOUNT->send_mail([
		'email' 	=> ['admin@diy.ski','mjskidiy@gmail.com','eric@inncom.cloud'],
	 	'subject'	=> $subject,
	 	'content'	=> $content,
	]);
	//寄給管理者
}
