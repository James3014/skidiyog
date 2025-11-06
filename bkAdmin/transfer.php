<?php
require('../includes/sdk.php');

$db = new DB();

$sql = "SELECT DISTINCT(`oidx`) FROM skidiy.schedules WHERE `oidx` != '' AND `noshow`=0
	AND DATE(`date`) BETWEEN '2024-11-01' AND '2025-04-15'
    ORDER BY `oidx`";

$res = $db->query('SELECT', $sql);

$orders = [];
foreach ($res as $r) {
  $sql = "SELECT * FROM `orders` WHERE `oidx`={$r['oidx']}";
  $o = $db->query('SELECT', $sql)[0];//_v($o);
  $sql = "SELECT * FROM `members_v2` WHERE `idx`={$o['student']}";
  $m = $db->query('SELECT', $sql)[0];//_v($s);

  $sql = "SELECT * FROM `schedules` WHERE `oidx`={$o['oidx']} ORDER BY `date`,`slot`";
  $sch = $db->query('SELECT', $sql);
  $lessons = [];
  foreach ($sch as $s) {
    $sql = "SELECT * FROM `instructorInfo` WHERE `name`='{$s['instructor']}'";
    $i = $db->query('SELECT', $sql)[0];
    $expertise = $i['expertise'] === 'both' ? ['sb','ski'] : [$s['expertise']];

    $lessons[] = [
      'date' => $s['date'],
      'instructor' => $s['instructor'],
      'slot' => (int) $s['slot'],
      'park' => $s['park'],
      'isOpen' => true,
      'expertises' => $expertise,
      'orderedExpertise' => $s['expertise'],
      'studentNumber' => $s['studentNum'],
      'orderId' => '24TO'.$o['oidx'],
      'ruleId' => null,
      'applyLessonId' => null,
      'isAbsent' => false,
      'createdAt' => $o['createDateTime'],
      'updatedAt' => $o['modifyDateTime'],
    ];
  }

  $orders[] = [
    'memberId' => $m['email'],
    'memberName' => $m['name'],
    'orderNo' => '24TO'.$o['oidx'],
    'transactionId' => null,
    'memberCurrency' => 'NTD',
    'parkCurrency' => 'JPY',
    'exchangeRate' => $o['exchangeRate'],
    'price' => $o['price'],
    'discount' => $o['discount'],
    'depositOfParkCurrency' => $o['prepaid'],
    'depositOfMemberCurrency' => $o['paid'],
    'specialDiscount' => $o['specialDiscount'],
    'payment' => $o['payment'],
    'refundAmount' => null,
    'requirementNote' => $o['requirement'],
    'instructorNote' => $o['note'],
    'adminNote' => $o['memo'],
    'referer' => $o['refer'] === 'n/a' ? null : $o['refer'],
    'feeInfo' => null,
    'status' => $o['status'] === 'create' ? 'pending' : $o['status'],
    'cancelInfo' => null,
    'linkInfo' => null,
    'applyInfo' => null,
    'createdAt' => $o['createDateTime'],
    'updatedAt' => $o['modifyDateTime'],
    'lessons' => $lessons,
  ];
}

_j($orders);