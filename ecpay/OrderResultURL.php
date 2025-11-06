<?php
//歐付寶付完款後從網頁導回來的入口//信用卡
require('../includes/sdk.php');

$filters = array(
    'MerchantTradeNo'   => FILTER_SANITIZE_STRING,
    'RtnCode'           =>  FILTER_SANITIZE_STRING,
);
$in = filter_var_array(array_merge($_REQUEST,$_POST), $filters);//_v($in);//exit();

$payment = new Payment();
$in['resp'] = 'OrderResultURL';
$payment->createLog($in);

if (empty($in['MerchantTradeNo'])) {//一定要有訂單編號
    echo '0|Record Error';
    exit();
}

$ok = $payment->updateOrder([
        'allpay_OrderResultURL' =>  json_encode($_POST, JSON_UNESCAPED_UNICODE)
    ],[
        'orderNo'               =>  $in['MerchantTradeNo'],
    ]
);

$msg = ($in['RtnCode']=='1') ? 'allpay_success' : 'allpay_fail';

Header("Location: ../my_order_list.php?msg={$msg}");