<?php
session_start();
require('includes/sdk.php');
$filters = array(
    'target'	=>	FILTER_SANITIZE_STRING,
    'section'	=>	FILTER_SANITIZE_STRING,
);
$_IN = filter_var_array($_REQUEST, $filters);//_v($_IN);


$DB = new db();
$target = $name = $section  = null;

if( !empty($_IN['target'])){
	// 雪場
	$sql = "SELECT * FROM `parks` WHERE `name`='{$_IN['target']}'";
	//_d($sql);
	$r = $DB->QUERY('SELECT',$sql);
	
	if(isset($r[0]['name'])){
		foreach($r as $n => $v){
			//var_dump($v);
			$section_content[$v['section']]=$v['content'];
		}
		$target = 'park';
		$name = $r[0]['name'];
	}
	
	// 教練
	//-----------------------------------------------------------------------------


	$sql = "SELECT * FROM `instructors` WHERE `name`='{$_IN['target']}'"; //_d($sql);
	$r2 = $DB->QUERY('SELECT',$sql);//_v($r2);
	if(isset($r2[0]['name'])){
		foreach($r2 as $n => $v){
			//var_dump($v);
			$section_content[$v['section']]=$v['content'];
		}
		//var_dump($section_content);
		$target = 'instructor';
		$name = $r2[0]['name'];
	}
	
} 

if( !empty($_IN['section']) ){
	$section = $_IN['section'];
}

if( !empty($_IN['target']) &&  $_IN['target']=='ui-backup' ){
	$target = 'UIbackup';
}

//route to page
//_d("target={$target}, name={$name}, section={$section}");
switch ($target) {
	case 'park':
		//echo 'load park/'.$section;
		require('park.php');
		break;	
	case 'instructor':
		require('instructor.php');
		break;
	case 'UIbackup':
		//echo 'ui-backup/'.$section.'/'.$_SERVER['PHP_SELF'];
		//require('ui-backup/'.$section.'/'.$_SERVER['PHP_SELF']);
		//echo 'x'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		break;	
	default:
		//echo 'Routing Error!<br>'.$_SERVER['REQUEST_URI'];
		//echo $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		//_v($_REQUEST);
		//Header('Location: '.$_SERVER['REQUEST_HOST'].'/'.$_SERVER['REQUEST_URI']);
		// redirect to original url 
		//Header('Location: https://'.domain_name.'/'.$target).'/'.$section;
		Header('Location: https://diy.ski');
		break;
}




?>
