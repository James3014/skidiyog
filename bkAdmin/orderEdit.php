<?php
require('../includes/sdk.php');
require('../includes/auth.php'); // Admin authentication check
$filters = array(
    'year'          =>  FILTER_SANITIZE_FULL_SPECIAL_CHARS,
    'month'         =>  FILTER_SANITIZE_FULL_SPECIAL_CHARS,        
    'park'          =>  FILTER_SANITIZE_FULL_SPECIAL_CHARS,
    'instructor'    =>  FILTER_SANITIZE_FULL_SPECIAL_CHARS,
    'status'        =>  FILTER_SANITIZE_FULL_SPECIAL_CHARS,

    'action'		=>	FILTER_SANITIZE_FULL_SPECIAL_CHARS,
    'oidx'			=>	FILTER_SANITIZE_FULL_SPECIAL_CHARS,
);//_v($_POST);
$in = filter_var_array(array_merge($_REQUEST,$_POST), $filters);//_v($in);exit();

$ko = new ko();

switch ($in['action']) {
	case 'success':
		$ok = $ko->updateOrder(['status'=>'success'],['oidx'=>$in['oidx']]);
		break;
	case 'canceled'://取消訂單
		$ko->notify([
	        'oidx'              => $in['oidx'],
	        'type'              => 'orderCanceled',
	        'resp'              => 'admin',
	        'createDateTime'    => date('Y-m-d H:i:s'),
	    ]);
	    $ok = $ko->updateOrder(['status'=>'cancel'],['oidx'=>$in['oidx']]);
		break;

	case 'refund'://記錄刷退
		$ko->notify([
	        'oidx'              => $in['oidx'],
	        'type'              => 'orderRefund',
	        'resp'              => 'admin',
	        'createDateTime'    => date('Y-m-d H:i:s'),
	    ]);
		$ok = $ko->updateOrder(['status'=>'refund'],['oidx'=>$in['oidx']]);
		break;
	default:
		# code...
		break;
}
$ko->log([
	'severity'	=> 'orderChangeStatus',
	'user'		=> 'admin',
	'oidx'		=> $in['oidx'],
	'msg'		=> $in['action'],
	'resp'		=> $ok,
]);
Header("Location: orders.php?year={$in['year']}&month={$in['month']}&park={$in['park']}&instructor={$in['instructor']}&status={$in['status']}");