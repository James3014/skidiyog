<?php
require('../includes/sdk.php');

$ACCOUNT = new MEMBER();
$db = new db();

// $sql = "SELECT `oidx`,`orderNo`,`paid`, m.`email`, m.`name` 
// 		FROM `orders` AS o LEFT JOIN `members_v2` AS m ON o.`student`=m.`idx`
// 		WHERE `memo`='五碼需刷退';";

// $data = $db->query('SELECT', $sql);//_v($data);exit();

// foreach ($data as $r) {
// 	$content = sprintf("%s 您好,

// 	很抱歉，因刷卡系統作業異常，訂單保險編號(#%d)經我們確認後為重複刷卡，其費用共 $%s台幣 目前已刷退。
// 	預計兩個工作天會退款到刷卡銀行，銀行到個人帳戶約兩個禮拜，屆時還請您撥空確認是否入帳。
// 	造成您的不便還請見諒！

// 	如有任何問題歡迎隨時與我們聯繫 admin@diy.ski
// 	SKIDIY敬上.\n\n", $r['name'], $r['oidx'], number_format($r['paid']));echo $content;

// 	// $ok = $ACCOUNT->send_mail([
// 	// 	'email' 	=> $r['email'],
// 	// 	'subject'	=> 'SKIDIY 重複刷卡退費通知',
// 	// 	'content'	=> $content,
// 	// ]);
// 	echo "Sent {$r['email']}\n\n";
// }

$sql = "SELECT DISTINCT(s.`oidx`),s.`date`,o.`student`,m.`email`, m.`name`
FROM `schedules` AS s 
LEFT JOIN `orders` AS o ON s.`oidx` = o.`oidx`
LEFT JOIN `members_v2` AS m ON o.`student` = m.`idx`
WHERE s.`noshow`=19 AND s.`park`!='doraemon'
ORDER BY `oidx` ASC";
$data = $db->query('SELECT', $sql);//_v($data);exit();

$emails = [];
foreach ($data as $n => $i) {
	$emails[$i['oidx']] = $i['email'];
}//_v($emails);

$content = '';
foreach ($emails as $oidx => $email) {
	$content = sprintf("親愛的雪友您好,
因為疫情的影響，雖然日本已經趨緩
但是至今仍未開放觀光旅遊
原本的課程 網站會自動延期至2022年4月份

之前如果有確定行程，來信告知訂單編號 #%s 即可
我們會協助安排

SKIDIY敬上.", $oidx);
	echo $content;
	$ok = $ACCOUNT->send_mail([
		'email' 	=> $email,
		'subject'	=> 'SKIDIY課程 - 延期通知 (更正)',
		'content'	=> $content,
	]);
	echo "#{$oidx} > {$email} sent.\n";
}

exit();


$sql = "SELECT distinct(s.`oidx`) as `oidx`, o.`student`, o.`lock`, o.`allpay_ReturnURL`, m.`name`, m.`email`
FROM `schedules` AS s 
LEFT JOIN `orders` AS o ON s.`oidx`=o.`oidx`
LEFT JOIN `members_v2` AS m ON o.`student`=m.`idx`
WHERE s.`noshow`=19  AND s.`park`!='doraemon'
AND o.`createDateTime` between '2020-05-01' and '2020-05-30'";echo "{$sql}\n\n";
$data = $db->query('SELECT', $sql);//_v($data);exit();

$content = "";
foreach ($data as $n => $i) {
	$content = sprintf("親愛的雪友 %s,
感謝您對於SKIDIY的支持，由於疫情影響課程延期
網站針對目前的狀況 開會討論過後 決定主動退課退費
我們會協助處理相關的退費程序與流程

以下麻煩協助
1.登入訂單點選取消
https://diy.ski/my_order_list.php

2. 回傳訂單編號  #%d
3. 刷卡卡號

SKIDIY敬上.


", ucfirst($i['name']), $i['oidx']);

	$ok = $ACCOUNT->send_mail([
		'email' 	=> $i['email'],
		'subject'	=> 'SKIDIY 退費通知',
		'content'	=> $content,
	]);
	echo "{$n} #{$i['oidx']} > {$i['email']} sent.\n";
}

exit();



$sql = "SELECT distinct(`oidx`) as `oidx` FROM `skidiy`.`schedules` where `noshow`=19 order by `oidx`";
$data = $db->query('SELECT', $sql);//_v($data);exit();

$in = '';
foreach ($data as $n => $o) {
	$in .= "{$o['oidx']},";
}
$in = substr($in, 0, -1);

$sql = "select m.`name`, m.`email`, o.`oidx`, o.`memo`, o.`lock`
		from `orders` as o 
		left join `members_v2` as m on o.`student`=m.`idx` 
		where o.`oidx` in ({$in})";echo $sql;
$data = $db->query('SELECT', $sql);//_v($data);exit();

$content = "";
foreach ($data as $n => $i) {
	$content = sprintf("Hi %s!
由於疫情影響
課程系統稍後會往後延至2021年4月份
期間如果有確定想預約的時間
在麻煩來信告知系統會協助更改

另外skidiy下一季已經有確定合作優惠
如果有興趣 也可以在這邊參考
https://diy.ski/article.php?idx=27

SKIDIY敬上.


", ucfirst($i['name']));

	$ok = $ACCOUNT->send_mail([
		'email' 	=> $i['email'],
		'subject'	=> 'SKIDIY 課程異動說明',
		'content'	=> $content,
	]);
	echo "{$n}#{$i['oidx']} > {$i['email']} sent.\n";
}

	
