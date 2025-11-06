<?php
require('../includes/sdk.php');

var_dump( time() - strtotime('-15 years'));exit();
echo date('Y-m-d', strtotime('-179 months'));exit();

$diff = strtotime('2007-11-06') - strtotime('-15 years');
var_dump($diff);exit();

$ACCOUNT = new MEMBER();
$db = new db();

$ok = $ACCOUNT->send_mail([
    'email' 	=> 'eric@inncom.cloud',
    'subject'	=> '測試信件',
    'content'	=> '測試內容',
]);

var_dump($ok);