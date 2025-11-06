<?php
require('/var/www/html/1819/includes/sdk.php');

exit(); // disable from 2020.02.01
//申請逾時
$ACCOUNT = new MEMBER();
$db = new DB();

$start = date('Y-m-d');
$end = date('Y-m-d', strtotime('+150 days'));

$sql = "SELECT * FROM `schedules` WHERE DATE(`date`) BETWEEN '{$start}' AND '{$end}'";_d($sql);
$res = $db->query('SELECT', $sql);

$data = [];
$cnt = 0;
$result = '';
foreach ($res as $s) {
	if(empty($s['park'])) continue;
	if($s['instructor']=='virtual') continue;

	if(empty($data[$s['instructor']][$s['date']])){
		$data[$s['instructor']][$s['date']][] = $s['park'];
	}else{
		if(!in_array($s['park'], $data[$s['instructor']][$s['date']])){
			$data[$s['instructor']][$s['date']][] = $s['park'];
			$cnt++;
			$park = $data[$s['instructor']][$s['date']][0];
			$result .= "{$cnt}. {$s['instructor']}, {$s['date']}: {$park} & {$s['park']}\n";
			//sleep(1);
		}
	}
}

echo $result;
//exit();

if(strlen($result)){
	$subject = "🏂 同一天不同雪場開課,請儘速和教練確認～～";
	$content = $result;
	$ok = $ACCOUNT->send_mail([
		'email' 	=> ['admin@diy.ski', 'eric@inncom.cloud', 'mjskidiy@gmail.com'],
		'subject'	=> $subject,
		'content'	=> $content,
	]);	
}