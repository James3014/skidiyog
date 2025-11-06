<?php
session_start();
date_default_timezone_set("Asia/Taipei");
ini_set('error_reporting', E_ALL);
ini_set('display_startup_errors', TRUE);
ini_set('display_errors', TRUE);
function _d($str){echo "<font color='red'>$str</font><hr>";}
function _v($var){var_dump($var); echo '<hr>';}
function _h($var){echo '<!--'; var_dump($var); echo "-->\n";}
function _f($file){return end(explode('/',$file));}//db needed
function _j($var){echo json_encode($var,JSON_UNESCAPED_UNICODE);}
function _go($url){echo "<script> window.location.replace('".$url."') </script>";}
function _alert($msg){echo "<script> alert('".$msg."') </script>";}
function _dbg($msg){echo "<script> console.log('".$msg."') </script>";}

//define("domain_name", "mj.diy.ski");
if(isset($_SERVER['HTTP_HOST'])){
	define("domain_name", $_SERVER['HTTP_HOST']);
}
define("teaching_domain_name","teaching.diy.ski");
define("token_slot","newdiyski");

require('db.class.php');
// AWS autoloader disabled - load only when needed in specific files
// require 'aws/aws-autoloader.php';
require('ko.class.php');
require('mj.class.php');
require('crypto.class.php');
require('payment.class.php');
require('config.php');

function _msg($msg){
	global $SYSMSG;
	if(isset($SYSMSG[$msg])){
		return "<script> alert('".$SYSMSG[$msg]."') </script>";
	}
}