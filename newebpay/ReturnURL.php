<?php
require('../includes/sdk.php');
require('../includes/mpg.class.php');

$m = new mpg();
$in = $_POST;//_j($in);

//測試資料
//$in = json_decode('{"Status":"SUCCESS","MerchantID":"MS3427622508","Version":"1.5","TradeInfo":"2d89cb2af494edcb0b96824e9ae05d1aca46f6bbdc6f52f34bc2de199f5cfcdcc3b30f3b7726b5285aa5c50d71f2116a772a1b0226a0c2d6e095327b5b8ac6e4a3fe3f134d33c0f8b322b37a770f524c730ced71b2becf5f9537a80367ca3d3cf3c54e90d725462acf93c6379a3d2d5c777ee866295e90cf2a67c459a82e7f3d8b3a9c24e44b42806ab6c15feb9073593c811014348f2ea69875db8212e0e46de4fb792f09431e01a8a7790b1a510be6a6812dc3ec849e571584409fb66c212242dcb47cda689993ca152cfc13cc06759c687fad7f989b06c795bb31f9615ace9104423eacc50e8cc9ca76932a3b3be879896a52372f56a17befb9240cfa7ff168d6033dc38c1214f4f54b20a13531ec8eb24903c9596ceb827cb056ed7e5ca1a713c0bad3e9f76b067edfeae24aad2b2d85f9df3909b350fb848ef1d88fef10503d5228b50fff7924a86cd2b03c8c44e74c917b7acf5f4beeeaaa37856431292c96b2ee433e08c29a4f9d6c43a3896f49386a563e3dd5e319d7d7882b8f4bed25fada22dd0346aa515e6bd61aa8736e85e02372a159ef798dc200b98e64d75fa9214fce4cd5cf0eb5959ad7201c415df0de2077d5b52136a9962e6fd5c5e0461f65d91a632471dfac43cac45cc6cc7dff2187e2b740fa930d8674b2a429ea91","TradeSha":"83AA1A255805490EE00CAFEB0D747EEC5A847FAF04CA7811F395EC0CD6AADC63"}', true);

//解密資料
$tradeInfo = json_decode($m->decrypt($in['TradeInfo']), true);//_d($tradeInfo);exit();

//紀錄交易結果
$payment = new Payment();
$in['resp'] = 'newebpayReturnURL';
$in['tradeInfo'] = $tradeInfo;
$payment->createLog($in);

$ko = new ko();
$oidx = substr($tradeInfo['Result']['MerchantOrderNo'], 4, 5);
$order = $ko->getOneOrderInfo(['oidx'=>$oidx]);//_j($order);exit();

$tradeInfo['Result']['RespondCode'] = isset($tradeInfo['Result']['RespondCode']) ? $tradeInfo['Result']['RespondCode'] : '4444'; // 付款人電子信箱錯誤 (格式錯誤) 時，沒有此欄位。
$ok = $payment->updateOrder([
    'allpay_PaymentType'    =>  $tradeInfo['Result']['Card6No'].'******'.$tradeInfo['Result']['Card4No'],
    'status'                =>  ($in['Status'] === 'SUCCESS') ? Payment::PAYMENT_SUCCESS : $tradeInfo['Result']['RespondCode'],//<==付款成功！success
    'allpay_ReturnURL'      =>  json_encode($tradeInfo, JSON_UNESCAPED_UNICODE),
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

if($in['Status'] === 'SUCCESS'){
    $ko->notify([
        'oidx'              => $oidx,
        'type'              => $lessonType,
        'resp'              => $tradeInfo['Result']['RespondCode'],
        'createDateTime'    => date('Y-m-d H:i:s'),
    ]);
}else{
    $ko->notify([
        'oidx'              => $oidx,
        'type'              => 'ecpayFail',
        'resp'              => $tradeInfo['Result']['RespondCode'],
        'createDateTime'    => date('Y-m-d H:i:s'),
    ]);
}

$msg = ($in['Status'] === 'SUCCESS') ? 'allpay_success' : 'allpay_fail';
Header("Location: https://diy.ski/my_order_list.php?msg={$msg}");
