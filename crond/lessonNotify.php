<?php
require('/var/www/html/1819/includes/sdk.php');

$ko = new ko();
$ses = new awsSES();
$db = new db();

$parkInfo = $ko->getParkInfo();

$template = "
Dear %s 

感謝您預訂SKIDIY滑雪課程，有沒有期待%s將要去%s滑雪了呢？

教練將會在上課前一週主動跟你們連繫，若有問題想要提前詢問教練，可透過以下的方式連絡：
%s
%s
%s
%s
%s

連絡教練時，請主動告知你們報名的訂單編號 #%d，教練可以較快確認你們的上課資料，謝謝！。
PS: 此為教練連絡資訊通知信件，勿回此信。

此外，我們非常榮幸向您介紹全新的滑雪學生程度評量系統！
這套系統讓您能夠事先了解自己的滑雪水準，並在課前獲得清晰的自我評估，同時也協助教練更精準地規劃課程內容。
我們的教練團隊擁有二級以上證照及豐富的教學經驗，能夠提供專業且精準的回饋。
課程結束後，根據您在課堂上的表現，教練將給予評量和指導，以幫助您不斷進步。

此外，若您已經具備連續滑行能力，在課前暖身後，我們將透過錄影紀錄您的滑行過程。
接著，我們將提供具體改善建議及特定的練習項目。在一段時間的練習後，再次錄影您的滑行，課後可以在後台比對兩段影片，清晰呈現您的進步。
同時提供詳盡解說，幫助您了解改善方向及技巧進步情況，以持續提升滑雪水準。

請立即登入Skidiy平台，在課程資料中填寫評量表，體驗專業教練帶來的進步！
如果有任何疑問或需要協助，請隨時聯繫我們。期待見證您在雪道上的進步與成長！

SKIDIY 敬上
";

//兩週內的訂課
$sql = "SELECT * FROM `notify` WHERE `type`='lessonNotify' AND `sent`=0 ORDER BY `idx` ASC";//_d($sql);
$res = $db->query('SELECT', $sql);//_v($res);exit();
foreach ($res as $r) {
	$r['createDateTime'] = '2020-01-21 17:40:04'; echo "{$r['createDateTime']}\n";
	if( $ko->isOverDays(strtotime($r['createDateTime']), 2) ){//
		$o = $ko->getOneOrderInfo(['oidx'=>$r['oidx']]);//_v($o);

		if(!empty($o['schedule'][0]['instructor'])){
			if(in_array($o['schedule'][0]['instructor'], ['virtual','skidiy'])) continue;
			
			$inst = getInst($o['schedule'][0]['instructor']);//_v($inst);
			$stud = getStud($r['oidx']);//_v($stud);

			echo "Send {$stud['email']}\n\n";
			$content = sprintf($template,
				empty($stud['name']) ? '' : $stud['name'],
				$o['schedule'][0]['date'],
				$parkInfo[$o['schedule'][0]['park']]['cname'],
				"教練名：{$inst['name']}",
				"教練Email：{$inst['email']}",
				empty($inst['line']) ? '' : "Line ID：{$inst['line']}",
				empty($inst['wechat']) ? '' : "WeChat ID：{$inst['wechat']}",
				empty($inst['fbid']) ? '' : "FB ID： https://www.facebook.com/{$inst['fbid']}",
				$r['oidx']
			);_v($content);
			//$ses->send('eric@inncom.cloud', '🏂SKIDIY 課前教練聯繫資訊', $content);
			$ses->send($stud['email'], '🏂SKIDIY 課前教練聯繫資訊!', $content);

			//2020.01.31: bug fix for sending notify mail everyday 
			//更新已寄送
			$sql_notify_update = "UPDATE `notify` SET `sent`=1 WHERE `idx`={$r['idx']}";
			$db->query('UPDATE', $sql_notify_update);			
		}
	}
}

	

//滿兩週的訂課
$lessons = getLessonList();
foreach ($lessons as $r) {//_v($r);
	if(in_array($r['instructor'], ['virtual','skidiy'])) continue;
	
	$inst = getInst($r['instructor']);
	$stud = getStud($r['oidx']);

	echo "Send {$stud['email']}\n\n";
	$content = sprintf($template,
		empty($stud['name']) ? '' : $stud['name'],
		$r['date'],
		$parkInfo[$r['park']]['cname'],
		"教練名：{$inst['name']}",
		"教練Email：{$inst['email']}",
		empty($inst['line']) ? '' : "Line ID：{$inst['line']}",
		empty($inst['wechat']) ? '' : "WeChat ID：{$inst['wechat']}",
		empty($inst['fbid']) ? '' : "FB ID： https://www.facebook.com/{$inst['fbid']}",
		$r['oidx']
	);_v($content);

	//$ses->send('eric@inncom.cloud', '🏂SKIDIY 課前教練聯繫資訊', $content);
	$ses->send($stud['email'], '🏂SKIDIY 課前教練聯繫資訊', $content);
}


function getInst($name){
	$db = new DB();
	return $db->select('members_v2', [
		'type'=>'instructor',
		'name'=>$name
	])[0];
}

function getStud($oidx){
	$db = new DB();
	$sql = "SELECT m.`email`, m.`name` FROM `members_v2` AS m LEFT JOIN `orders` AS o ON o.`student`=m.`idx` WHERE o.`oidx`={$oidx}";
	$data = $db->query('SELECT', $sql);
	return $data[0];
}


function getLessonList(){
	$db = new DB();
	$d1 = date('Y-m-d', strtotime('+ 14 days'));
	$d2 = date('Y-m-d', strtotime('+ 28 days'));

	$sql = "SELECT `oidx`, `date`, `instructor`, `park`, COUNT(DISTINCT(`date`)) AS `days` FROM (
				SELECT * FROM `schedules` /*先依日期排序*/
				WHERE `oidx`!=0 AND `date` BETWEEN '{$d1}' AND '{$d2}' ORDER BY `date` ASC
			) AS `s` 
			WHERE s.`date`='{$d1}' 
			GROUP BY `oidx`
	";//_d($sql);

	$data = $db->query('SELECT', $sql);//_v($res);
	return $data;
}