<?php
require('../includes/sdk.php');
require_once('../vendor/autoload.php');

$base58 = new StephenHill\Base58();

if(isset($argv[1])){
	echo $base58->encode($argv[1])."\n\n";
	exit();
}

// for($i=1;$i<=80;$i++){
// 	$id = sprintf('%03d',$i*11+100);
// 	echo  $id . '=' . $base58->encode($id) . "\n";
// }

/* 產生教練代碼
$db = new DB();
$ko = new ko();
$instructor = $ko->getInstructorInfo();
sort($instructor);
foreach ($instructor as $i) {
	$refer = $base58->encode((string)($i['idx']*11+100));
	echo  "{$i['idx']}, {$i['name']}, {$refer}\n";
	$db->insert('refer',[
		'idx'=> $i['idx']*11+100,
		'refer'=> $refer,
		'from'=> $i['name'],
		'landingpage'=> $i['name']
	]);
}*/
