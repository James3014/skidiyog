<?php
require('../includes/sdk.php');

$filters = array(
    'key'      		=> FILTER_SANITIZE_STRING,
    'action'    	=> FILTER_SANITIZE_STRING,
    'id'         	=> FILTER_SANITIZE_STRING,
);//_v($_POST);
$in = filter_var_array(array_merge($_REQUEST,$_POST), $filters);//_v($in);//exit();

$in = [
	'oidx' 		=> crypto::dv($in['key']),//訂單編號
	'accepted'	=> crypto::dv($in['action']),//是/否
	'instructor'=> crypto::dv($in['id']),//教練名
];//_v($in);

$ko = new ko();
$parkInfo = $ko->getParkInfo();//_v($parkInfo);exit();
$instructorInfo = $ko->getInstructorInfo();

if( !in_array($in['accepted'], ['wait','true','false']) ||
	!is_numeric($in['oidx']) ||
	!isset($instructorInfo[$in['instructor']])
){
	Header("Location: messages.php?msg=resvLinkError");
	exit();
}

//檢查是否點過連結
//$accept = $ko->getAcception(['oidx'=>$in['oidx'],'instructor'=>$in['instructor']]);
$accept = $ko->getLastAcception($in['oidx'], $in['instructor']);//_v($accept);exit();
if(isset($accept[0]['oidx']) && ($accept[0]['accepted']!='wait')){
	$idx = Crypto::ev($accept[0]['idx']);
	Header("Location: messages.php?msg=resvAccepted&idx={$idx}");
	exit();
}

$ok = $ko->setAcception([
	'accepted'=>$in['accepted']
],[
	'oidx'=>$in['oidx'], 'instructor'=>$in['instructor']
]);//_v($ok);exit();

if(empty($ok)){
	Header("Location: messages.php?msg=resvFail&idx={$accept[0]['idx']}");
	exit();
}

$ko->notify([
	'oidx'              => $in['oidx'],
    'type'              => 'resvAcception',
    'resp'              => $in['accepted'],
    'createDateTime'    => date('Y-m-d H:i:s'),
]);

$idx = Crypto::ev($accept[0]['idx']);
Header("Location: messages.php?msg=resvResponsed&idx={$idx}");

?>
