<?php
//歐付寶後台回覆交易狀況(信用卡&ATM)
require('../includes/sdk.php');

$filters = array(
    'MerchantTradeNo'   => FILTER_SANITIZE_STRING,
    'PaymentType'       => FILTER_SANITIZE_STRING,
    'RtnCode'           => FILTER_SANITIZE_STRING,
    'MerchantID'        => FILTER_SANITIZE_STRING,
);
$in = filter_var_array(array_merge($_REQUEST,$_POST), $filters);//_v($in);//exit();

$payment = new Payment();
$in['resp'] = 'ReturnURL';
$payment->createLog($in);

if (empty($in['MerchantTradeNo'])) {//一定要有訂單編號
    echo '0|Record Error';
    exit();
}

//避免重複通知
$ko = new ko();
$oidx = substr($in['MerchantTradeNo'], 4, 5);
$order = $ko->getOneOrderInfo(['oidx'=>$oidx]);//_j($order);exit();
if( $order['status']===Payment::PAYMENT_SUCCESS){
    $payment->createLog([
        'severity'  => 'ecPay',
        'oidx'      => $oidx,
        'msg'       => json_encode($order, JSON_UNESCAPED_UNICODE),
        'resp'      => '1|OK',
    ]);
    echo '1|OK';
    exit();
}

if(sizeof($order['schedule'])==0 && empty($order['gidx'])){//理論上不會發生. 除了團體課程
    $payment->createLog([
        'severity'  => 'ecPay',
        'oidx'      => $oidx,
        'msg'       => json_encode($order, JSON_UNESCAPED_UNICODE),
        'resp'      => '0|Record Error',
    ]);
    echo '0|Record Error';
    exit();
}

$ok = $payment->updateOrder([
        'allpay_PaymentType'    =>  $in['PaymentType'],
        'status'                =>  ($in['RtnCode']=='1') ? Payment::PAYMENT_SUCCESS : $in['RtnCode'],//<==付款成功！success
        'allpay_ReturnURL'      =>  json_encode($_POST, JSON_UNESCAPED_UNICODE),
        'MerchantId'            =>  $in['MerchantID'],
    ],[
        'oidx'                  =>  $oidx,
    ]
);

$lessonType = 'booking';
foreach ($order['schedule'] as $n => $s) {
    if($s['reservation']){
        $lessonType = 'reservation';
    }
}
if(!empty($order['gidx'])){
    $lessonType = 'group';
}//_v($lessonType);exit();

if($in['RtnCode']=='1'){
    $ko->notify([
        'oidx'              => $order['oidx'],
        'type'              => $lessonType,
        'resp'              => $in['RtnCode'],
        'createDateTime'    => date('Y-m-d H:i:s'),
    ]);
}else{
    $ko->notify([
        'oidx'              => $order['oidx'],
        'type'              => 'ecpayFail',
        'resp'              => $in['RtnCode'],
        'createDateTime'    => date('Y-m-d H:i:s'),
    ]);
}

$msg = ($ok===false) ? '0|Record Error' : '1|OK';
echo $msg;

$payment->createLog([
    'severity'  => 'ecPay',
    'oidx'      => $oidx,
    'msg'       => json_encode($order, JSON_UNESCAPED_UNICODE),
    'resp'      => $msg
]);
