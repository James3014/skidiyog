<?php
require('../includes/sdk.php');

$filters = array(
    'oidx'        =>  FILTER_SANITIZE_FULL_SPECIAL_CHARS,
    'instructor'  =>  FILTER_SANITIZE_FULL_SPECIAL_CHARS,
);
$in = filter_var_array(array_merge($_REQUEST,$_POST), $filters);
//測試用
//$in['instructor']='ko';

$ko = new ko();
$oldOrder = $ko->getOneOrderInfo(['oidx'=>$in['oidx']]);//_v($order);
//檢查
if(empty($oldOrder['schedule'][0]['date'])){
  echo 'Order date error!!';exit();
}
if(empty($oldOrder['schedule'][0]['expertise'])){
  echo 'Order expertise error!!';exit();
}
if(empty($oldOrder['schedule'][0]['park'])){
  echo 'Order park error!!';exit();
}
foreach ($oldOrder['schedule'] as $s) {
	if($s['instructor']!='virtual'){
		echo "Arrange instructor {$s['instructor']} is not allowed!"; exit();
	}
}


//開始排課
foreach ($oldOrder['schedule'] as $s) {
	$schedule = [
		'instructor'	=> $in['instructor'],
		'date'			=> $s['date'],
		'slot'			=> $s['slot'],
		'park'			=> $s['park'],
		'expertise'		=> $s['expertise'],
		'oidx'			=> $in['oidx'],
		'studentNum'	=> $s['studentNum'],
		'fee'			=> $s['fee'],
		'arranged'		=> 1,
		'rule'			=> 0,
		'reservation'	=> 1,
		'createDateTime'=> date('Y-m-d H:i:s'),
	];//_v($schedule);
	//再檢查該申請是否有開課/訂課/條件
	$ok = $ko->addResvSchedule($schedule);
	if(!$ok){
		$ko->log([
			'severity'  =>  'orderApply',
		    'user'      =>  'admin',
		    'oidx'      =>  $in['oidx'],
		    'msg'       =>  json_encode($s, JSON_UNESCAPED_UNICODE),
		    'resp'      =>  json_encode($schedule, JSON_UNESCAPED_UNICODE),
		]);
		echo 'Arrage fail, please contact ko!!!'; exit();
	}
}
//刪除原來的申請
$ko->deleteSchedule(['oidx'=>$in['oidx'],'instructor'=>'virtual']);

$newOrder = $ko->getOneOrderInfo(['oidx'=>$in['oidx']]);//_v($order);

$ko->log([
	'severity'  =>  'orderApply',
    'user'      =>  'admin',
    'oidx'      =>  $in['oidx'],
    'msg'       =>  json_encode($oldOrder, JSON_UNESCAPED_UNICODE),
    'resp'      =>  json_encode($newOrder, JSON_UNESCAPED_UNICODE),
]);

$ko->notify([
	'oidx'              => $in['oidx'],
	'type'              => 'reservationNext',
	'sent'				=> 0,
	'resp'              => 'admin arrange '.$in['instructor'],
	'createDateTime'    => date('Y-m-d H:i:s'),
]);

echo 'Arrange done!!';