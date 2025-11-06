<?php
require('/var/www/html/1819/includes/sdk.php');

//申請逾時
$db = new DB();
$ko = new ko();

$sql = "SELECT * FROM `acceptions` WHERE `accepted`='wait' ORDER BY `oidx` ASC";//_d($sql);exit();
$res = $db->query('SELECT', $sql);//_v($res);

foreach ($res as $n => $r) {//_v($r['oidx']);//_v($r);
	//算時間
	$timeout = (36*60*60) + strtotime($r['createDateTime']);//_d(date('Y-m-d H:i:s', $timeout));
	
	//skidiy 直接timeout方便排課
	if($r['instructor']=='skidiy'){
		$timeout = time()-1000;
	}

	$now = time();_d("{$r['oidx']}: {$timeout}, {$now}");

	//測試
	if(in_array($r['oidx'],['5267'])){
		echo "{$r['oidx']}\n";
		$timeout = $now-100;
	}//continue;

	if($now>$timeout){//_v($r);//逾時
		echo "timeout\n";
		$ko->setAcception(['accepted'=>'false'],['idx'=>$r['idx']]);
		$ko->notify([
	        'oidx'              => $r['oidx'],
	        'type'              => 'acceptTimeout',
	        'resp'              => 'crond',
	        'createDateTime'    => date('Y-m-d H:i:s'),
	    ]);
	}//if逾時

}//每筆訂單
