<?php
require('../includes/sdk.php');
$filters = array(
	'instructor'    =>  FILTER_SANITIZE_FULL_SPECIAL_CHARS,
);//_v($_POST);
$in = filter_var_array(array_merge($_REQUEST,$_POST), $filters);//_v($in);//exit();

$ko = new ko();
$parkInfo = $ko->getParkInfo();
$instructors = $ko->getInstructorInfo();//_v($instructors);
$db = new DB();
$sql = "SELECT 	`date`, `slot`, `park`, `instructor`, `studentNum`, `expertise`, s.`oidx`, `fee`, 
				`prepaid`, `discount`, `exchangeRate`, `payment`, `price`, `requirement`, `note`, `memo`, `backup`,
        		r.`from`, `arranged`, s.`noshow`, o.`noshow` AS `o-noshow`, `status`, o.`student`, `email`, `country`
FROM `schedules` AS `s` 
LEFT JOIN `orders` AS `o` ON s.`oidx`=o.`oidx` 
LEFT JOIN `refer` AS `r` ON o.`refer`=r.`refer`
LEFT JOIN `members_v2` AS `m` ON m.`idx`=o.`student`
WHERE s.`instructor`='{$in['instructor']}'
AND DATE(s.`date`) BETWEEN '2023-11-01' AND '2024-04-20' AND s.`oidx`!=0 
ORDER BY s.`date` ASC, s.`slot` ASC";//_d($sql);

$data = $db->query('SELECT', $sql);//_v($data);
$oidx = [];
$accumulate = 0;
$total = 0;
$totalBalance = 0;

function percentage($fee){
	if($fee>=2000001){
		return '25%';
	}else if($fee>=1000001){
		return '20%';
	}else if($fee>=100001){
		return '15%';
	}else{
		return '10%';
	}
}

function isOld($student){
	global $db;
	$sql = "SELECT DISTINCT(`day1_class`) AS `day1_class` FROM `orders` 
			WHERE `student`={$student} AND `status`='success' 
			ORDER BY `createDateTime` ASC";
	$data = $db->query('SELECT', $sql);//_v($data);
	$resp = [];
	foreach ($data as $r) {
		$resp[$r['day1_class']] = $r['day1_class'];
	}
	if(count($resp)==1){
		return null;
	}else{
		return implode('<br>', $resp);
	}
}

?>
<html>
	<body>
		<style>
			body{font-size: 10px;}
			table{border-collapse: collapse;}
			td{border: 1px solid #000; padding: 2px;}
		</style>
		<form action="statement.php" method="post">
		<select name="instructor">
			<option value="0">請選擇教練</option>
		<?php foreach ($instructors as $i) { ?>
			<option value="<?=$i['name']?>" <?=($in['instructor']==$i['name'])?'selected':''?>><?=$i['name']?></option>
		<?php } ?>
		</select>
		<input type="submit" value="查詢">
		</form>
		<table>
			<tr>
				<td>-</td>
				<td>訂單編號</td>
				<td>上課日期</td>
				<td>堂次</td>
				<td>雪場</td>
				<td>教練</td>
				<td>學生人數</td>
				<td>課程</td>
				
				<td>該堂學費</td>
				<td>學費</td>
				<td>訂金</td>
				<td>優惠</td>
				<td>尾款</td>
				
				<td>匯率</td>
				<td>實收學費</td>
				<td>累加學費</td>
				<td>實收訂金</td>
				<td>手續費</td>
				<td>餘額</td>
				
				<td>介紹來源</td>
				<td>有經排課</td>
				<td>是否舊生</td>
				<td>單堂Noshow</td>
				<td>訂單Noshow</td>
				<td>訂單狀態</td>

				<td>學生備註</td>
				<td>系統註記</td>
				<td>尾款差額</td>
				<td>學生國籍</td>
				<td>學生帳號</td>
			</tr>

			<?php foreach ($data as $n => $s) { 
				if(in_array($s['oidx'], $oidx)){
					$first = false;
				}else{
					$first = true;
					$oidx[] = $s['oidx'];
				}
				$total += $s['fee'];
				$lessonFee = $parkInfo[$s['park']]['base'] + ($parkInfo[$s['park']]['unit']*$s['studentNum']);
				$beforeCharge = percentage($accumulate);
				$accumulate += $lessonFee;
				$afterCharge = percentage($accumulate);
				if($beforeCharge==$afterCharge){
					$charge = $afterCharge;
					$p = (int) substr($afterCharge, 0, -1);
					$balance = $parkInfo[$s['park']]['deposit'] - ($p * $lessonFee / 100);
				}else{
					$charge = "{$beforeCharge}+{$afterCharge}";
					$balance = 0;
				}
				$totalBalance += $balance;

				$backup = json_decode($s['backup'], true);
			?>
			<tr>
				<td><?=$n+1;?></td>
				<td><?=$s['oidx']?></td>
				<td><?=$s['date']?></td>
				<td><?=$s['slot']?></td>
				<td><?=$s['park']?></td>
				<td><?=$s['instructor']?></td>
				<td><?=$s['studentNum']?></td>
				<td><?=$s['expertise']?></td>

				<td><?=$s['fee']?></td>
				<?php if($first){?>
					<td><?=$s['price']?></td>
					<td><?=$s['prepaid']?></td>
					<td><?=$s['discount']?></td>
					<td><?=$s['payment']?></td>
					<td><?=$s['exchangeRate']?></td>
				<?php }else{ ?>
					<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
					<td>&nbsp;</td><td>&nbsp;</td>
				<?php } ?>
				<td><?=$lessonFee?></td>
				<td><?=$accumulate?></td>
				<td><?=$parkInfo[$s['park']]['deposit']?></td>
				<td><?=$charge?></td>
				<td><?=$balance?></td>

				<td><?=$s['from']?></td>
				<td><?=empty($s['arranged'])?'':'排'?></td>
				<td><?=isOld($s['student'])?></td>	

				<td><?=empty($s['noshow'])?'':'缺'?></td>
				<td><?=empty($s['o-noshow'])?'':'曠'?></td>
				<td><?=($s['status']=='success')?'':$s['status']?></td>

				<?php if($first){ ?>
					<td><?=$s['requirement']?><br><?=$s['note']?></td>
					<td><?=$s['memo']?></td>
					<td>
						<?php if($s['payment'] !== $backup['payment']){ ?>
							<span style="color: <?=($s['payment']-$backup['payment']>0)?'blue':'red'?>"><?=$s['payment']-$backup['payment']?></span>
						<?php } ?>
					</td>
					<td><?=$s['country']?></td>
					<td><?=$s['email']?></td>
				<?php }else{ ?>
					<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
				<?php } ?>

				

			</tr>
			<?php }//foreach ?>

			<tr>
				<td colspan="8">&nbsp;</td>
				<td><?=$total?></td>
				<td colspan="5">&nbsp;</td>
				<td><?=$accumulate?></td>
				<td colspan="3">&nbsp;</td>
				<td><?=$totalBalance?></td>
				<td colspan="11">&nbsp;</td>
			</tr>

		</table>
	</body>
</html>