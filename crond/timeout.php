<?php
require('/var/www/html/1819/includes/sdk.php');

//付款逾時
$ACCOUNT = new MEMBER();
$db = new DB();
$ko = new ko();

$sql = "SELECT * FROM `orders` WHERE `status`='" . Payment::PAYMENT_CREATED . "' OR `status` REGEXP '[0-9]+'";//_d($sql);exit();
$res = $db->query('SELECT', $sql);//_v($res);

foreach ($res as $n => $r) {//_v($r['oidx']);//_v($r);
	//算時間
	$timeout = ((int)$r['timeout']*60) + strtotime($r['createDateTime']);//_d(date('Y-m-d H:i:s', $timeout));
	$now = time();//_d("{$timeout}, {$now}");

	//測試
	if(in_array($r['oidx'],['5205'])){
		echo "{$r['oidx']}\n";
		$timeout = $now-100;
	}//continue;

	$notifyType = ($r['status']=='create') ? 'orderTimeout' : 'ecpayFail';
	if($now>$timeout){//_v($r);//逾時
		$ko->notify([
	        'oidx'              => $r['oidx'],
	        'type'              => $notifyType,
	        'resp'              => 'crond',
	        'createDateTime'    => date('Y-m-d H:i:s'),
	    ]);
	}//if逾時

}//每筆訂單