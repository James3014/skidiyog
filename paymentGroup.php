<?php
require('includes/sdk.php');
if(!isset($_SESSION['user_idx'])){
  _go('https://'.domain_name.'/account_login.php');
}
$filters = array(
    'gidx'        =>  FILTER_SANITIZE_NUMBER_INT,
    'price'       =>  FILTER_SANITIZE_NUMBER_INT,
    'prepaid'     =>  FILTER_SANITIZE_NUMBER_INT,
    'paid'        =>  FILTER_SANITIZE_NUMBER_INT,
    'payment'     =>  FILTER_SANITIZE_NUMBER_INT,
    'requirement' =>  FILTER_SANITIZE_STRING,
    'exchangeRate'=>  FILTER_SANITIZE_STRING,
    'currency'	  =>  FILTER_SANITIZE_STRING,
);//_v($_POST);
$in = filter_var_array(array_merge($_REQUEST,$_POST), $filters);//_v($in);exit();

$loggedStudent = $_SESSION['user_idx'];

$ko = new ko();
$order = array_merge($in,[
	'gidx'			=> $in['gidx'],
	'orderNo'		=> $ko->genOrderNo(),
	'student'		=> $loggedStudent,
	'status'		=> 'create',
//	'refer'			=> empty($_POST['refer']) ? '' : $_POST['refer'],
//	'detail'		=> json_encode($detail, JSON_UNESCAPED_UNICODE),
	'timeout'		=> 30,
	'createDateTime'=> date('Y-m-d H:i:s'),
]);//_j($order);exit();
$oidx = $ko->placeOrder($order);//_d($oidx);exit();

if(empty($oidx)){
	Header('Location: class_group_list.php?msg=groupLessonFail');
	exit();
}

$oidx = crypto::ev($oidx);//_d($oidx);
Header('Location: pay.php?id='.$oidx);
?>


