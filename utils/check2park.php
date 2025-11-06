<?php
require('../includes/sdk.php');

$data = [];

$db = new db();
$sql = "SELECT * FROM `schedules` WHERE DATE(`date`) BETWEEN '2018-11-20' AND '2019-04-30' AND `oidx`!=0";
$res = $db->query('SELECT', $sql);

$cnt = 0;
foreach ($res as $s) {
	$cnt++;
	if(empty($data[$s['instructor']][$s['date']])){
		$data[$s['instructor']][$s['date']][] = $s['park'];
	}else{
		if(!in_array($s['park'], $data[$s['instructor']][$s['date']])){
			echo "{$s['instructor']}, {$s['date']}, {$s['slot']} @ {$s['park']} & {$data[$s['instructor']][$s['date']][0]} <br>";
		}
	}
}
echo "Total check {$cnt}.";

//echo json_encode($data);