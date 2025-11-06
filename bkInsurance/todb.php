<?php
require('../includes/sdk.php');
    $filters = array(
        'year'          =>  FILTER_SANITIZE_STRING,
        'month'         =>  FILTER_SANITIZE_STRING,        
        'oidx'        	=>  FILTER_SANITIZE_STRING,
        'insurance'     =>  FILTER_SANITIZE_STRING,
        'insuranceChecked'=>FILTER_SANITIZE_STRING,
        'insuranceMemo'	=>  FILTER_SANITIZE_STRING,
        'notify'        =>  FILTER_SANITIZE_STRING,
    );//_v($_POST);
    $in = filter_var_array(array_merge($_REQUEST,$_POST), $filters);//_v($in);exit();

$ko = new ko();
$msg = '';

if($in['notify']=='yes'){
    $ko->notify([
        'oidx'              => $in['oidx'],
        'type'              => 'insuranceNotify',
        'resp'              => '',
        'createDateTime'    => date('Y-m-d H:i:s'),
    ]);
    $ko->updateOrder([
      'insuranceLastNotify' => date('Y-m-d H:i:s')
    ],[
      'oidx'            => $in['oidx']
    ]);
    $msg = '通知信已發送！';
    Header("Location: index.php?year={$in['year']}&month={$in['month']}&insurance={$in['insurance']}&msg={$msg}");
    exit();
}

$ko->updateOrder([
	  'insuranceChecked'=> $in['insuranceChecked'],
	  'insuranceMemo'	=> $in['insuranceMemo']
   ],[
      'oidx'			=> $in['oidx']
]);
Header("Location: index.php?year={$in['year']}&month={$in['month']}&insurance={$in['insurance']}&msg={$msg}");
?>