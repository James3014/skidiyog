<?php
require('includes/sdk.php');
require('includes/ecpay.class.php');
if(!isset($_SESSION['user_idx'])){
  _go('https://'.domain_name.'/account_login.php');
}
$filters = array(
    'id'            => FILTER_SANITIZE_STRING,
);//_v($_POST);
$in = filter_var_array(array_merge($_REQUEST,$_POST), $filters);//_v($in);//exit();

$oidx = crypto::dv($in['id']);//_d($oidx);

if(!is_numeric($oidx)){
    echo 'Error Link!!!';
    exit();
}

$ko = new ko();
$ORDER_OBJ = new ORDER();
$payment = new payment();
$order = $ko->getOneOrderInfo(['oidx'=>$oidx]);//_j($order);exit();

//Random orderNo last 4 digi for ecPay
$new4Digi = $last4Digi = substr($order['orderNo'], 9, 3);//_d($last4Digi);
while($new4Digi==$last4Digi){
    $new4Digi = sprintf('%03d', rand(1,999));//_d($new4Digi);
}
$order['orderNo'] = substr($order['orderNo'], 0, 9) . $new4Digi;//_d($order['orderNo']);
//Change to the new orderNo
$ko->updateOrder(['orderNo'=>$order['orderNo']],['oidx'=>$oidx]);

//Change to the payment student
if($order['student'] != $_SESSION['user_idx']){
    $ORDER_OBJ->update($oidx,['student'=>$_SESSION['user_idx']]);
}

$ko->log([
    'severity'  =>  'pay',
    'user'      =>  $_SESSION['user_idx'],
    'oidx'      =>  $oidx,
    'msg'       =>  json_encode($order, JSON_UNESCAPED_UNICODE),
]);

$ko->notify([
    'oidx'              => $oidx,
    'type'              => 'paying',
    'resp'              => '',
    'createDateTime'    => date('Y-m-d H:i:s'),
]);

//測試用-
if($order['student']==48){
    $order['paid'] = 1;
}

if(1){//Newebpay
require('includes/mpg.class.php');
$m = new mpg();

//轉址至信用卡付款
$html = $m->pay([
    'orderNo' => $order['orderNo'],
    'price' => $order['paid'],
    'description' => 'SKIDIY 滑雪教學訂金',
    'email' => $_SESSION['account'],
]);
echo $html;

}else{
$accountInfo = $payment->getECPayAccount('B');//exit();

try{
    $ec = new AllInOne();
    $ec->ServiceURL                 = Payment::ECPAY_SERVICE_URL;
    $ec->HashKey                    = $accountInfo['HashKey'];
    $ec->HashIV                     = $accountInfo['HashIV'];
    $ec->MerchantID                 = $accountInfo['MerchantID'];

    /* 基本參數 */
    $ec->Send['ReturnURL']          = Payment::ECPay_ReturnURL;
    $ec->Send['OrderResultURL']     = Payment::ECPay_OrderResultURL;

    /* SKIDIY */
    $ec->Send['MerchantTradeNo']    = $order['orderNo'];//廠商的交易編號,該交易編號不可重複。
    $ec->Send['MerchantTradeDate']  = date('Y/m/d H:i:s');//廠商的交易時間。
    $ec->Send['TotalAmount']        = (int) $order['paid'];//交易總金額。
    $ec->Send['TradeDesc']          = 'SKIDIY 滑雪教學訂金';

    $ec->Send['ChoosePayment']      = PaymentMethod::Credit;//限信用卡。
    $ec->Send['IgnorePayment']      = "CVS#BARCODE#Alipay#Tenpay#TopUpUsed"; // 不顯示的付款方式。例(排除支付寶與財富通): Alipay#Tenpay // 加入選購商品資料。
        
    $ec->Send['Remark']             = 'SKIDIY 滑雪教學訂金';
    $ec->Send['ChooseSubPayment']   = PaymentMethodItem::None;//使用的付款子項目。
    $ec->Send['NeedExtraPaidInfo']  = ExtraPaymentInfo::No;//是否需要額外的付款資訊。
    
    array_push($ec->Send['Items'], [
        'Name'      => 'SKIDIY 自助滑雪教學訂金',
        'Price'     => $order['paid'],
        'Currency'  => '台幣',
        'Quantity'  => 1,
        'URL'       => '',//產品介紹網址
    ]);

    /* 產生訂單 HTML */
    $html = $ec->CheckOut();
    $payment->createLog($html);
    echo $html;

} catch (Exception $e) {
    // 例外錯誤處理。
    echo $e->getMessage();
    //Header('Location: /index.php?msg=payment_fail'); exit();
    throw $e; 
}
}//ECPay
