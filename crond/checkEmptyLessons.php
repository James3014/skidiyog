<?php
require('/var/www/html/1819/includes/sdk.php');

//申請逾時
$ACCOUNT = new MEMBER();
$db = new DB();

$start = '2018-12-05';
$end = '2019-04-15';

$sql = "SELECT * FROM `orders` WHERE `oidx`>5000 and `status`='success' and `gidx`=0";//_d($sql);
$res = $db->query('SELECT', $sql);

foreach ($res as $o) {
	$sql = "SELECT * FROM `schedules` WHERE `oidx`={$o['oidx']}";
	$lessons =  $db->query('SELECT', $sql);
	if(sizeof($lessons)==0){
		echo "{$o['oidx']},";
	}
}