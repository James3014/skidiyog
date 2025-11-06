<?php
require('../includes/sdk.php');

$d = date('Y-m-d');
$sql = "SELECT * FROM `schedules` where DATE(`date`)>='{$d}' AND `oidx`!=0 AND `park`='naeba' ORDER BY `date`";
$db = new DB();
$res = $db->query('SELECT', $sql);

//每天有哪些教練在雪場
$s = [];
foreach ($res as $r) {
	if(@!in_array($r['instructor'], $s[$r['date']])){
		$s[$r['date']][] = $r['instructor'];
	}
}

//var_dump($s);
header("Content-type: text/csv");
header("Content-Type: application/force-download");
header("Content-Type: application/octet-stream");
header("Content-Type: application/download");
header("Content-Disposition: attachment;filename=naeba-instructors.csv");

foreach ($s as $d => $i) {
	echo "{$d}," . implode(',', $i) . "\n";
}