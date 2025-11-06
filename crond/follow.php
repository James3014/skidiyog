<?php
require('/var/www/html/1819/includes/sdk.php');
exit();
if(!in_array((int)date('N'), [1, 4]) && empty($argv[1])){
    echo 'Not today!';
    exit();
}

$ko = new ko();
$parkInfo = $ko->getParkInfo();
$instructorInfo = $ko->getInstructorInfo();
$ACCOUNT = new MEMBER();
//申請逾時
$db = new DB();
$sql = "SELECT f.`date`, f.`park`, f.`expertise`, f.`instructors`, m.`name`, m.`email`
		FROM `follow` AS `f` 
		LEFT JOIN `members_v2` AS `m` 
		ON f.`student`=m.`idx`
        WHERE f.`deleted`=0
        AND `date` > CURDATE()
		ORDER BY f.`idx` ASC";//_d($sql);
$res = $db->query('SELECT', $sql);//_v($res);

foreach ($res as $f) {

	$startDate = date('Y-m-d', strtotime('-3 days', strtotime($f['date'])));
    $endDate = date('Y-m-d', strtotime('+3 days', strtotime($f['date'])));//_d("{$startDate}~{$endDate}");
    $f['instructors'] = json_decode($f['instructors'], true);
    $where = '';

    //先找固定開課
    if($f['park']!='any'){
    	$where .= "AND `park`='{$f['park']}'";
    }
    if($f['instructors'][0]!='any'){
        foreach ($f['instructors'] as $idx => $inst) {
            if(in_array($inst, ['dandan'])){//排除不適合教練
                unset($f['instructors'][$inst]);echo "排除{$inst}\n";
            }
        }
    	$str = implode("','", $f['instructors']);
    	$where .= "AND `instructor` IN ('{$str}')";
    }
    $sql = "SELECT * FROM `schedules` 
    		WHERE `oidx`=0 AND `expertise`!='disable'
    		AND (`expertise`='{$f['expertise']}' OR `expertise`='both') 
    		AND `date` BETWEEN '{$startDate}' AND '{$endDate}' {$where}
    		ORDER BY `date`";//_d($sql);

    $lessons = $db->query('SELECT', $sql);
    $info = [];
    foreach ($lessons as $l) {
    	if(!isset($info[$l['park']][$l['instructor']])){
    		$info[$l['park']][$l['instructor']] = 1;
    	}else{
    		$info[$l['park']][$l['instructor']] ++;
    	}
    	
    }//foreach lessons
    //_v($info);

    //再找條件開課
    $sql = "SELECT * FROM `rules`
            WHERE `matched`=0 AND ( 
                ('{$startDate}'>=DATE(`start`) AND '{$startDate}'<=DATE(`end`))
                OR
                ('{$endDate}'>=DATE(`start`) AND '{$endDate}'<=DATE(`end`))
            ) {$where}";//echo("\n{$sql}\n");
    $rules = $db->query('SELECT', $sql);
    $rInfo = [];
    foreach ($rules as $r) {
        if($r['instructor']=='dandan') continue;
        $rInfo[$r['park']][$r['instructor']]["{$r['start']}~{$r['end']}"] = "{$r['days']},{$r['lessons']}";//以雪場為主
        //$rInfo[$r['instructor']][$r['park']]["{$r['start']}~{$r['end']}"] = "{$r['days']},{$r['lessons']}";//以教練為主
    }//_v($rInfo);



    //Email 顯示追蹤條件
    $instructorsList = '';
    if($f['instructors'][0]=='any'){
    	$instructorsList = '不限';
    }else{
	    foreach ($f['instructors'] as $i) {
	    	$instructorsList .= $instructorInfo[$i]['cname'] . ', ';
	    }
	    $instructorsList = substr($instructorsList, 0, -2);
	}
    $rule = 
    		"   日期：{$startDate} ~ {$endDate}\n   課程：" . ($f['expertise']=='sb'?'單板':'雙板') . "\n" .
    		"   雪場：" . (($f['park']=='any' ? '不限' : $parkInfo[$f['park']]['cname'])) . "\n" .
    		"   教練：{$instructorsList}\n\n";

    //信件內容
    $content = '';
    foreach ($info as $park => $instructors) {
        $content .= "<{$parkInfo[$park]['cname']}>\n";
        foreach ($instructors as $i => $cnt) {
            $content .= "   {$instructorInfo[$i]['cname']}: {$cnt}堂\n";
        }
    }//echo $content."\n\n";

    if(count($rInfo)){
        if(!empty($content)){
            $content.="\n另外可調度教練的課程有：\n";
        }

        // 已雪場為主
        foreach ($rInfo as $park => $instructors) {
            $content .= "<{$parkInfo[$park]['cname']}>\n";
            foreach ($instructors as $instructor => $rules) {
                foreach ($rules as $period => $condition) {
                    list($day, $lesson) = explode(',', $condition);
                    if($day==1 && $lesson==1){
                        $content .= "   {$instructorInfo[$instructor]['cname']}: {$period}期間內，至少選課一堂以上才會開課。\n";
                    }else{
                        $content .= "   {$instructorInfo[$instructor]['cname']}: {$period}期間內，需連續{$day}天，選課達{$lesson}堂以上才會開課。\n";
                    }
                }//foreach rules
            }//foreach instructors
        }//foreach par

        // 已教練為主
        // foreach ($rInfo as $instructor => $parks) {
        //     $content .= "<{$instructorInfo[$instructor]['cname']}>\n";
        //     foreach ($parks as $park => $rules) {
        //         foreach ($rules as $period => $condition) {
        //             list($day, $lesson) = explode(',', $condition);
        //             if($day==1 && $lesson==1){
        //                 $content .= "   {$parkInfo[$park]['cname']}: {$period}期間內，至少選課一堂以上才會開課。\n";
        //             }else{
        //                 $content .= "   {$parkInfo[$park]['cname']}: {$period}期間內，需連續{$day}天，選課達{$lesson}堂以上才會開課。\n";
        //             }
        //         }//foreach rules
        //     }//foreach instructors
        // }//foreach parks

    }//if rInfo


    if(empty($content)){
        $message = "{$f['name']} 您好,\n\n您目前追蹤的課程尚未開課。\n";
    }else{
        $message = "{$f['name']} 您好,\n\n您追蹤的開課資訊如下:\n{$content}\n";
    	$message.= "詳細開課資訊請至,\nhttps://diy.ski/schedule.php?date={$f['date']}&expertise={$f['expertise']}&park={$f['park']}\n\n";
    }
    $message.= "追蹤條件如下;\n" . $rule;
    $message.= "通知信將於每週ㄧ、四發送, 若要取消追蹤請至 [帳號] -> [課程追蹤] 刪除.\n\n";
    $message.= "SKIDIY敬上.";

    echo "{$message}\n==================================== ====================================\n\n\n";
    if(1){
        $park = ($f['park']=='any') ? '教練' : $parkInfo[$park]['cname'];
        $ok = $ACCOUNT->send_mail([
			'email' 	=> [$f['email']],
            //'email'     => ['admin@diy.ski'],
			'subject'	=> "SKIDIY {$park}課程追蹤通知信 ({$startDate} ~ {$endDate})",
			'content'	=> $message,
		]);
	}
}//foreach follows
