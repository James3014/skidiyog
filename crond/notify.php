<?php
require('/var/www/html/1819/includes/sdk.php');

//發信通知
$ACCOUNT = new MEMBER();
$db = new DB();
$ko = new ko();

$parkInfo = $ko->getParkInfo();
$instructorInfo = $ko->getInstructorInfo();

$sql = "SELECT * FROM `notify` WHERE `sent`=0 ORDER BY `idx` ASC";//_d($sql);
$res = $db->query('SELECT', $sql);//_v($res);exit();

$adminEmail = ['admin@diy.ski'];
//$adminEmail = ['eric@inncom.cloud'];
$insuranceEmail = ['eric@inncom.cloud','jakechang106@gmail.com','jasmine082077@gmail.com','admin@diy.ski'];
//$insuranceEmail = ['eric@inncom.cloud'];

$bookingNote = "📕訂課注意事項:\r\n" . 
				"・尾款請準備日幣現金在上課時交給教練。\r\n" . 
				"・若於上課期間無故曠課，將沒收訂金賠償教練損失，除非提供相關證明，因天災、意外原因，非故意曠課，才會退還訂金。\r\n" . 
				"・此為自助行程，請提早在上課時間前抵達，以免影響上課時間，教練會按照時間準時上下課。\r\n" . 
				"・預定課程完成後若預取消，需遵守以下列條款\r\n" . 
				"    2個月前取消，訂金全額退費；\r\n" . 
				"    1個月前取消，退還50%訂金；\r\n" . 
				"    1個月內取消，訂金不退還。\r\n" . 
				" (以上退還金額需扣除刷卡金額3%手續費後轉帳退回)。\r\n";

