<?php
require('../includes/sdk.php');
$filters = array(
	'oidx'    =>  FILTER_SANITIZE_STRING,
);//_v($_POST);
$in = filter_var_array(array_merge($_REQUEST,$_POST), $filters);//_v($in);//exit();

$ko = new ko();
$parkInfo = $ko->getParkInfo();
$instructors = $ko->getInstructorInfo();//_v($instructors);
$db = new DB();

if($in['oidx']){
	$sql = "SELECT * FROM `orders` WHERE `oidx`={$in['oidx']}";//_d($sql);
	$order = $db->query('SELECT', $sql);//_v($order);
	$sql = "SELECT * FROM `schedules` WHERE `oidx`={$in['oidx']}";//_d($sql);
	$schedules = $db->query('SELECT', $sql);//_v($schedules);

	if(count($schedules)==0){
		$schedules = json_decode($order[0]['backup'], true);
		$schedules = $schedules['lessons'];//_v($schedules);
	}
}
?>
<html>
	<body>
		<style>
			body{font-size: 10px;}
			table{border-collapse: collapse;}
			td{border: 1px solid #000; padding: 2px;}
			cr{color: #f00;}
			td{padding: 3px 12px;}
		</style>
		<form action="refund.php" method="post">
		<input type="text" name="oidx" placeholder="請輸入訂單編號">
		<input type="submit" value="查詢">
		</form>
		
		<?php if(!empty($order)){ ?>
		
		訂單資訊：
		<table>
				<tr><td>訂單編號</td><td><?=$order[0]['oidx']?></td></tr>
				<tr><td>綠界編號</td><td><?=$order[0]['orderNo']?></td></tr>
				<tr><td>學生備註</td><td><?=$order[0]['requirement']?></td></tr>
				<tr><td>系統備註</td><td><?=$order[0]['note']?></td></tr>
				<tr><td>管理員備註</td><td><?=$order[0]['memo']?></td></tr>
				<tr><td><cr>訂單狀態</cr></td><td><cr><?=$order[0]['status']?></cr></td></tr>

				<tr><td>學費</td><td><?=number_format($order[0]['price'])?></td></tr>
				<tr><td>折扣</td><td><?=number_format($order[0]['discount'])?></td></tr>
				<tr><td>特費</td><td><?=number_format($order[0]['specialDiscount'])?></td></tr>
				<tr><td>訂金</td><td><?=number_format($order[0]['prepaid'])?></td></tr>
				<tr><td>匯率</td><td><?=number_format($order[0]['exchangeRate'])?></td></tr>
				<tr><td>刷卡</td><td><?=number_format($order[0]['paid'])?> NTD</td>
				<tr><td>尾款</td><td><?=number_format($order[0]['payment'])?></td></tr>
		</table>
		<hr>
		課程資訊
		<table>
			<tr>
				<td>日期</td><td>堂次</td><td>雪場</td><td>教練</td><td>課程</td><td>人數</td><td>學費</td><td>註記</td>
			</tr>
			<?php foreach ($schedules as $s) { ?>
				<tr>
					<td><?=$s['date']?></td>
					<td><sub>第</sub><?=$s['slot']?><sub>堂</sub></td>
					<td><?=$s['park']?></td>
					<td><?=$s['instructor']?></td>
					<td><?=$s['expertise']?></td>
					<td><?=(empty($s['students'])?$s['studentNum']:$s['students'])?><sub>位</sub></td>
					<td><?=$s['fee']?></td>
					<td><?=(empty($s['noshow'])?'-':'<cr>noshow</cr>')?></td>
				</tr>
			<?php }?>
		</table>
		<?php } ?>

	</body>
</html>