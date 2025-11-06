<?php
require('/var/www/html/1819/includes/sdk.php');

//申請逾時
$db = new DB();
$ko = new ko();

$sql = "SELECT * FROM `acceptions` WHERE `accepted`='false' ORDER BY `oidx` ASC";//_d($sql);//exit();
$res = $db->query('SELECT', $sql);//_v($res);

foreach ($res as $n => $r) {//_v($r['oidx']);//_v($r);
	//取消教練加級費用
	// $levelFee = 0;
	// $order = $ko->getOneOrderInfo(['oidx'=>$r['oidx']]);//_v($order);
	// foreach ($order['schedule'] as $n => $s) {
	// 	$instructorData = $ko->getInstructorInfo(['type'=>'instructor', 'name'=>$s['instructor']]);//_v($instructorData);
	// 	$instructor = array_pop($instructorData);
	// 	if($r['unLevelFee']==0 && $instructor['levelFee']>0){//有加級
	// 		//更新費用
	// 		$fee = $s['fee'] - $instructor['levelFee'];//扣掉加級
	// 		$ko->updateSchedule(['fee'=>$fee], ['sidx'=>$s['sidx']]);//更新
	// 		$levelFee += $instructor['levelFee'];//扣學費&尾款用
	// 	}
	// }//for each schedule

	// if($r['unLevelFee']==0 && $levelFee){//更新學費&尾款
	// 	$price = $order['price'] - $levelFee;
	// 	$payment = $order['payment'] - $levelFee;
	// 	$ko->updateOrder(['price'=>$price, 'payment'=>$payment], ['oidx'=>$r['oidx']]);
	// }

	//最後把課程改為未定
	$where = ['oidx'=>$r['oidx']];
	$schedule = [
		'instructor'	=> 'virtual',
	];
	$ko->updateSchedule($schedule, $where);

	//把詢問改為pending, unlevelFee設為1表示已第一次已扣過加級費, 
	$ko->setAcception(['accepted'=>'pending','unLevelFee'=>1], ['idx'=>$r['idx']]);
}//每筆訂單