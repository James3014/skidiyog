<?php
require('../includes/sdk.php');

$db1 = new db('1718');
$db2 = new db('skidiy');

//orders
$sql = "SELECT * FROM `members` ORDER BY `idx` ASC";// LIMIT 10";
$res = $db1->query('SELECT', $sql);//_v($res);exit();

$_t1 = microtime(true);
foreach ($res as $n => $m) {
	$co = [
		'idx'					=> $m['idx'],
		'type'					=> $m['type'],
		'name'					=> $m['name'],

		'email'					=> $m['email'],
		'fbid'					=> $m['fbname'],
		'wechat'				=> '',

		'line'					=> '',
		'passwd'				=> $m['passwd'],
		'2fa_authcode'			=> rand(1111,9999),

		'active_status'			=> 'PHASE1_DONE',
		'phone'					=> $m['phone'],
		'country'				=> '886',

		'student'				=> $m['student'],
		'create_date'			=> $m['create_date'],
		'timestamp'				=> $m['timestamp'],
	];

	$db2->insert('members_v2', $co);
}
$_t2 = microtime(true);

echo 'Cost' . ($_t2-$_t1) ."sec.\n";


$sql = "SELECT * FROM `members_instructor";
$res = $db1->query('SELECT', $sql);
foreach ($res as $n => $mi) {
	$sql = "UPDATE `members_v2` SET 
					`fbid`='{$mi['fbid']}', 
					`email`='{$mi['email']}',
					`wechat`='{$mi['wechat']}',
					`line`='{$mi['line']}', 
					`passwd`='{$mi['passwd']}',
					`phone`='{$mi['phone']}',
					`country`='{$mi['country']}',
					`student`=1
			WHERE `type`='instructor' AND `name`='{$mi['name']}'
			";
	$db2->query('UPDATE', $sql);
}
