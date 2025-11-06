<?php
require('/var/www/html/1819/includes/sdk.php');

//申請逾時
$ACCOUNT = new MEMBER();
$db = new DB();

$start = '2020-12-01';

$sql = "
select 
	o.`oidx`, o.`price`, o.`discount`, o.`specialDiscount`, o.`prepaid`, o.`payment`, o.`requirement`, o.`note`, o.`memo`,
	s.`instructor`, s.`expertise`, s.`date`, s.`park`, s.`slot`, s.`fee`
from 
	`orders` as o left join `schedules` as s on s.`oidx`=o.`oidx` 
where 
	o.`status`='success' and o.`gidx`=0 and date(o.`createDateTime`)>='2022-09-01' 
order by
	s.`date`, s.`slot`
";_d($sql);//exit();

$res = $db->query('SELECT', $sql);//_v($res);


$data = [];
foreach ($res as $r) {
	$data[$r['oidx']] = [
		'price'				=> $r['price'],
		'discount'			=> $r['discount'],
		'specialDiscount'	=> $r['specialDiscount'],
		'prepaid'			=> $r['prepaid'],
		'payment'			=> $r['payment'],

		'requirement'	=> $r['requirement'],
		'note'			=> $r['note'],
		'memo'			=> $r['memo'],

		'fees'	=> empty($data[$r['oidx']]['fees']) ? $r['fee'] : $data[$r['oidx']]['fees']+$r['fee'],
		'date'	=> empty($data[$r['oidx']]['date']) ? $r['date'] : $data[$r['oidx']]['date'],
		'park'	=> $r['park'],
	];
}//_v($data);

$msg = '';
foreach ($data as $oidx => $o) {//
	$o['memo'] = nl2br($o['memo']);
	$o['memo'] = str_replace('<br>', ',', $o['memo']);

	if($o['price'] != $o['fees']){
		$msg .= "#{$oidx}, {$o['date']}@{$o['park']}: 學費{$o['price']} != {$o['fees']}\n\t{$o['memo']}\n";
	}else if(
		($o['payment'] != ($o['price']-$o['discount']-$o['prepaid']))
	){
		if (!in_array($oidx, [12422]))
			$msg .= "#{$oidx}, {$o['date']}@{$o['park']}:  尾款{$o['payment']} != {$o['price']} - {$o['discount']} + {$o['specialDiscount']} - {$o['prepaid']} \n\t\t{$o['memo']}\n";
	}else{
		//echo "#{$oidx} Check ok.\n"; 
	}
}

echo $msg;
//exit();

if(strlen($msg)>0){
	$subject = "🏂 SKIDIY 訂單費用異常通知～";
	$content = "{$msg}\r\n\r\n";
	$ok = $ACCOUNT->send_mail([
		'email' 	=> ['admin@diy.ski','mjskidiy@gmail.com','eric@inncom.cloud'],
		'subject'	=> $subject,
		'content'	=> $content,
	]);
}

