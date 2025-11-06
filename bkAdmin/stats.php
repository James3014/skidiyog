<?php
require('../includes/sdk.php');


$db = new db();

$sql = "SELECT * FROM `1819-LessonFee`";
$lf1819 = $db->query('SELECT', $sql);//_v($res);

$sql = "SELECT * FROM `1819-ParkLessons`";
$pl1819 = $db->query('SELECT', $sql);//_v($res);

$sql = "SELECT * FROM `1920-LessonFee`";
$lf1920 = $db->query('SELECT', $sql);//_v($res);

$sql = "SELECT * FROM `1920-ParkLessons`";
$pl1920 = $db->query('SELECT', $sql);//_v($res);

$sql = "SELECT * FROM `2223-LessonFee`";
$lf2223 = $db->query('SELECT', $sql);//_v($res);

$sql = "SELECT * FROM `2223-ParkLessons`";
$pl2223 = $db->query('SELECT', $sql);//_v($res);

$sql = "SELECT * FROM `2324-LessonFee`";
$lf2324 = $db->query('SELECT', $sql);//_v($res);

$sql = "SELECT * FROM `2324-ParkLessons`";
$pl2324 = $db->query('SELECT', $sql);// _v($pl2324);

?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
	<style>
	table{
		border-collapse: collapse;
	}
	td{
		border: 1px solid #000;
	}
	</style>
</head>
<body>
<h3>2324 營收統計-雪場</h3>
<table>
	<tr>
		<td>雪場</td><td>堂數</td><td>學生數</td><td>學費</td>
	</tr>
	<?php $total=0; foreach ($pl2324 as $i => $r) { $total+=$r['fee'];?>
	<tr>
		<td><?=$r['park']?></td>
		<td><?=number_format($r['lessons'])?></td>
		<td><?=number_format($r['students'])?></td>
		<td align="right"><?=number_format($r['fee'])?></td>
	</tr>
	<?php }?>
	<tr><td></td><td></td><td></td><td><?=number_format($total)?></td></tr>
</table>

<h3>2324 營收統計-教練</h3>
<table>
	<tr>
		<td>教練</td><td>學費</td>
	</tr>
	<?php $total=0; foreach ($lf2324 as $i => $r) { $total+=$r['fee'];?>
	<tr>
		<td><?=$r['instructor']?></td>
		<td align="right"><?=number_format($r['fee'])?></td>
	</tr>
	<?php }?>
	<tr><td></td><td><?=number_format($total)?></td></tr>
</table>


<h3>2223 營收統計-雪場</h3>
<table>
	<tr>
		<td>雪場</td><td>堂數</td><td>學生數</td><td>學費</td>
	</tr>
	<?php $total=0; foreach ($pl2223 as $i => $r) { $total+=$r['fee'];?>
	<tr>
		<td><?=$r['park']?></td>
		<td><?=number_format($r['lessons'])?></td>
		<td><?=number_format($r['students'])?></td>
		<td align="right"><?=number_format($r['fee'])?></td>
	</tr>
	<?php }?>
	<tr><td></td><td></td><td></td><td><?=number_format($total)?></td></tr>
</table>

<h3>2223 營收統計-教練</h3>
<table>
	<tr>
		<td>教練</td><td>學費</td>
	</tr>
	<?php $total=0; foreach ($lf2223 as $i => $r) { $total+=$r['fee'];?>
	<tr>
		<td><?=$r['instructor']?></td>
		<td align="right"><?=number_format($r['fee'])?></td>
	</tr>
	<?php }?>
	<tr><td></td><td><?=number_format($total)?></td></tr>
</table>

<h3>1920 營收統計-雪場</h3>
<table>
	<tr>
		<td>雪場</td><td>堂數</td><td>學生數</td><td>學費</td>
	</tr>
	<?php $total=0; foreach ($pl1920 as $i => $r) { $total+=$r['fee'];?>
	<tr>
		<td><?=$r['park']?></td>
		<td><?=number_format($r['lessons'])?></td>
		<td><?=number_format($r['students'])?></td>
		<td align="right"><?=number_format($r['fee'])?></td>
	</tr>
	<?php }?>
	<tr><td></td><td></td><td></td><td><?=number_format($total)?></td></tr>
</table>

<h3>1920 營收統計-教練</h3>
<table>
	<tr>
		<td>教練</td><td>學費</td>
	</tr>
	<?php $total=0; foreach ($lf1920 as $i => $r) { $total+=$r['fee'];?>
	<tr>
		<td><?=$r['instructor']?></td>
		<td align="right"><?=number_format($r['fee'])?></td>
	</tr>
	<?php }?>
	<tr><td></td><td><?=number_format($total)?></td></tr>
</table>

<h3>1819 營收統計-雪場</h3>
<table>
	<tr>
		<td>雪場</td><td>堂數</td><td>學生數</td><td>學費</td>
	</tr>
	<?php $total=0; foreach ($pl1819 as $i => $r) { $total+=$r['fee'];?>
	<tr>
		<td><?=$r['park']?></td>
		<td><?=number_format($r['lessons'])?></td>
		<td><?=number_format($r['students'])?></td>
		<td align="right"><?=number_format($r['fee'])?></td>
	</tr>
	<?php }?>
	<tr><td></td><td></td><td></td><td><?=number_format($total)?></td></tr>
</table>

<h3>1819 營收統計-教練</h3>
<table>
	<tr>
		<td>教練</td><td>學費</td>
	</tr>
	<?php $total=0; foreach ($lf1819 as $i => $r) { $total+=$r['fee'];?>
	<tr>
		<td><?=$r['instructor']?></td>
		<td align="right"><?=number_format($r['fee'])?></td>
	</tr>
	<?php }?>
	<tr><td></td><td><?=number_format($total)?></td></tr>
</table>


</body>
</html>