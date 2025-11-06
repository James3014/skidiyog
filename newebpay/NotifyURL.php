<?php
require('../includes/sdk.php');
require('../includes/mpg.class.php');

$m = new mpg();
$in = $_POST;//_j($in);

//解密資料
$tradeInfo = json_decode($m->decrypt($in['TradeInfo']), true);//_d($tradeInfo);exit();

//紀錄交易結果
$payment = new Payment();
$in['resp'] = 'newebpayNotifyURL';
$in['tradeInfo'] = $tradeInfo;
$payment->createLog($in);

exit();