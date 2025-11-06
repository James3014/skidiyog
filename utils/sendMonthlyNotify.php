<?php
require('../includes/sdk.php');

$begin = '2019-03-01';
$end = '2019-03-31';

$sql = "select o.`oidx`, m.`idx`, m.`email` from `orders` as o left join `schedules` as s on o.oidx=s.oidx left join `members_v2` as m on o.`student`=m.`idx`
		where o.`status`='success'
		and s.`date`!=''
		and s.`park`!='doraemon'
		and DATE(s.`date`) BETWEEN '{$begin}' and '{$end}'
		group by o.`oidx`
		order by s.`date`";//_d($sql);

$db = new DB();
$res = $db->query('SELECT', $sql);

$emails = [];
foreach ($res as $n => $r) {
	if(in_array($r['email'], $emails)){
		echo "{$r['idx']}:{$r['email']}\n";
	}else{
		$emails[] = $r['email'];
	}
}

echo 'Total: '.sizeof($emails)."\n\n";

//發信通知
$ACCOUNT = new MEMBER();
$content = "
Dear

滑雪課程時間快到了，準備好要去滑雪了嗎？

在出發前提醒你要注意以下這些事項喔！

護照：到期了沒有？ 有六個月有效期嗎？

自備裝備：1.雪衣 2.雪褲 3.毛帽 4.雪鏡 5.雪襪 6.手套 7.脖圍 8.排汗衫。
Ps.請記得準備指甲剪，腳指甲在滑雪前需修整。

首先確認教練：
請在訂單中觀看上課教練姓名，在教練的個人頁面都會擺放這個雪季的穿著
訂單連結：https://diy.ski/my_order_list.php

教練會在上課前一週主動跟你連絡，請在個人資料頁面填寫連絡資料，教練才有辦法找到你
個人資料：https://diy.ski/account_info.php

接著確認保險：
由於skidiy都會幫學生保險，請記得填寫保險資料我們才能幫大家投保喔(訂單中有連結)，若太臨時填寫資料將無法投保。
訂單連結：https://diy.ski/my_order_list.php

若上課人數跟當初訂課時不一樣，不論增加或減少請事先聯繫保險人員更改保險資料，也在後台更新上課人數，系統會自動計算新的尾款。

修改人數後台（點選訂單後即可進入修改頁面）：https://diy.ski/my_order_list.php

保險連絡窗口 : 
富邦人壽 - 張元柏(Jake)
Cell : 0930-039-227
E-mail : jakechang106@gmail.com
Fax : (02)6608-7188

上課前請確認以下事項：

1.確認住宿位置是否離雪場很遠，注意接駁時間。

日本交通：請確認所有交通，建議帶張紙筆(必要時寫漢字，部份日本人可以看得懂)。

2.是否知道雪具店位置，若人數較多需提早租借，第一次租裝備至著裝完成都至少要1小時。


租用裝備：雪板+雪鞋+Bindings 
(建議租用新品較佳， 舊品常會因為裝備問題需要處理而浪費滑雪跟上課時間。)

租借裝備教學影片：
ski教學
https://www.youtube.com/watch?v=i1XSCxWcJgg
sb完整版教學
https://www.youtube.com/watch?v=NgTMyVgZizE
sb現場對照版(3min)
https://www.youtube.com/watch?v=xqRYih-YxrA

護具除安全帽外，其他租不到，請自行準備，可在雪具店或淘寶購買
SB護具裝備：1.安全帽 2.護手腕 3.護膝 4.防摔褲 。
Ski護具裝備：1.安全帽

上述裝備：如租用者請自行與飯店、雪具店等確認清楚


3.是否記得上課時間和集合地點，需在上課前著裝完畢在集合地點等待。

上課時間請準時
08:00上課表示8點前所有裝備穿好拿好，開始上課喔。

上課集合地點：
請在網站中找詢雪場資訊，在下方「上課地點及事項」都會公佈，部份教練上課集合地點不同，請至教練個人頁面確認。部份雪場集合地點教練連繫時會告知。

4.是否跟教練確認需要先購買雪票。

一般第一堂課程就有機會坐纜車練習，但若人數較多，可以先跟教練討論確認是否需要雪票，需事先購買。

尾款統一收當地貨幣現金(後台可查詢)(日幣或加幣)，請於第一堂下課時交給教練即可。

有任何問題，請盡早跟我們反應，謝謝。

祝您有個愉快的滑雪假期。
SKIDIY 敬上
";

//$emails = ['amber.wu3014@gmail.com','ericko@inncom.cloud'];
$cnt=0;
foreach ($emails as $to) {
	$cnt++;
	$ok = $ACCOUNT->send_mail([
		'email' 	=> $to,
		'subject'	=> '❄ SKIDIY 行前通知信～',
		'content'	=> $content,
	]);
	echo "{$cnt}. Send {$to} - {$ok}\n";
}