foreach ($res as $n => $r) {//_v($r);
	$notifyUpdate = false;//預設不更新
	$lessonFirstDate = '';//訂單的第一天上課日期(保險用)
	$lessonParks = [];
	echo "Send {$r['oidx']}, type={$r['type']}\n";

	$order = $ko->getOneOrderInfo(['oidx'=>$r['oidx']]);//_v($order);
	if(@$order['oidx']!=$r['oidx']){//訂單已消失,異常.
		echo "#{$r['oidx']} 訂單異常。\n";
		continue;
	}
	// if($r['type']=='orderTimeout' && (count($order['schedule'])==0)){//訂單逾時,且訂課已被移除,不再發通知.
	// 	echo "#{$r['oidx']} 重複逾時的訂單,忽略!!\n";
	// 	continue;
	// }

	$oidxEnc = crypto::ev($r['oidx']);//_d($oidx);
	$acceptEnc = crypto::ev('true');
	$rejectEnc = crypto::ev('false');

	$student = $ko->getMembers(['idx'=>$order['student']]);//_v($student);
	$instructors = [];

	if(empty($order['gidx'])){//指定or預約
		//開課種類
		$orderType = 'fix';//預設
		//
		$lessonContent = "📝課程資訊:\r\n";
		foreach ($order['schedule'] as $n => $s) {			
			if($n===0) $lessonFirstDate = $s['date'];//上課第一天			
			if($s['noshow']==1 ) $noshow_str='(課程已取消)'; else $noshow_str='';
			if($order['lock']=='sars' ) $noshow_str.='(課程已延期)'; else $noshow_str.='';
			$lessonParks[$s['park']] = $s['park'];

			$instructors[$s['instructor']] = $s['instructor'];
			$lessonContent .= "上課日期: {$s['date']} {$parkInfo[$s['park']]['timeslot'][$s['slot']]}, \t" . 
							  "雪場: {$parkInfo[$s['park']]['cname']}, \t" . 
							  "教練: {$instructorInfo[$s['instructor']]['cname']}/". strtoupper($s['expertise']) . ",\t" .
							  "學生: {$s['studentNum']}位 {$noshow_str}\r\n";
			if($s['reservation']!=0){
	        	$orderType = 'reservation';
	        }else if($s['rule']!=0){
	            $orderType = 'rule';
	        }
		}//_d($student);_v($instructors);
		if(!empty($order['requirement'])){
			$lessonContent .= "備註: {$order['requirement']}\r\n";
		}
	}else{//團體課
		$groupLesson = $ko->getGroupLessons($order['gidx']);
		$lessonFirstDate = $groupLesson['start'];//上課第一天
		$lessonContent = "📝課程資訊:\r\n";
		$lessonContent.= "課程名稱: {$groupLesson['title']}.\r\n" . 
						 "課程日期: {$groupLesson['start']} ~ {$groupLesson['end']}.\r\n" . 
						 "上課雪場: {$parkInfo[$groupLesson['park']]['cname']}.\r\n" . 
						 "上課教練: {$instructorInfo[$groupLesson['instructor']]['cname']}, {$groupLesson['expertise']}課程.\r\n\r\n\r\n" . 
						 "課程說明:\r\n{$groupLesson['content']}\r\n\r\n";
		$orderType = 'group';
	}

	//雪場特別提醒.
	// if(in_array('karuizawa', $lessonParks)){
	// 	$lessonContent .= "🚩特別提醒: 目前輕井澤的課程經雪場要求需住宿王子飯店才能上課，也提供給我們學生優惠價，細節請參考連結 https://diy.ski/article.php?idx=27 \r\n";
	// }else if(in_array('appi', $lessonParks)){
	// 	$lessonContent .= "🚩特別提醒: SKIDIY和安比合作，飯店提供給學生優惠住宿和雪票價格，訂課完成需來信索取優惠訂房連結，透過連結訂房才可享有優惠喔，謝謝。 \r\n";
	// }else if(in_array('naeba', $lessonParks)){
	// 	$lessonContent .= "🚩特別提醒: 目前苗場的課程經雪場要求需住宿王子飯店才能上課，也提供給我們學生優惠價，細節請參考連結 https://diy.ski/article.php?idx=27 \r\n";
	// }
	
	//加入金額符號

	$order['payment'] = $order['payment']+$order['specialDiscount'];
	$order['discount'] = $order['discount']-$order['specialDiscount'];
	foreach ($order as $k => $v) {
		if(in_array($k, ['price','discount','prepaid','paid','payment'])){
			$order[$k] = number_format($v);
		}
	}
	//
	$costContent = "💴費用資訊:\r\n";
	$costContent.= "學費總計: {$order['price']} {$order['currency']}\r\n" . 
				   "優惠折扣: {$order['discount']} {$order['currency']}\r\n" . 
				   "預付訂金: {$order['prepaid']} {$order['currency']}\r\n" . 
				   "當日匯率: {$order['exchangeRate']} {$order['currency']}\r\n" . 
				   "刷卡金額: {$order['paid']} NTD\r\n" . 
				   "剩餘尾款: {$order['payment']} {$order['currency']}\r\n";

	//_v($lessonContent.$costContent);
	switch($r['type']){
		case 'paying':
			//學生
			$studentName = strtoupper($student[0]['name']);
			$subject = "🏂 {$studentName} 訂課交易處理中...";
			$content = "{$studentName} 您好!\r\n我們已收到您的訂課交易，目前正在處理中，以下是您的訂課內容:\r\n\r\n";
			$content.= $lessonContent . "\r\n\r\n" . $costContent . "\r\n\r\n";
			$content.= "訂單保險編號 #{$r['oidx']}\r\n";
			$content.= "若您已完成交易，請確認收到訂課成功通知才算完成訂課。\r\n";
			$content.= "若您未完成交易，約20分鐘會收到交易逾時通知，收到通知後才可依原時段重新訂課。\r\n";
			$content.= "SKIDIY 自助滑雪\r\nadmin@diy.ski";
			//_v($content);
			$ok = $ACCOUNT->send_mail([
				'email' 	=> $student[0]['email'],
				'subject'	=> $subject,
				'content'	=> $content,
			]);//_d($student[0]['email'].'='.$ok);

			$notifyUpdate = true;
			break;

		case 'booking_extend':
			$studentName = strtoupper($student[0]['name']);
			$extend_subject_s	= "🏂 #訂單編號{$r['oidx']}: {$studentName} 課程延期通知～";	
			$extend_subject_i	= "🏂 #訂單編號{$r['oidx']}: 學生 {$studentName} 課程延期通知～";
			$extend_subject_a	= "🏂 #訂單編號{$r['oidx']}: {$studentName} 課程延期通知～";
			
			$extend_content  = "針對本次疫情延期，請先留意以下相關注意事項:\r\n\r\n";
			$extend_content .="1.延期訂單會優先延後至 上課日期+5個月 比如原本是 2月1日就會改制 7月1日\r\n"; 
			$extend_content .="2.訂單可以在2021雪季結束前都可以預約\r\n";
			$extend_content .="3.需要優先以原本的教練為主，如果滑雪場改變 會以新雪場費用為主\r\n";
			$extend_content .="4.預約時數 無法減少 假設原本預約8小時課程，下季也是八小時\r\n";
			$extend_content .="5.下一季保險需要 自行負擔費用\r\n\r\n";
			$booking_extend='Y';
		case 'booking'://一般訂課, ecPay通知訂課成功
			//學生
			$studentName = strtoupper($student[0]['name']);
			$subject = "🏂 {$studentName} 訂課成功通知～";			
			$content = "{$studentName} 您好!\r\n以下是您的訂課內容:\r\n\r\n";
			if($r['oidx']==9157){ // exception case
				//$student[0]['email']='mjskidiy@gmail.com';
				$content = "{$studentName} 您好! 很抱歉造成您的困擾～\r\n以下是您本次的訂單內容:\r\n\r\n";
			} 
			if($booking_extend=='Y'){
				$subject = $extend_subject_s;
				$content  = "{$studentName} 您好!\r\n我們已收到您的課程延期請求\r\n";
				$content .= $extend_content;
				$content .= "以下為您本次延期調整後的的訂課內容:\r\n\r\n";
			}
			$content.= $lessonContent . "\r\n\r\n" . $costContent . "\r\n\r\n";
			$content.= $bookingNote . "\r\n";
			$content.= "＊溫馨提醒\r\n";
			$content.= "➡系統將會在上課前兩週提供教練的聯絡方式。\r\n";
			$content.= "➡教練會在上課前一週主動跟學生連繫(請確認『帳號』是否填寫正確的手機號碼、LINE ID、FB ID)。\r\n";
			$content.= "➡帳號設定頁面 https://diy.ski/account_info.php\r\n";
			$content.= "訂單保險編號 #{$r['oidx']}\r\n";
			$content.= "訂課方式：{$CLASSTYPE[$orderType]}\r\n\r\n";
			$content.= "謝謝您的訂課，如有疑問歡迎與我們聯絡。\r\n並預祝您有個愉快的滑雪假期！\r\n\r\n";
			$content.= "此外，我們非常榮幸向您介紹全新的滑雪學生程度評量系統！這套系統讓您能夠事先了解自己的滑雪水準，並在課前獲得清晰的自我評估，同時也協助教練更精準地規劃課程內容。\r\n";
			$content.= "我們的教練團隊擁有二級以上證照及豐富的教學經驗，能夠提供專業且精準的回饋。課程結束後，根據您在課堂上的表現，教練將給予評量和指導，以幫助您不斷進步。\r\n";
			$content.= "若您已經具備連續滑行能力，在課前暖身後，我們將透過錄影紀錄您的滑行過程。接著，我們將提供具體改善建議及特定的練習項目。在一段時間的練習後，再次錄影您的滑行，課後可以在後台比對兩段影片，清晰呈現您的進步。同時提供詳盡解說，幫助您了解改善方向及技巧進步情況，以持續提升滑雪水準。\r\n";
			$content.= "請立即登入Skidiy平台，在課程資料中填寫評量表，體驗專業教練帶來的進步！\r\n";
			$content.= "如果有任何疑問或需要協助，請隨時聯繫我們。期待見證您在雪道上的進步與成長！\r\n";
			$content.= "SKIDIY 自助滑雪 敬上\r\nadmin@diy.ski";

			if(empty($r['resent'])||(stripos($r['resent'],'student')!==false)){
				_v($content);
				$ok = $ACCOUNT->send_mail([
					'email' 	=> [$student[0]['email']],
					'subject'	=> empty($r['resent']) ? $subject : $r['subject'],
					'content'	=> $content,
				]);//_d($student[0]['email'].'='.$ok);
			}

			//教練
			foreach ($instructors as $name) {
				$name = strtoupper($name);
				$subject = "🏂 #{$r['oidx']}: 學生 {$studentName} 訂課成功通知～";
				$content = "{$name} 教練您好!\r\n以下是學生的訂課內容:\r\n\r\n";

				if($booking_extend=='Y'){
					$subject = $extend_subject_i;
					$content  = "{$name} 教練您好!!\r\n我們已收到學生 {$studentName} 的課程延期請求\r\n";
					$content .= $extend_content;
					$content .= "以下為學生本次延期調整後的的訂課內容:\r\n\r\n";
				}

				$content.= $lessonContent . "\r\n\r\n" . $costContent . "\r\n\r\n";
				//$content.= "學生聯絡資訊:\r\n暱稱: {$studentName}\r\nEmail: {$student[0]['email']}\r\nLINE: {$student[0]['line']}\r\nWeChat: {$student[0]['wechat']}\r\n";
				//$content.= "手機:{$student[0]['country']}, {$student[0]['phone']}\r\n";
				$content.= "訂單保險編號 #{$r['oidx']}\r\n\r\n";
				if($orderType=='rule'){
					$content.= "PS: 此訂課為條件式開課，由於學生訂課付款成功，目前相同條件的開課已自動取消，請儘速回教練後台確認後，新增指定開課或條件開課以利學生再次訂課。\r\n";
				}
				$content.= "訂課方式：{$CLASSTYPE[$orderType]}\r\n";
				$content.= "如有任何疑問請隨時與管理者聯絡。\r\n";
				$content.= "SKIDIY 自助滑雪\r\nadmin@diy.ski";
				$instructor = $ko->getMembers(['type'=>'instructor','name'=>$name]);//_v($instructor);

				if(empty($r['resent'])||(stripos($r['resent'],'instructor')!==false)){
					_v($content);
					$ok = $ACCOUNT->send_mail([
						'email' 	=> $instructor[0]['email'],
						'subject'	=> empty($r['resent']) ? $subject : $r['subject'],
						'content'	=> $content,
					]);//_d($instructor[0]['email'].'='.$ok);
				}
			}

			//管理者
			$studentName = strtoupper($student[0]['name']);
			$subject = "🏂 #{$r['oidx']}: {$studentName} 訂課成功通知～";			
			$content = "以下是學生的訂課內容:\r\n\r\n";

			if($booking_extend=='Y'){
				$subject = $extend_subject_a;
				$content  = "學生 {$studentName} 的課程延期通知\r\n";
				$content .= $extend_content;
				$content .= "以下為學生本次延期調整後的的訂課內容:\r\n\r\n";
			}

			$content.= $lessonContent . "\r\n\r\n" . $costContent . "\r\n\r\n";
			$content.= "學生聯絡資訊:\r\n暱稱:{$studentName}\r\nEmail: {$student[0]['email']}\r\nLINE: {$student[0]['line']}\r\nWeChat: {$student[0]['wechat']}\r\n";
			$content.= "手機:{$student[0]['country']}, {$student[0]['phone']}\r\n";
			$content.= "訂單保險編號 #{$r['oidx']}\r\n";
			$content.= "訂課方式：{$CLASSTYPE[$orderType]}\r\n";

			if(empty($r['resent'])||(stripos($r['resent'],'admin')!==false)){
				_v($content);
				$ok = $ACCOUNT->send_mail([
					'email' 	=> $adminEmail,
					'subject'	=> empty($r['resent']) ? $subject : $r['subject'],
					'content'	=> $content,
				]);//_d($student[0]['email'].'='.$ok);
			}

			//保險員
			if($ko->isInWeekOrder($lessonFirstDate)){//一週內的訂單要趕快通知保險
				$subject = "🏂 #{$r['oidx']}: {$studentName} 一週內訂課通知，請儘速處理保險事宜，謝謝。";
				$content = "以下是學生一週內的訂課內容:\r\n\r\n";
				$content.= $lessonContent . "\r\n\r\n" . $costContent . "\r\n\r\n";
				$content.= "學生聯絡資訊:\r\n暱稱:{$studentName}\r\nEmail: {$student[0]['email']}\r\nLINE: {$student[0]['line']}\r\nWeChat: {$student[0]['wechat']}\r\n";
				$content.= "手機:{$student[0]['country']}, {$student[0]['phone']}\r\n";
				$content.= "訂單保險編號 #{$r['oidx']}\r\n";

				if(empty($r['resent'])||(stripos($r['resent'],'insurance')!==false)){
					_v($content);
					$ok = $ACCOUNT->send_mail([
						'email' 	=> $insuranceEmail,
						'subject'	=> empty($r['resent']) ? $subject : $r['subject'],
						'content'	=> $content,
					]);//_d($student[0]['email'].'='.$ok);
				}
			}

			//教練聯繫通知信
			// 2020.01.27 mj.應該改成只發送未發生的課程定單
			if($ko->isInDays(strtotime($lessonFirstDate), 14)){//兩週內的訂課另外排程通知 crond/lessonNotify.php
				$ko->notify([
			        'oidx'              => $r['oidx'],
			        'type'              => 'lessonNotify',
			        'createDateTime'    => date('Y-m-d H:i:s'),
			    ]);
			}

			$notifyUpdate = true;
			break;

		case 'reservation'://申請開課, ecPay通知訂課成功
			//學生
			$studentName = strtoupper($student[0]['name']);
			$subject = "🏂 {$studentName} 您好我們已收到您的開課申請(#{$r['oidx']})";
			$content = "{$studentName} 您好!\r\n以下是您申請的內容:\r\n\r\n";
			$content.= $lessonContent . "\r\n\r\n" . $costContent . "\r\n\r\n" ;
			$content.= $bookingNote . "\r\n";
			$content.= "＊溫馨提醒\r\n";
			$content.= "➡申請開課需等候教練同意上課，約3天的時間教練才能確認行程。\r\n";
			$content.= "➡若申請的教練無法上課，系統會再安排網站上的其他教練，約2週左右會確認並安排，若確定沒有教練上課將全額退費，SKIDIY保留更換教練的權利。\r\n";
			$content.= "訂單保險編號 #{$r['oidx']}\r\n";
			$content.= "SKIDIY 自助滑雪\r\nadmin@diy.ski";

			if(empty($r['resent'])||(stripos($r['resent'],'student')!==false)){
				_v($content);
				$ok = $ACCOUNT->send_mail([
					'email' 	=> $student[0]['email'],
					'subject'	=> empty($r['resent']) ? $subject : $r['subject'],
					'content'	=> $content,
				]);//_d($student[0]['email'].'='.$ok);
			}

			//教練
			$_instructors = '';
			foreach ($instructors as $name) {
				$_instructors .= "{$name},";
				$instructorEnc = crypto::ev($name);
				$name = strtoupper($name);
				$subject = "🏂 #{$r['oidx']}: 學生{$studentName}申請開課通知～";
				$content = "{$name} 教練您好!\r\n以下是學生申請的開課內容:\r\n\r\n";
				$content.= $lessonContent . "\r\n\r\n" . $costContent . "\r\n\r\n" ;
				//$content.= "學生聯絡資訊:\r\n暱稱:{$studentName}\r\nEmail: {$student[0]['email']}\r\nLINE: {$student[0]['line']}\r\nWeChat: {$student[0]['wechat']}\r\n";
				//$content.= "手機:{$student[0]['country']}, {$student[0]['phone']}\r\n";
				$content.= "訂單保險編號 #{$r['oidx']}\r\n";
				$content.= "若超過36小時未回覆，系統將自動視同放棄。\r\n\r\n";
				$content.= "點擊前請確認已登入教練後台喔～\r\n\r\n";
				$content.= "⭕同意 => https://instructor.diy.ski/acceptLessons.php?key={$oidxEnc}&action={$acceptEnc}&id={$instructorEnc}\r\n\r\n\r\n";
				$content.= "🚫拒絕 => https://instructor.diy.ski/acceptLessons.php?key={$oidxEnc}&action={$rejectEnc}&id={$instructorEnc}\r\n\r\n";

				$content.= "SKIDIY 自助滑雪\r\nadmin@diy.ski";
				$instructor = $ko->getMembers(['type'=>'instructor','name'=>$name]);//_v($instructor);

				//新增至詢問table
				$ok = $ko->addAcception([
					'oidx'				=> $r['oidx'],
					'accepted'			=> 'wait',
					'instructor'		=> strtolower($name),
					'createDateTime'	=> date('Y-m-d H:i:s'),
				]);

				if(empty($r['resent'])||(stripos($r['resent'],'instructor')!==false)){
					_v($content);
					$ok = $ACCOUNT->send_mail([
						'email' 	=> $instructor[0]['email'],
						'subject'	=> empty($r['resent']) ? $subject : $r['subject'],
						'content'	=> $content,
					]);//_d($instructor[0]['email'].'='.$ok);
				}
			}
			$_instructors = substr($_instructors, 0, -1);

			//管理者
			$studentName = strtoupper($student[0]['name']);
			$subject = "🏂 #{$r['oidx']}: {$studentName} 提出教練{$_instructors}的開課申請。";
			$content = "以下是學生申請的內容:\r\n\r\n";
			$content.= $lessonContent . "\r\n\r\n" . $costContent . "\r\n\r\n" ;
			$content.= "學生聯絡資訊:\r\n暱稱:{$studentName}\r\nEmail: {$student[0]['email']}\r\nLINE: {$student[0]['line']}\r\nWeChat: {$student[0]['wechat']}\r\n";
			$content.= "手機:{$student[0]['country']}, {$student[0]['phone']}\r\n";
			$content.= "訂單保險編號 #{$r['oidx']}\r\n";
			$content.= "訂課方式：{$CLASSTYPE[$orderType]}\r\n";
			$content.= "等候教練回覆中.";

			if(empty($r['resent'])||(stripos($r['resent'],'admin')!==false)){
				_v($content);
				$ok = $ACCOUNT->send_mail([
					'email' 	=> $adminEmail,
					'subject'	=> empty($r['resent']) ? $subject : $r['subject'],
					'content'	=> $content,
				]);//_d($student[0]['email'].'='.$ok);
			}

			$notifyUpdate = true;
			break;

		case 'reservationNext'://管理者排課通知
			$studentName = strtoupper($student[0]['name']);
			//教練
			$_instructors = '';
			foreach ($instructors as $name) {
				$_instructors .= "{$name},";
				$instructorEnc = crypto::ev($name);
				$name = strtoupper($name);
				$subject = "🏂 #{$r['oidx']}: 管理者排課通知！另轉學生{$studentName}申請開課通知";
				$content = "{$name} 教練您好!\r\n以下是學生申請的開課內容:\r\n";
				$content.= "＊提醒：由管理者排課不會有加級費用，學費將以各雪場之標準費用計算。\r\n\r\n";
				$content.= $lessonContent . "\r\n\r\n" . $costContent . "\r\n\r\n" ;
				//$content.= "學生聯絡資訊:\r\n暱稱:{$studentName}\r\nEmail: {$student[0]['email']}\r\nLINE: {$student[0]['line']}\r\nWeChat: {$student[0]['wechat']}\r\n";
				//$content.= "手機:{$student[0]['country']}, {$student[0]['phone']}\r\n";
				$content.= "訂單保險編號 #{$r['oidx']}\r\n";
				$content.= "若超過36小時未回覆，系統將自動視同放棄。\r\n\r\n";
				$content.= "點擊前請確認已登入教練後台喔～\r\n\r\n";
				$content.= "⭕同意 => https://instructor.diy.ski/acceptLessons.php?key={$oidxEnc}&action={$acceptEnc}&id={$instructorEnc}\r\n\r\n\r\n";
				$content.= "🚫拒絕 => https://instructor.diy.ski/acceptLessons.php?key={$oidxEnc}&action={$rejectEnc}&id={$instructorEnc}\r\n\r\n";

				$content.= "SKIDIY 自助滑雪\r\nadmin@diy.ski";
				$instructor = $ko->getMembers(['type'=>'instructor','name'=>$name]);//_v($instructor);

				//新增至詢問table
				$ok = $ko->addAcception([
					'oidx'				=> $r['oidx'],
					'accepted'			=> 'wait',
					'instructor'		=> strtolower($name),
					'createDateTime'	=> date('Y-m-d H:i:s'),
				]);

				if(empty($r['resent'])||(stripos($r['resent'],'instructor')!==false)){
					_v($content);
					$ok = $ACCOUNT->send_mail([
						'email' 	=> $instructor[0]['email'],
						'subject'	=> empty($r['resent']) ? $subject : $r['subject'],
						'content'	=> $content,
					]);//_d($instructor[0]['email'].'='.$ok);
				}
				_v($content);
			}
			$_instructors = substr($_instructors, 0, -1);

			//管理者
			$studentName = strtoupper($student[0]['name']);
			$subject = "🏂 #{$r['oidx']}: 管理者已轉達學生{$studentName}的開課申請至新教練{$_instructors}。";
			$content = "以下是學生申請的內容:\r\n\r\n";
			$content.= $lessonContent . "\r\n\r\n" . $costContent . "\r\n\r\n" ;
			$content.= "學生聯絡資訊:\r\n暱稱:{$studentName}\r\nEmail: {$student[0]['email']}\r\nLINE: {$student[0]['line']}\r\nWeChat: {$student[0]['wechat']}\r\n";
			$content.= "手機:{$student[0]['country']}, {$student[0]['phone']}\r\n";
			$content.= "訂單保險編號 #{$r['oidx']}\r\n";
			$content.= "訂課方式：{$CLASSTYPE[$orderType]}\r\n";
			$content.= "等候教練回覆中.";

			if(empty($r['resent'])||(stripos($r['resent'],'admin')!==false)){
				_v($content);
				$ok = $ACCOUNT->send_mail([
					'email' 	=> $adminEmail,
					'subject'	=> empty($r['resent']) ? $subject : $r['subject'],
					'content'	=> $content,
				]);//_d($student[0]['email'].'='.$ok);
			}
			_v($content);

			$notifyUpdate = true;
			break;

		case 'group'://團體報名, ecPay通知訂課成功
			//學生
			$studentName = strtoupper($student[0]['name']);
			$subject = "🏂 {$studentName} 團體報名成功通知 (#{$r['oidx']})";
			$content = "{$studentName} 您好!\r\n以下是您團體課程的內容:\r\n\r\n";
			$content.= $lessonContent . "\r\n\r\n" . $costContent . "\r\n\r\n" ;
			$content.= $bookingNote . "\r\n";
			$content.= "＊溫馨提醒\r\n";
			$content.= "➡若開課人數不足，團體課程將取消並全額退費。\r\n";
			$content.= "➡教練會在上課前一週主動跟學生連繫(請確認『帳號』是否填寫正確的手機號碼、LINE ID、FB ID)。\r\n";
			$content.= "➡帳號設定頁面 https://diy.ski/account_info.php\r\n";
			$content.= "訂單保險編號 #{$r['oidx']}\r\n";
			$content.= "訂課方式：{$CLASSTYPE[$orderType]}\r\n\r\n";
			$content.= "謝謝您的訂課，如有疑問歡迎與我們聯絡。\r\n並預祝您有個愉快的滑雪假期！\r\n\r\n";
			$content.= "SKIDIY 自助滑雪\r\nadmin@diy.ski";
			if(empty($r['resent'])||(stripos($r['resent'],'student')!==false)){
				_v($content);
				$ok = $ACCOUNT->send_mail([
					'email' 	=> [$student[0]['email']],
					'subject'	=> empty($r['resent']) ? $subject : $r['subject'],
					'content'	=> $content,
				]);//_d($student[0]['email'].'='.$ok);
			}

			//教練
			$name = ucfirst($groupLesson['instructor']);
			$subject = "🏂 #{$r['oidx']}: 學生{$studentName} 報名 {$groupLesson['title']} 通知～";
			$content = "{$name} 教練您好!\r\n以下是學生報名資訊:\r\n\r\n";
			$content.= $lessonContent . "\r\n\r\n" . $costContent . "\r\n\r\n" ;
			//$content.= "學生聯絡資訊:\r\n暱稱:{$studentName}\r\nEmail: {$student[0]['email']}\r\nLINE: {$student[0]['line']}\r\nWeChat: {$student[0]['wechat']}\r\n";
			//$content.= "手機:{$student[0]['country']}, {$student[0]['phone']}\r\n";
			$content.= "訂單保險編號 #{$r['oidx']}\r\n";
			$content.= "SKIDIY 自助滑雪\r\nadmin@diy.ski";
			$instructor = $ko->getMembers(['type'=>'instructor','name'=>$name]);//_v($instructor);

			if(empty($r['resent'])||(stripos($r['resent'],'instructor')!==false)){
				_v($content);
				$ok = $ACCOUNT->send_mail([
					'email' 	=> $instructor[0]['email'],
					'subject'	=> empty($r['resent']) ? $subject : $r['subject'],
					'content'	=> $content,
				]);//_d($instructor[0]['email'].'='.$ok);
			}

			//管理者
			$studentName = strtoupper($student[0]['name']);
			$subject = "🏂 #{$r['oidx']}: {$studentName} 已報名 {$groupLesson['title']}。";
			$content = "以下是學生報名的上課內容:\r\n\r\n";
			$content.= $lessonContent . "\r\n\r\n" . $costContent . "\r\n\r\n" ;
			$content.= "學生聯絡資訊:\r\n暱稱:{$studentName}\r\nEmail: {$student[0]['email']}\r\nLINE: {$student[0]['line']}\r\nWeChat: {$student[0]['wechat']}\r\n";
			$content.= "手機:{$student[0]['country']}, {$student[0]['phone']}\r\n";
			$content.= "訂單保險編號 #{$r['oidx']}\r\n";
			$content.= "訂課方式：{$CLASSTYPE[$orderType]}\r\n";

			if(empty($r['resent'])||(stripos($r['resent'],'admin')!==false)){
				_v($content);
				$ok = $ACCOUNT->send_mail([
					'email' 	=> $adminEmail,
					'subject'	=> empty($r['resent']) ? $subject : $r['subject'],
					'content'	=> $content,
				]);//_d($student[0]['email'].'='.$ok);
			}

			//保險員
			if($ko->isInWeekOrder($lessonFirstDate)){//一週內的訂單要趕快通知保險
				$subject = "🏂 #{$r['oidx']}: {$studentName} 一週內訂課通知，請儘速處理保險事宜，謝謝。";
				$content = "以下是學生一週內的訂課內容:\r\n\r\n";
				$content.= $lessonContent . "\r\n\r\n" . $costContent . "\r\n\r\n";
				$content.= "學生聯絡資訊:\r\n暱稱:{$studentName}\r\nEmail: {$student[0]['email']}\r\nLINE: {$student[0]['line']}\r\nWeChat: {$student[0]['wechat']}\r\n";
				$content.= "手機:{$student[0]['country']}, {$student[0]['phone']}\r\n";
				$content.= "訂單保險編號 #{$r['oidx']}\r\n";

				if(empty($r['resent'])||(stripos($r['resent'],'insurance')!==false)){
					_v($content);
					$ok = $ACCOUNT->send_mail([
						'email' 	=> $insuranceEmail,
						'subject'	=> empty($r['resent']) ? $subject : $r['subject'],
						'content'	=> $content,
					]);//_d($student[0]['email'].'='.$ok);
				}
			}

			$notifyUpdate = true;
			break;

		case 'ecpayFail'://交易失敗
			//學生
			$studentName = strtoupper($student[0]['name']);
			$subject = "🏂 {$studentName} 您好，刷卡交易失敗通知(#{$r['oidx']})。";
			$content = "{$studentName} 您好!\r\n以下是您訂課的內容:\r\n\r\n";
			$content.= $lessonContent . "\r\n\r\n" . $costContent . "\r\n\r\n" ;
			$content.= "刷卡交易失敗代碼：{$r['resp']}\r\n";

			$content.= "刷卡失敗可能的原因有：\r\n";
			$content.= "・信用卡第一次使用尚未開卡。\r\n";
			$content.= "・信用卡卡號或到期日輸入錯誤。\r\n";
			$content.= "・信用卡已超過到期日使用期限。\r\n";
			$content.= "・超出信用卡使用額度或餘額不足。\r\n";
			$content.= "・信用卡發卡銀行內部系統問題…等。\r\n";
			$content.= "・此筆卡號同時有人刷卡授權中，因此視窗會跳出。\r\n";
			$content.= "・信用卡授權時，網路斷線。\r\n\r\n";
			$content.= "建議直接換張信用卡使用，若您原本的信用卡並未超出使用額度。\r\n";
			$content.= "提醒您！請留意輸入的信用卡卡號、姓名、到期日以及卡片背後末3碼是否正確。如果依然收到授權失敗的通知，可能原因為發卡銀行內部系統作業問題，請與您的信用卡發卡銀行聯絡。\r\n";

			if($orderType=='rule'){
				$content.= "註: 此訂課為教練調度課程，還需等待教練重新確認後才能再次預訂此時段，造成您的不便還請見諒。\r\n";
			}else{
				$content.= "還請您返回網站重新訂課，造成您的不便還請見諒。\r\n";	
				$content.= "官方網站: https://diy.ski。\r\n";	
			}
			$content.= "訂單保險編號 #{$r['oidx']}\r\n";
			$content.= "訂課方式：{$CLASSTYPE[$orderType]}\r\n";
			$content.= "如有任疑問請隨時與我們聯繫。\r\n";
			$content.= "SKIDIY 自助滑雪\r\nadmin@diy.ski";
			_v($content);
			$ok = $ACCOUNT->send_mail([
				'email' 	=> $student[0]['email'],
				'subject'	=> $subject,
				'content'	=> $content,
			]);_d($student[0]['email'].'='.$ok);

			//教練
			$_instructors = '';
			foreach ($instructors as $name) {
				$_instructors .= "{$name},";
				$instructorEnc = crypto::ev($name);
				$name = strtoupper($name);
				$subject = "🏂 #{$r['oidx']}: 學生{$studentName}刷卡交易失敗通知～";
				$content = "{$name} 教練您好!\r\n以下是學生訂課的內容:\r\n\r\n";
				$content.= $lessonContent . "\r\n\r\n" . $costContent . "\r\n\r\n" ;
				$content.= "刷卡交易失敗代碼：{$r['resp']}\r\n";
				//$content.= "學生聯絡資訊:\r\n暱稱:{$studentName}\r\nEmail: {$student[0]['email']}\r\nLINE: {$student[0]['line']}\r\nWeChat: {$student[0]['wechat']}\r\n";
				//$content.= "手機:{$student[0]['country']}, {$student[0]['phone']}\r\n";
				$content.= "訂單保險編號 #{$r['oidx']}\r\n";
				$content.= "訂課方式：{$CLASSTYPE[$orderType]}\r\n";
				if($orderType=='rule'){
					$content.= "PS: 原訂課為條件式開課，由於學生取消訂課，之前相同條件的開課也已自動取消，請儘速回教練後台確認後，新增指定開課或條件開課以利學生再次訂課。\r\n\r\n";
				}

				$content.= "SKIDIY 自助滑雪\r\nadmin@diy.ski";
				$instructor = $ko->getMembers(['type'=>'instructor','name'=>$name]);//_v($instructor);
				_v($content);
				if($orderType=='rule'){//改為條件開課才需告知教練
					$subject = "🏂 #{$r['oidx']}: 學生{$studentName} 訂課取消通知";
					$ok = $ACCOUNT->send_mail([
						'email' 	=> $instructor[0]['email'],
						'subject'	=> $subject,
						'content'	=> $content,
					]);_d($instructor[0]['email'].'='.$ok);
				}
			}
			$_instructors = substr($_instructors, 0, -1);

			//管理者
			$studentName = strtoupper($student[0]['name']);
			$subject = "🏂 #{$r['oidx']}: 學生 {$studentName} 信用卡交易失敗。";
			$content = "以下是學生申請的內容:\r\n\r\n";
			$content.= $lessonContent . "\r\n\r\n" . $costContent . "\r\n\r\n" ;
			$content.= "刷卡交易失敗代碼：{$r['resp']}\r\n";
			$content.= "學生聯絡資訊:\r\n暱稱:{$studentName}\r\nEmail: {$student[0]['email']}\r\nLINE: {$student[0]['line']}\r\nWeChat: {$student[0]['wechat']}\r\n";
			$content.= "手機:{$student[0]['country']}, {$student[0]['phone']}\r\n";
			$content.= "訂單保險編號 #{$r['oidx']}\r\n";
			$content.= "訂課方式：{$CLASSTYPE[$orderType]}\r\n";
			if($orderType=='rule'){
				$content.= "PS: 訂課為條件式開課，已Email通知教練後台確認後新增指定開課或條件開課以利學生再次訂課。\r\n\r\n";
			}
			_v($content);
			$ok = $ACCOUNT->send_mail([
				'email' 	=> $adminEmail,
				'subject'	=> $subject,
				'content'	=> $content,
			]);_v($adminEmail);

			//回復訂單之開課
			$ko->rollbackOrder($order['oidx'],'fail');

			$notifyUpdate = true;
			break;

		case 'orderTimeout'://訂單逾時
			//學生
			$studentName = strtoupper($student[0]['name']);
			$subject = "🏂 {$studentName} 您好，刷卡交易逾時通知 (#{$r['oidx']})。";
			$content = "{$studentName} 您好!\r\n以下是您訂課的內容:\r\n\r\n";
			$content.= $lessonContent . "\r\n\r\n" . $costContent . "\r\n\r\n" ;

			if($orderType=='rule'){
				$content.= "逾時原因可能是線上交易過程中網路、信用卡授權異常或訂單連結失效。\r\n";
				$content.= "註: 此訂課為教練調度課程，還需等待教練重新確認後才能再次預訂此時段，造成您的不便還請見諒。\r\n";
			}else{
				$content.= "逾時原因可能是線上交易過程中網路、信用卡授權異常或訂單連結失效。還煩請確認後返回網站重新訂課，造成您的不便還請見諒。\r\n";	
				$content.= "官方網站: https://diy.ski。\r\n";	
			}
			$content.= "訂單保險編號 #{$r['oidx']}\r\n";
			$content.= "訂課方式：{$CLASSTYPE[$orderType]}\r\n";
			$content.= "如有任疑問請隨時與我們聯繫。\r\n";
			$content.= "SKIDIY 自助滑雪\r\nadmin@diy.ski";
			_v($content);
			$ok = $ACCOUNT->send_mail([
				'email' 	=> $student[0]['email'],
				'subject'	=> $subject,
				'content'	=> $content,
			]);_d($student[0]['email'].'='.$ok);

			//教練
			$_instructors = '';
			foreach ($instructors as $name) {
				$_instructors .= "{$name},";
				$instructorEnc = crypto::ev($name);
				$name = strtoupper($name);
				$subject = "🏂 #{$r['oidx']}: 學生{$studentName}交易逾時通知～";
				$content = "{$name} 教練您好!\r\n以下是學生訂課的內容:\r\n\r\n";
				$content.= $lessonContent . "\r\n\r\n" . $costContent . "\r\n\r\n" ;
				//$content.= "學生聯絡資訊:\r\n暱稱:{$studentName}\r\nEmail: {$student[0]['email']}\r\nLINE: {$student[0]['line']}\r\nWeChat: {$student[0]['wechat']}\r\n";
				//$content.= "手機:{$student[0]['country']}, {$student[0]['phone']}\r\n";
				$content.= "訂單保險編號 #{$r['oidx']}\r\n";
				$content.= "訂課方式：{$CLASSTYPE[$orderType]}\r\n";
				if($orderType=='rule'){
					$content.= "PS: 原訂課為條件式開課，由於學生取消訂課，之前相同條件的開課也已自動取消，請儘速回教練後台確認後，新增指定開課或條件開課以利學生再次訂課。\r\n\r\n";
				}
				$content.= "訂課方式：{$CLASSTYPE[$orderType]}\r\n";
				$content.= "SKIDIY 自助滑雪\r\nadmin@diy.ski";
				$instructor = $ko->getMembers(['type'=>'instructor','name'=>$name]);//_v($instructor);
				_v($content);
				if($orderType=='rule'){//改為條件開課才需告知教練
					$subject = "🏂 #{$r['oidx']}: 學生{$studentName} 條件開課取消通知";
					$ok = $ACCOUNT->send_mail([
						'email' 	=> $instructor[0]['email'],
						'subject'	=> $subject,
						'content'	=> $content,
					]);_d($instructor[0]['email'].'='.$ok);
				}//if rule
			}
			$_instructors = substr($_instructors, 0, -1);

			//管理者
			$studentName = strtoupper($student[0]['name']);
			$subject = "🏂 #{$r['oidx']}: 學生 {$studentName} 訂單交易逾時。";
			$content = "以下是學生的訂課內容:\r\n\r\n";
			$content.= $lessonContent . "\r\n\r\n" . $costContent . "\r\n\r\n" ;
			$content.= "學生聯絡資訊:\r\n暱稱:{$studentName}\r\nEmail: {$student[0]['email']}\r\nLINE: {$student[0]['line']}\r\nWeChat: {$student[0]['wechat']}\r\n";
			$content.= "手機:{$student[0]['country']}, {$student[0]['phone']}\r\n";
			$content.= "訂單保險編號 #{$r['oidx']}\r\n";
			$content.= "訂課方式：{$CLASSTYPE[$orderType]}\r\n";
			if($orderType=='rule'){
				$content.= "PS: 此訂課為條件式開課，已Email通知教練後台確認後新增指定開課或條件開課以利學生再次訂課。\r\n\r\n";
			}
			_v($content);
			$ok = $ACCOUNT->send_mail([
				'email' 	=> $adminEmail,
				'subject'	=> $subject,
				'content'	=> $content,
			]);_v($adminEmail);

			//回復訂單之開課
			$ko->rollbackOrder($order['oidx'],'timeout');

			$notifyUpdate = true;
			break;

		case 'resvAcception'://申請開課
			switch ($r['resp']) {
				case 1:
				case 'true'://教練接受
					//學生
					$instructorName = strtoupper(implode('，', $instructors));
					$studentName = strtoupper($student[0]['name']);
					$subject = "🏂 {$studentName} 您好，申請開課成功通知(#{$r['oidx']})。";
					$content = "{$studentName} 您好!\r\n教練{$instructorName}已接受開課，以下是您訂課的內容:\r\n\r\n";
					$content.= $lessonContent . "\r\n\r\n" . $costContent . "\r\n\r\n" ;
					$content.= $bookingNote . "\r\n";
					$content.= "訂單保險編號 #{$r['oidx']}\r\n";
					$content.= "如有任疑問請隨時與我們聯繫。\r\n";
					$content.= "SKIDIY 自助滑雪\r\nadmin@diy.ski";
					if(empty($r['resent'])||(stripos($r['resent'],'student')!==false)){
						_v($content);
						$ok = $ACCOUNT->send_mail([
							'email' 	=> [$student[0]['email']],
							'subject'	=> empty($r['resent']) ? $subject : $r['subject'],
							'content'	=> $content,
						]);_d($student[0]['email'].'='.$ok);
					}
					//教練
					$resvArranged = $order['extraInfo']['arranged'] ? '(有經管理員排課)' : '';
					foreach ($instructors as $name) {
						$name = strtoupper($name);
						$subject = "🏂 #{$r['oidx']}: 接受開課成功通知{$resvArranged}。";
						$content = "{$name} 教練您好!\r\n感謝您接受開課，以下是學生訂課的內容:\r\n\r\n";
						$content.= $lessonContent . "\r\n\r\n" . $costContent . "\r\n\r\n" ;
						//$content.= "學生聯絡資訊:\r\n暱稱:{$studentName}\r\nEmail: {$student[0]['email']}\r\nLINE: {$student[0]['line']}\r\nWeChat: {$student[0]['wechat']}\r\n";
						//$content.= "手機:{$student[0]['country']}, {$student[0]['phone']}\r\n";
						$content.= "訂單保險編號 #{$r['oidx']}\r\n";
						$content.= "訂課方式：{$CLASSTYPE[$orderType]}{$resvArranged}\r\n";

						$content.= "SKIDIY 自助滑雪\r\nadmin@diy.ski";
						$instructor = $ko->getMembers(['type'=>'instructor','name'=>$name]);//_v($instructor);
						if(empty($r['resent'])||(stripos($r['resent'],'instructor')!==false)){
							_v($content);
							$ok = $ACCOUNT->send_mail([
								'email' 	=> $instructor[0]['email'],
								'subject'	=> empty($r['resent']) ? $subject : $r['subject'],
								'content'	=> $content,
							]);_d($instructor[0]['email'].'='.$ok);
						}
					}//foreach教練
					//管理者
					$studentName = strtoupper($student[0]['name']);
					$subject = "🏂 #{$r['oidx']}: 學生 {$studentName} 與教練 {$instructorName} 申請與接受開課成功通知{$resvArranged}。";
					$content = "以下是開課內容:\r\n\r\n";
					$content.= $lessonContent . "\r\n\r\n" . $costContent . "\r\n\r\n" ;
					$content.= "學生聯絡資訊:\r\n暱稱:{$studentName}\r\nEmail: {$student[0]['email']}\r\nLINE: {$student[0]['line']}\r\nWeChat: {$student[0]['wechat']}\r\n";
					$content.= "手機:{$student[0]['country']}, {$student[0]['phone']}\r\n";
					$content.= "訂單保險編號 #{$r['oidx']}\r\n";
					$content.= "訂課方式：{$CLASSTYPE[$orderType]}{$resvArranged}\r\n";
					if(empty($r['resent'])||(stripos($r['resent'],'admin')!==false)){
						_v($content);
						$ok = $ACCOUNT->send_mail([
							'email' 	=> $adminEmail,
							'subject'	=> empty($r['resent']) ? $subject : $r['subject'],
							'content'	=> $content,
						]);_v($adminEmail);
						_v($order['extraInfo']);
					}
					//保險員
					if($ko->isInWeekOrder($lessonFirstDate)){//一週內的訂單要趕快通知保險
						$subject = "🏂 #{$r['oidx']}: {$studentName} 一週內訂課通知，請儘速處理保險事宜，謝謝。";
						$content = "以下是學生一週內的訂課內容:\r\n\r\n";
						$content.= $lessonContent . "\r\n\r\n" . $costContent . "\r\n\r\n";
						$content.= "學生聯絡資訊:\r\n暱稱:{$studentName}\r\nEmail: {$student[0]['email']}\r\nLINE: {$student[0]['line']}\r\nWeChat: {$student[0]['wechat']}\r\n";
						$content.= "手機:{$student[0]['country']}, {$student[0]['phone']}\r\n";
						$content.= "訂單保險編號 #{$r['oidx']}\r\n";

						if(empty($r['resent'])||(stripos($r['resent'],'insurance')!==false)){
							_v($content);
							$ok = $ACCOUNT->send_mail([
								'email' 	=> $insuranceEmail,
								'subject'	=> empty($r['resent']) ? $subject : $r['subject'],
								'content'	=> $content,
							]);//_d($student[0]['email'].'='.$ok);
						}
					}
					break;
				case 'false'://教練拒絕
					$studentName = strtoupper($student[0]['name']);
					//教練
					foreach ($instructors as $name) {
						$name = ucfirst($name);
						$subject = "🏂 #{$r['oidx']}: 拒絕開課記錄通知。";
						$content = "{$name} 教練您好!\r\n您已拒絕學生{$studentName}以下開課:\r\n\r\n";
						$content.= $lessonContent . "\r\n\r\n" . $costContent . "\r\n\r\n" ;
						$content.= "訂課方式：{$CLASSTYPE[$orderType]}\r\n";
						$content.= "SKIDIY 自助滑雪\r\nadmin@diy.ski";
						$instructor = $ko->getMembers(['type'=>'instructor','name'=>$name]);//_v($instructor);
						_v($content);
						$ok = $ACCOUNT->send_mail([
							'email' 	=> $instructor[0]['email'],
							'subject'	=> $subject,
							'content'	=> $content,
						]);_d($instructor[0]['email'].'='.$ok);
					}//foreach教練
					//管理者
					$studentName = strtoupper($student[0]['name']);
					$subject = "🏂 #{$r['oidx']}: {$instructorName}教練已拒絕學生{$studentName}申請開課，請重新安排教練。";
					$content = "以下是申請開課內容:\r\n\r\n";
					$content.= $lessonContent . "\r\n\r\n" . $costContent . "\r\n\r\n" ;
					$content.= "排課連結：待設計。\r\n\r\n";
					$content.= "學生聯絡資訊:\r\n暱稱:{$studentName}\r\nEmail: {$student[0]['email']}\r\nLINE: {$student[0]['line']}\r\nWeChat: {$student[0]['wechat']}\r\n";
					$content.= "手機:{$student[0]['country']}, {$student[0]['phone']}\r\n";
					$content.= "訂單保險編號 #{$r['oidx']}\r\n";
					$content.= "訂課方式：{$CLASSTYPE[$orderType]}\r\n";
					_v($content);
					$ok = $ACCOUNT->send_mail([
						'email' 	=> $adminEmail,
						'subject'	=> $subject,
						'content'	=> $content,
					]);_v($adminEmail);
					break;
			}

			$notifyUpdate = true;
			break;

		case 'acceptTimeout'://申請開課教練逾時
			//教練
			foreach ($instructors as $name) {
				$_name = strtoupper($name);
				$subject = "🏂 #{$r['oidx']}: 學生{$studentName}申請開課已逾時取消。";
				$content = "{$_name} 教練您好!\r\n因系統未收到您的開課回覆，以下開課申請已取消:\r\n\r\n";
				$content.= $lessonContent . "\r\n\r\n" . $costContent . "\r\n\r\n" ;
				$content.= "訂課方式：{$CLASSTYPE[$orderType]}\r\n";
				$content.= "SKIDIY 自助滑雪\r\nadmin@diy.ski";
				$instructor = $ko->getMembers(['type'=>'instructor','name'=>$name]);//_v($instructor);
				_v($content);
				$ok = $ACCOUNT->send_mail([
					'email' 	=> $instructor[0]['email'],
					'subject'	=> $subject,
					'content'	=> $content,
				]);_d($instructor[0]['email'].'='.$ok);
			}//foreach 教練
			//該申請, 此教練設定為拒絕
			$ko->setAcception(['accepted'=>'false'],['oidx'=>$r['oidx'],'instructor'=>$_name]);

			//管理者
			$studentName = strtoupper($student[0]['name']);
			$subject = "🏂 #{$r['oidx']}: 學生 {$studentName} 申請開課教練逾時未回覆，請重新安排教練。";
			$content = "以下是學生申請開課內容:\r\n\r\n";
			$content.= $lessonContent . "\r\n\r\n" . $costContent . "\r\n\r\n" ;
			$content.= "排課連結：待設計。\r\n\r\n";
			$content.= "學生聯絡資訊:\r\n暱稱:{$studentName}\r\nEmail: {$student[0]['email']}\r\nLINE: {$student[0]['line']}\r\nWeChat: {$student[0]['wechat']}\r\n";
			$content.= "手機:{$student[0]['country']}, {$student[0]['phone']}\r\n";
			$content.= "訂單保險編號 #{$r['oidx']}\r\n";
			$content.= "訂課方式：{$CLASSTYPE[$orderType]}\r\n";
			_v($content);
			$ok = $ACCOUNT->send_mail([
				'email' 	=> $adminEmail,
				'subject'	=> $subject,
				'content'	=> $content,
			]);_v($adminEmail);

			$notifyUpdate = true;
			break;

		case 'orderCanceling'://學生申請取消訂單
			//學生
			$studentName = ucfirst($student[0]['name']);
			$subject = "🏂 {$studentName} 您好，我們已在處理您的訂單取消作業 (#{$r['oidx']})。";
			$content = "{$studentName} 您好!\r\n以下是您將取消的訂課內容:\r\n\r\n";
			$content.= $lessonContent . "\r\n\r\n" . $costContent . "\r\n\r\n" ;
			$content.= "訂單保險編號 #{$r['oidx']}\r\n";
			$content.= "訂課方式：{$CLASSTYPE[$orderType]}\r\n";
			$content.= "如有任疑問請隨時與我們聯繫。\r\n";
			$content.= "SKIDIY 自助滑雪\r\nadmin@diy.ski";

			if(empty($r['resent'])||(stripos($r['resent'],'student')!==false)){
				_v($content);
				$ok = $ACCOUNT->send_mail([
					'email' 	=> $student[0]['email'],
					'subject'	=> $subject,
					'content'	=> $content,
				]);_d($student[0]['email'].'='.$ok);
			}

			//管理者
			$studentName = ucfirst($student[0]['name']);
			$subject = "🏂 #{$r['oidx']}: 學生 {$studentName} 申請訂單取消，請撥空處理。";
			$content = "以下是學生原本的訂課內容:\r\n\r\n";
			$content.= $lessonContent . "\r\n\r\n" . $costContent . "\r\n\r\n" ;
			$content.= "學生聯絡資訊:\r\n暱稱:{$studentName}\r\nEmail: {$student[0]['email']}\r\nLINE: {$student[0]['line']}\r\nWeChat: {$student[0]['wechat']}\r\n";
			$content.= "手機:{$student[0]['country']}, {$student[0]['phone']}\r\n";
			$content.= "訂單保險編號 #{$r['oidx']}\r\n";
			$content.= "訂課方式：{$CLASSTYPE[$orderType]}\r\n";

			if(empty($r['resent'])||(stripos($r['resent'],'admin')!==false)){
				_v($content);
				$ok = $ACCOUNT->send_mail([
					'email' 	=> $adminEmail,
					'subject'	=> $subject,
					'content'	=> $content,
				]);_v($adminEmail);
			}

			$notifyUpdate = true;
			break;

		case 'orderCanceled'://管理員刪除訂單
			//學生
			$studentName = strtoupper($student[0]['name']);
			$subject = "🏂 {$studentName} 您好，我們已取消您的訂單 (#{$r['oidx']})。";
			$content = "{$studentName} 您好!\r\n以下是您之前的訂課內容:\r\n\r\n";
			$content.= $lessonContent . "\r\n\r\n" . $costContent . "\r\n\r\n" ;
			$content.= "🚩請注意～ 因第三方支付退刷規定關係，統一於每週一早上8-12點退刷結算，若不在此時間之前申請取消，需等到隔週一才能處理，謝謝！\r\n\r\n" ;
			$content.= "訂單保險編號 #{$r['oidx']}\r\n";
			$content.= "訂課方式：{$CLASSTYPE[$orderType]}\r\n";
			$content.= "如有任疑問請隨時與我們聯繫。\r\n";
			$content.= "SKIDIY 自助滑雪\r\nadmin@diy.ski";

			if(empty($r['resent'])||(stripos($r['resent'],'student')!==false)){
				_v($content);
				$ok = $ACCOUNT->send_mail([
					'email' 	=> array_merge([$student[0]['email']], $insuranceEmail),
					'subject'	=> $subject,
					'content'	=> $content,
				]);_d($student[0]['email'].'='.$ok);
			}

			//教練
			$_instructors = '';
			foreach ($instructors as $name) {
				$_instructors .= "{$name},";
				$name = ucfirst($name);
				$subject = "🏂 #{$r['oidx']}: 學生{$studentName}訂單取消通知～";
				$content = "{$name} 教練您好!\r\n以下學生訂課內容已取消:\r\n\r\n";
				$content.= $lessonContent . "\r\n\r\n" . $costContent . "\r\n\r\n" ;
				//$content.= "學生聯絡資訊:\r\n暱稱:{$studentName}\r\nEmail: {$student[0]['email']}\r\nLINE: {$student[0]['line']}\r\nWeChat: {$student[0]['wechat']}\r\n";
				//$content.= "手機:{$student[0]['country']}, {$student[0]['phone']}\r\n";
				$content.= "訂單保險編號 #{$r['oidx']}\r\n";
				$content.= "訂課方式：{$CLASSTYPE[$orderType]}\r\n";
				if($orderType=='rule'){
					$content.= "PS: 此訂課為條件式開課，相同條件的開課也已自動取消，請儘速回教練後新增指定開課或條件開課以利學生再次訂課。\r\n\r\n";
				}

				$content.= "SKIDIY 自助滑雪\r\nadmin@diy.ski";
				$instructor = $ko->getMembers(['type'=>'instructor','name'=>$name]);//_v($instructor);

				if(empty($r['resent'])||(stripos($r['resent'],'instructor')!==false)){
					_v($content);
					$ok = $ACCOUNT->send_mail([
						'email' 	=> $instructor[0]['email'],
						'subject'	=> $subject,
						'content'	=> $content,
					]);_d($instructor[0]['email'].'='.$ok);
				}
			}
			$_instructors = substr($_instructors, 0, -1);

			//管理者
			$studentName = ucfirst($student[0]['name']);
			$subject = "🏂 #{$r['oidx']}: 學生 {$studentName} 訂單已從管理員後台取消完成。";
			$content = "以下是學生原本的訂課內容:\r\n\r\n";
			$content.= $lessonContent . "\r\n\r\n" . $costContent . "\r\n\r\n" ;
			$content.= "學生聯絡資訊:\r\n暱稱:{$studentName}\r\nEmail: {$student[0]['email']}\r\nLINE: {$student[0]['line']}\r\nWeChat: {$student[0]['wechat']}\r\n";
			$content.= "手機:{$student[0]['country']}, {$student[0]['phone']}\r\n";
			$content.= "訂單保險編號 #{$r['oidx']}\r\n";
			$content.= "訂課方式：{$CLASSTYPE[$orderType]}\r\n";
			if($orderType=='rule'){
				$content.= "PS: 此訂課為條件式開課，已Email通知教練後台確認後新增指定開課或條件開課以利學生再次訂課。\r\n\r\n";
			}

			if(empty($r['resent'])||(stripos($r['resent'],'admin')!==false)){
				_v($content);
				$ok = $ACCOUNT->send_mail([
					'email' 	=> $adminEmail,
					'subject'	=> $subject,
					'content'	=> $content,
				]);_v($adminEmail);
			}
			//回復訂單之開課
			$ko->rollbackOrder($order['oidx'],'canceled');

			$notifyUpdate = true;
			break;

		case 'orderRefund'://管理員退訂紀錄
			//學生
			$studentName = ucfirst($student[0]['name']);
			$subject = "🏂 {$studentName} 您好，我們已刷退您的訂單 (#{$r['oidx']})。";
			$content = "{$studentName} 您好!\r\n以下的刷卡金額我們已經刷退(扣除手續費 3%)，兩個工作天會退款到刷卡銀行，銀行到個人帳戶約兩個禮拜，還請您撥空確認是否入帳:\r\n\r\n";
			$content.= "刷卡金額: {$order['paid']}\r\n" ;
			$content.= "訂單編號 {$order['orderNo']}\r\n";
			$content.= "如有任疑問請隨時與我們聯繫。\r\n";
			$content.= "SKIDIY 自助滑雪\r\nadmin@diy.ski";

			if(empty($r['resent'])||(stripos($r['resent'],'student')!==false)){
				_v($content);
				$ok = $ACCOUNT->send_mail([
					'email' 	=> $student[0]['email'],
					'subject'	=> $subject,
					'content'	=> $content,
				]);_d($student[0]['email'].'='.$ok);
			}

			//管理者
			$studentName = ucfirst($student[0]['name']);
			$subject = "🏂 #{$r['oidx']}: 學生 {$studentName} 訂單已從管理員後台退訂完成。";
			$content = "以下是學生原本的訂課內容:\r\n\r\n";
			$content.= $costContent . "\r\n\r\n" ;
			$content.= "學生聯絡資訊:\r\n暱稱:{$studentName}\r\nEmail: {$student[0]['email']}\r\nLINE: {$student[0]['line']}\r\nWeChat: {$student[0]['wechat']}\r\n";
			$content.= "手機:{$student[0]['country']}, {$student[0]['phone']}\r\n";
			$content.= "訂單保險編號 #{$r['oidx']}\r\n";
			$content.= "訂課方式：{$CLASSTYPE[$orderType]}\r\n";
			if($orderType=='rule'){
				$content.= "PS: 此訂課為條件式開課，已Email通知教練後台確認後新增指定開課或條件開課以利學生再次訂課。\r\n\r\n";
			}

			if(empty($r['resent'])||(stripos($r['resent'],'admin')!==false)){
				_v($content);
				$ok = $ACCOUNT->send_mail([
					'email' 	=> $adminEmail,
					'subject'	=> $subject,
					'content'	=> $content,
				]);_v($adminEmail);
			}

			$notifyUpdate = true;
			break;

		case 'insuranceNotify'://保險員後台檢查後通知學生補齊資料
			// disable ; cause of the new insurance notify online at 2019.11.26
			//break;
			$studentName = ucfirst($student[0]['name']);
			$subject = "🏂  {$studentName} 您好, SKIDIY提醒您保險資料尚未齊全。";
  			//$content = "{$studentName} 您好，您的保險資料尚未齊全，請點擊下列表單網址填寫『所有』上課學員的資料喔～\r\n";
  			//$content.= "保險表單: http://goo.gl/vh5noU\r\n\r\n";
  			//$content.= "也可登入官網 https://diy.ski 後，至『帳號』-> 『訂單資訊』 點擊保險填寫連結，謝謝您～\r\n\r\n";
  			$content = "{$studentName} 您好，您的保險資料尚未齊全～\r\n";
  			$content.= "請您登入官網 https://diy.ski 後，至『帳號』-> 『訂單資訊』 點擊對應訂單後，確認『所有』上課學員的保險資料是否皆已完成填寫，謝謝您～\r\n";
  			$content.= "以下是您的訂課內容, 供您參考:\r\n";
			$content.= $lessonContent . "\r\n\r\n" . $costContent . "\r\n\r\n" ;
  			$content.= "訂單保險編號 #{$r['oidx']}\r\n";

  			if(empty($r['resent'])||(stripos($r['resent'],'student')!==false)){
				_v($content);
				$ok = $ACCOUNT->send_mail([
					'email' 	=> $student[0]['email'],
					'subject'	=> $subject,
					'content'	=> $content,
				]);_d($student[0]['email'].'='.$ok);
			}

			$notifyUpdate = true;
			break;
		case 'insuranceNotify_v2'://保險檢查上課前14天，提醒通知學生補齊資料
			//==================================================================================
            $order_id = $r['oidx'];
            $insuranceFUNC 	= new INSURANCE();
            $MEMBER_FUNC 	= new MEMBER();
            $insuranceList 	= $insuranceFUNC->get_list($order_id);
      
            $c=0;
            $insurance_total_people=0;
            $insurance_info='';
            if(count($insuranceList)>0){
              foreach ($insuranceList as $key => $value) {
                $c++;
                $insurance_info .= $c.". ".$value['pcname']." | ".$value['birthday']." | ".$value['twid']."\r\n";                            
                if($value['master']=='Y'){
                  $insurance_total_people = $value['inusrance_num'];
                }else{ // 其他團員
		        // send mail
	              $sec_idx 	= crypto::ev($value['idx']);	              
	              $sec_oidx	= crypto::ev($order_id);	
	              $modify_link = 'https://diy.ski/insurance_fapply.php?id='.$sec_oidx.'&qid='.$sec_idx.'&m=m';
		          $mail_info['email']    = $value['email'];
		          $mail_info['subject']  = "🏂  {$value['pcname']} 您好, SKIDIY提醒您即將上課（訂單編號：#".$order_id."）";
		          $mail_info['content']  = $value['pcname']." 您好,\r\n提醒您即將上課，您先前填寫的保險資料如下:\r\n\r\n";
		          $mail_info['content'] .= "姓名: ".$value['pcname'].", 出生日期: ".$value['birthday'].", 身分證:".$value['twid']." \r\n\r\n";  
				  $mail_info['content'] .= "以上若有錯誤，請儘速點擊下列連結修改，謝謝。\r\n";
				  $mail_info['content'] .= $modify_link;

		          //echo $mail_info['content'];
		          $MEMBER_FUNC->send_mail($mail_info);
                }                   
              }

              if($insurance_total_people  > count($insuranceList)){
                $remind = $insurance_total_people - count($insuranceList);                                                
              }             
            }else{
              $remind = 0;
            }
            $sec_oidx = crypto::ev($order_id);//_d($sec_oidx);
            $insurance_add_link ="https://diy.ski/insurance_fapply.php?id=".$sec_oidx;          
            $order_booking_link ="https://diy.ski/class_booking_edit.php?id=".$sec_oidx;            
            $insurance_info .= "\r\n為維護團員的權益，請您儘快通知其他 ".$remind." 人填寫保險資訊，保單填寫網址如下；\r\n".$insurance_add_link."\r\n";
            //==================================================================================            

			$studentName = ucfirst($student[0]['name']);
			$subject = "🏂  {$studentName} 您好, SKIDIY提醒您即將上課（訂單編號：#".$order_id."）";
			if($insurance_total_people==0){
				$content = "{$studentName} 您好，提醒您即將上課，您目前尚未填寫任何保單！\r\n";
				$content.= "您可登入官網 https://diy.ski 後，至『帳號』-> 『訂單資訊』 點擊對應訂單後，確認『所有』上課學員的保險資料是否皆已完成填寫，謝謝您～\r\n";
			}else if($remind>0){
  				$content = "{$studentName} 您好，提醒您即將上課，目前仍有".$remind."人尚未填寫保險表單，已填人員如下：\r\n";
  				$content.= $insurance_info;
  				$content.= "您可登入官網 https://diy.ski 後，至『帳號』-> 『訂單資訊』 點擊對應訂單後，確認『所有』上課學員的保險資料是否皆已完成填寫，謝謝您～\r\n";
  			}else{
  				$content = "{$studentName} 您好，提醒您即將上課，請再次確認相關保單資料是否正確完整～\r\n";
  				$content.= "若是您仍尚未完成相關保單填寫！";
  				$content.= "您可登入官網 https://diy.ski 後，至『帳號』-> 『訂單資訊』 點擊對應訂單後，確認『所有』上課學員的保險資料是否皆已完成填寫，謝謝您～\r\n";
  			}
  			$content.= "或是使用下列快速鏈結進行檢視確認：\r\n".$order_booking_link."\r\n\r\n";
  			

  			$content.= "以下是您的訂課內容, 供您參考:\r\n";
			$content.= $lessonContent . "\r\n" ;
  			$content.= "訂單保險編號 #{$r['oidx']}\r\n";

  			if(empty($r['resent'])||(stripos($r['resent'],'student')!==false)){
				_v($content);
				$ok = $ACCOUNT->send_mail([
					'email' 	=> $student[0]['email'],
					//'email' 	=> 'mauji168@gmail.com',
					'subject'	=> $subject,
					'content'	=> $content,
				]);
				_d($student[0]['email'].'='.$ok);
			}

			$notifyUpdate = true;
			break;			

		case 'orderChangeStudent'://學生自行變更上課人數
			//學生
			$studentName = ucfirst($student[0]['name']);
			$subject = "🏂  {$studentName} 您好, SKIDIY通知您的上課人數已變更完成。";
  			$content = "{$studentName} 您好，若上課人數變多，請補上新學員的保險資料喔～\r\n";
  			$content.= "保險表單: http://goo.gl/vh5noU\r\n\r\n";
  			$content.= "也可登入官網 https://diy.ski 後，至『帳號』-> 『訂單資訊』 點擊保險填寫連結，謝謝您～\r\n\r\n";
  			$content.= "以下是您變更後的訂課內容, 供您參考:\r\n";
			$content.= $lessonContent . "\r\n\r\n" . $costContent . "\r\n\r\n" ;
  			$content.= "訂單保險編號 #{$r['oidx']}\r\n";

  			if(empty($r['resent'])||(stripos($r['resent'],'student')!==false)){
				_v($content);
				$ok = $ACCOUNT->send_mail([
					'email' 	=> $student[0]['email'],
					'subject'	=> $subject,
					'content'	=> $content,
				]);_d($student[0]['email'].'='.$ok);
			}

			//教練
			foreach ($instructors as $name) {
				$name = ucfirst($name);
				$subject = "🏂 #{$r['oidx']}: 學生{$studentName}上課人數變動通知～";
				$content = "{$name} 教練您好!\r\n學生變動後的課程資訊如下:\r\n\r\n";
				$content.= $lessonContent . "\r\n\r\n" . $costContent . "\r\n\r\n" ;
				//$content.= "學生聯絡資訊:\r\n暱稱:{$studentName}\r\nEmail: {$student[0]['email']}\r\nLINE: {$student[0]['line']}\r\nWeChat: {$student[0]['wechat']}\r\n";
				//$content.= "手機:{$student[0]['country']}, {$student[0]['phone']}\r\n";
				$content.= "訂單保險編號 #{$r['oidx']}\r\n";
				$content.= "訂課方式：{$CLASSTYPE[$orderType]}\r\n";
				$content.= "SKIDIY 自助滑雪\r\nadmin@diy.ski";
				$instructor = $ko->getMembers(['type'=>'instructor','name'=>$name]);//_v($instructor);

				if(empty($r['resent'])||(stripos($r['resent'],'instructor')!==false)){
					_v($content);
					$ok = $ACCOUNT->send_mail([
						'email' 	=> $instructor[0]['email'],
						'subject'	=> $subject,
						'content'	=> $content,
					]);_d($instructor[0]['email'].'='.$ok);
				}
			}

			//保險
			$subject = "🏂 #{$r['oidx']}: 學生{$studentName}上課人數變動通知～";
			$content = "您好!\r\n學生變動後的課程資訊如下:\r\n\r\n";
			$content.= $lessonContent . "\r\n\r\n";
			$content.= "學生聯絡資訊:\r\n暱稱:{$studentName}\r\nEmail: {$student[0]['email']}\r\nLINE: {$student[0]['line']}\r\nWeChat: {$student[0]['wechat']}\r\n";
			$content.= "手機:{$student[0]['country']}, {$student[0]['phone']}\r\n";
			$content.= "訂單保險編號 #{$r['oidx']}\r\n";
			if(empty($r['resent'])||(stripos($r['resent'],'insurance')!==false)){
				_v($content);
				$ok = $ACCOUNT->send_mail([
					'email' 	=> $insuranceEmail,
					'subject'	=> $subject,
					'content'	=> $content,
				]);_d($student[0]['email'].'='.$ok);
			}

			$notifyUpdate = true;
			break;

	}//switch type

	//更新已寄送
	if($notifyUpdate){
		$sql = "UPDATE `notify` SET `sent`=1 WHERE `idx`={$r['idx']}";
		$db->query('UPDATE', $sql);
	}else{
		//未定義的 type
	}

}//foreach notify

