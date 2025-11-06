<?php
require('includes/sdk.php');
if(!isset($_SESSION['user_idx'])){
  _go('https://'.domain_name.'/account_login.php');
}
//_v($_POST);exit();
$loggedStudent = $_SESSION['user_idx'];
if($_SESSION['type'] === 'instructor' || in_array($_SESSION['user_idx'], ['13361','8870','22009'])){
  $isAdmin = true;
  $_POST['timeout'] = empty($_POST['timeout']) ? 20 : $_POST['timeout'];
  if ($_SESSION['type'] === 'instructor') {
	$refer = '&refer='.$_SESSION['name'];
	$_SESSION['refer_hash'] = $_SESSION['name'];
  } else {
	$refer = ['13361'=>'leo','8870'=>'oboe', '22009'=>'lorraine'];
	$_SESSION['refer_hash'] = $refer[$_SESSION['user_idx']];
  	$refer = isset($refer[$_SESSION['user_idx']]) ? '&refer='.$refer[$_SESSION['user_idx']] : null;
  }
}else{
  $isAdmin = false;
}

$orderTimeout = ($isAdmin) ? $_POST['timeout'] : 20;

$payment = json_decode($_POST['payment'], true);//_j($payment);exit();
$lessons = $payment['lessons'];

//處理JS小數點
$payment['paid'] = round($payment['paid']);
//移除不要的欄位
$lessons = $payment['lessons'];
unset($payment['lessons']);
unset($payment['insurance']);

$ko = new ko();
$order = array_merge($payment,[
	'orderNo'		=> $ko->genOrderNo(),
	'student'		=> $loggedStudent,
	'requirement'	=> empty($_POST['requirement']) ? '' : $_POST['requirement'],
	'status'		=> 'create',	
	'refer'			=> empty($_SESSION['refer_hash']) ? 'n/a' : $_SESSION['refer_hash'],
	'timeout'		=> $orderTimeout,
	'createDateTime'=> date('Y-m-d H:i:s'),
]);//_j($order);exit();//_j(array_merge($order, $payment));exit();
//'refer'			=> empty($_POST['refer']) ? '' : $_POST['refer'],

//備份訂單資訊
$order['backup'] = json_encode(array_merge(['lessons'=>$lessons], $order), JSON_UNESCAPED_UNICODE);
$oidx = $ko->placeOrder($order);//_d($oidx);exit();

$lessonInstructions = [];
foreach ($lessons as $n => $s) {
	if($s['ruleId']=='reservation'){//申請開課
		$schedule = [
			'instructor'	=> $s['instructor'],
			'date'			=> $s['date'],
			'slot'			=> $s['slot'],
			'park'			=> $s['park'],
			'expertise'		=> $s['expertise'],
			'oidx'			=> $oidx,
			'studentNum'	=> $s['students'],
			'fee'			=> $s['fee'],
			'arranged'		=> in_array($_SESSION['user_idx'], ['2','3']) ? 1 : 0,
			'rule'			=> 0,
			'reservation'	=> 1,
			'createDateTime'=> date('Y-m-d H:i:s'),
		];
		//再檢查該申請是否有開課/訂課/條件
		$ok = $ko->addResvSchedule($schedule);

	}else if( empty($s['sidx']) && !empty($s['ruleId']) ){//條件開課
		$schedule = [
			'instructor'	=> $s['instructor'],
			'date'			=> $s['date'],
			'slot'			=> $s['slot'],
			'park'			=> $s['park'],
			'expertise'		=> $s['expertise'],
			'oidx'			=> $oidx,
			'studentNum'	=> $s['students'],
			'fee'			=> $s['fee'],
			'arranged'		=> in_array($_SESSION['user_idx'], ['2','3']) ? 1 : 0,
			'rule'			=> $s['ruleId'],
			'reservation'	=> 0,
			'createDateTime'=> date('Y-m-d H:i:s'),
		];
		$ok = $ko->addSchedule($schedule);
		$ok = $ko->updateRuleLesson(['matched'=>1],['idx'=>$s['ruleId']]);

	}else if( !empty($s['sidx']) && empty($s['ruleId']) ){//指定開課
		//檢查原開課是否可被訂課
		$_sche = $ko->readSchedule(['sidx'=>$s['sidx']]);
		if(isset($_sche[0]['sidx']) && $_sche[0]['oidx']!=0 ){//已被訂課
			echo 'Payment data error, please try again!!';
            exit();
		}

		$data = [
			'expertise'		=> $s['expertise'],
			'oidx'			=> $oidx,
			'studentNum'	=> $s['students'],
			'fee'			=> $s['fee'],
			'arranged'		=> in_array($_SESSION['user_idx'], ['2','3']) ? 1 : 0,
		];
		$ok = $ko->updateSchedule($data, ['sidx'=>$s['sidx']]);

	}else{
		echo 'Payment fail.';
		exit();
	}
	$lessonInstructions[$s['instructor']] = $s['instructor'];
}//foreach lesson

$oidx = crypto::ev($oidx);//_d($oidx);
if(!$isAdmin){//學生直轉付款頁面
	Header("Location: pay.php?id={$oidx}");
	exit();
}
?>

<!DOCTYPE html>
  <html>
    <head>
      <?php require('pageHeader.php'); ?>	
      <?php require('head.php'); ?>
      <style>
      div{
      	font-size: 0.8rem;
      }
      </style>
    </head>

    <body>
    <blockquote>
      <h5>支付方式-刷卡訂單連結</h5>
      <div>https://diy.ski/paylink.php?id=<?=$oidx?><?=$refer?></div>
    </blockquote>

    <blockquote>
      <h5>支付方式-微信支付連結</h5>
      <div>https://diy.ski/paywechat.php?id=<?=$oidx?><?=$refer?></div>
    </blockquote>

      <!--JavaScript at end of body for optimized loading-->
      <script src="assets/js/materialize.min.js"></script>
      <script>
      $(document).ready(function(){
      });
      </script>
    </body>
  </html>

