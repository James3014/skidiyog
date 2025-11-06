<?php
require('../includes/sdk.php');

$sql = "SELECT * FROM `schedules` where DATE(`date`)>='2020-01-01' AND `oidx`!=0 AND `park`='naeba' ORDER BY `date`";
$db = new DB();
$res = $db->query('SELECT', $sql);

$s = [];
foreach ($res as $r) {
	if(@!in_array($r['date'], $s[$r['instructor']])){
		$s[$r['instructor']][] = $r['date'];
	}
}

header("Content-type: text/csv");
header("Content-Type: application/force-download");
header("Content-Type: application/octet-stream");
header("Content-Type: application/download");
header("Content-Disposition: attachment;filename=naeba-instructors.csv");

foreach ($s as $i => $r) {
	echo "{$i}," . count($r) . "\n";
}