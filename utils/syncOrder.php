<?php
require('../includes/sdk.php');

$db1 = new db('1718');
$db2 = new db('skidiy');

//orders
$sql = "SELECT * FROM `orders` WHERE `status`='success' ORDER BY `oidx` ASC";// LIMIT 10";
$res = $db1->query('SELECT', $sql);//_v($res);exit();

$_t1 = microtime(true);
foreach ($res as $n => $o) {
	$co = [
		'oidx'					=> $o['oidx'],
		'gidx'					=> 0,
		'orderNo'				=> $o['orderNo'],

		'student'				=> $o['student'],
		'requirement'			=> $o['requirement'],
		'note'					=> $o['note'],

		'memo'					=> '[18轉]'.$o['memo'],
		'status'				=> $o['status'],
		'timeout'				=> 20,

		'price'					=> $o['price'],
		'discount'				=> $o['discount'],
		'specialDiscount'		=> $o['specialDiscount'],

		'prepaid'				=> $o['prepaid'],
		'paid'					=> $o['paid'],
		'payment'				=> $o['payment'],

		'currency'				=> strtoupper($o['currency']),
		'exchangeRate'			=> $o['exchangeRate'],
		'allpay_OrderResultURL'	=> $o['allpay_OrderResultURL'],

		'allpay_ReturnURL'		=> $o['allpay_ReturnURL'],
		'allpay_PaymentType'	=> $o['allpay_PaymentType'],
		'MerchantId'			=> $o['MerchantId'],

		'insuranceChecked'		=> $o['insuranceChecked'],
		'insuranceMemo'			=> $o['insuranceMemo'],
		'refer'					=> $o['refer'],

		'createDateTime'		=> $o['createDateTime'],
		'modifyDateTime'		=> $o['modifyDateTime'],
	];

	$db2->insert('orders', $co);
}
$_t2 = microtime(true);

echo 'Cost' . ($_t2-$_t1) ."sec.\n";
