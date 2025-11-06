<?php
require('../includes/sdk.php');

$db1 = new db('1718');
$db2 = new db('skidiy');

//schedule
$sql = "SELECT * FROM `schedules` WHERE `date`>='2018-05-01' ORDER BY `date` ASC";//_d($sql);
$res = $db1->query('SELECT', $sql);//_v($res);exit();


$_t1 = microtime(true);
foreach ($res as $n => $s) {
	if($s['instructor']=='virtual'&&empty($s['oidx'])) continue;
	
	$ns = [
		'sidx'			=> $s['sidx'],
		'instructor'	=> $s['instructor'],
		'date'			=> $s['date'],
		'slot'			=> $s['timeslot'],
		'park'			=> $s['park'],
		'expertise'		=> $s['expertise'],
		'oidx'			=> $s['oidx'],
		'studentNum'	=> $s['studentNum'],
		'fee'			=> $s['fee'],
		'arranged'		=> $s['arranged'],
		'rule'			=> 0,
		'reservation'	=> 0,
		'noshow'		=> $s['noshow'],
		'createDateTime'=> $s['createDateTime'],
		'modifyDateTime'=> $s['modifyDateTime'],
	];//_v($ns);

	$db2->insert('schedules', $ns);
}
$_t2 = microtime(true);

echo 'Cost' . ($_t2-$_t1) ."sec.\n";

