<?php
require('../includes/sdk.php');

$data = [];
$db = new db();
$ko = new ko();
$sql = "SELECT s.`oidx`, s.`studentNum`, o.`discount`, i.`inusrance_num` FROM `schedules` as s 
		LEFT JOIN `orders` as o ON s.`oidx`=o.`oidx` 
		LEFT JOIN `insuranceInfo` as i ON i.`oidx`= o.`oidx` 
		WHERE s.`oidx`!=0 AND s.`date` >= '2019-11-15' 
		AND o.`status`='success' 
		AND i.`master`='Y' AND i.`inusrance_num`!=0
		ORDER BY s.`oidx`";
$res = $db->query('SELECT', $sql);

foreach ($res as $r) {
	if(!isset($data[$r['oidx']]['studentNum'][$r['studentNum']])){
		$data[$r['oidx']]['studentNum'][$r['studentNum']] = 0;
	}else{
		$data[$r['oidx']]['studentNum'][$r['studentNum']] += 1;
	}
	$data[$r['oidx']]['count'][$r['studentNum']] = $r['studentNum'];
	$data[$r['oidx']]['discount'] = $r['discount'];
	$data[$r['oidx']]['insurance'] = $r['inusrance_num'];
}

//_v($data);

foreach ($data as $oidx => $r) {
	//echo "#{$oidx}, " . implode('|', $r['count']) . ", Dis={$r['discount']}, Insurance={$r['insurance']}<br>";
	$o = array_sum($r['count']);
	$diff = ($o-$r['insurance'])*1000;
	
	if($diff<0){
		$order = $ko->getOneOrderInfo(['oidx'=>$oidx]);
		$d = $order['schedule'][0]['date'];
		if(strtotime($d)<time()) continue;
		echo "#{$oidx},{$o},{$r['insurance']}, {$diff}, {$d}\n";

	}
}
