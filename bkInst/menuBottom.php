<style>
	.row{
		margin-left: 0rem;
    	margin-right: 0rem;
	}
</style>
<h4>特殊手機-功能選單</h4>
<div class="row">
	<?php if($instructor[$loggedInstructor]['jobType']!='support'){ ?>
	<div class="col s4">
        <a href="schedule.php">📅 上課排程</a></li>
	</div>
	<?php } ?>
	<div class="col s4">
		<a href="orders.php">💰 訂單資訊</a>
	</div>
	<div class="col s4">
		<a href="logout.php">登出<?=ucfirst($instructor[$loggedInstructor]['name'])?></a>
	</div>
</div>