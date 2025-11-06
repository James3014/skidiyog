<?php
require('../includes/sdk.php');
$db = new db();

$sql = "SELECT * FROM `schedules` 
		WHERE `oidx`!=0 AND `park`='karuizawa' AND `date` BETWEEN '2018-11-05' AND '2019-04-15' 
		ORDER BY `date`,`oidx`";

$res = $db->query('SELECT', $sql);//_v($res);exit();

$orders = [];
foreach ($res as $s) {
	$orders[$s['oidx']][$s['date']] = $s['studentNum'];
}//_j($orders);

$stats = [];
foreach ($orders as $oidx => $o) {
	$num = 

	$stats[] = [
		'oidx'	=> $oidx,
		'date'  => key($o),
		'days'	=> count($o),
		'students'=> call_user_func(function ($a){
			$students = 0;
			foreach ($a as $date => $num) {
				$students += $num;
			}
			return $students;
		}, $o),
	];
	//var_dump($stats);exit();
}
ksort($stats);

foreach ($stats as $s) {
	echo "{$s['oidx']},{$s['date']},{$s['days']},{$s['students']}\n";
}
