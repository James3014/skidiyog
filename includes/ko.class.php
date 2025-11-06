<?php

class ko{
    public $db = null;

    public function __construct()
    {
        $this->db = new DB();
    }

    public function getMembers($where){//登入
        return $this->db->select('members_v2', $where);
    }

    public function placeOrder($data){//訂單
        return $this->db->insert('orders', $data);
    }

    //log
    public function log($info){
        $info=[
            'severity'  => $info['severity'],
            'user'      => $info['user'],
            'oidx'      => $info['oidx'],
            'msg'       => $info['msg'],
            'resp'      => isset($info['resp']) ? $info['resp'] : '',
            'ip'        => isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : 'localhost',
        ];
        return $this->db->insert('log', $info);
    }

    //產生訂單編號
    public function genOrderNo(){
        $sql = 'SELECT MAX(`oidx`)+1 AS `oidx` FROM `orders`';
        $res = $this->db->query('SELECT', $sql);
        return sprintf('S%d%02d%d%03d', 
                        (int) substr(date('Y'),3,1),
                        (int) date('d'), 
                        (int) $res[0]['oidx'],
                        rand(1,999)
        );
    }

    public function readSchedule($where){//訂課
        return $this->db->select('schedules', $where);
    }

    public function addSchedule($data){//訂課
        return $this->db->insert('schedules', $data);
    }

    public function updateSchedule($data, $where){//訂課
        return $this->db->update('schedules', $data, $where);
    }

    public function deleteSchedule($where){
        return $this->db->delete('schedules', $where);
    }

    public function addResvSchedule($sche){//_v($sche);exit();
        //基本檢查
        if(empty($sche['instructor'])||empty($sche['date'])||empty($sche['slot'])){
            echo 'Payment data error!';
            exit();
        }

        //檢查教練該時段是否有開課.
        $where = [
            'instructor'    => $sche['instructor'],
            'date'          => $sche['date'],
            'slot'          => $sche['slot'],
        ];

        //檢查指定開課, 理論上不會發生
        $oldSche = $this->db->select('schedules', $where);//_v(sizeof($oldSche));exit();
        if( sizeof($oldSche)!=0 && 
            (!empty($oldSche[0]['oidx']) || $oldSche[0]['expertise']=='disable')
        ){//嚴重問題, 預約有出現訂過課或停課的課表
            $this->notify([
                'oidx'              => $oldSche[0]['sidx'],
                'type'              => 'reservationFail',
                'resp'              => json_encode($sche),
                'createDateTime'    => date('Y-m-d H:i:s'),
            ]);
            _v($oldSche);
            echo 'Payment check fail!';
            exit();
        }

        //清掉同日不同雪場的開課. 有oidx不能清(
        //  1.之前同雪場訂課,
        //  2.同訂單foreach申請的同日開課)
        $sql = "DELETE FROM `schedules` 
                WHERE `instructor`='{$sche['instructor']}' AND `date`='{$sche['date']}' 
                AND `park`!='{$sche['park']}' AND `oidx`=0 
                AND `expertise`!='disable'";//停課的不能刪
        $this->db->query('DELETE', $sql);
        //清掉同雪場同時段的開課(因上面只清不同雪場的),接著再新增=>不用update的方式
        if(sizeof($oldSche)!=0){
            $this->db->delete('schedules', $where);
        }
        $this->db->insert('schedules', $sche);//新增開課

        //取消該日有條件開課的
        $sql = "SELECT * FROM `rules` 
                WHERE `instructor`='{$sche['instructor']}' 
                AND DATE(`start`)<=DATE('{$sche['date']}') 
                AND DATE(`end`)>=DATE('{$sche['date']}')";
        $oldRule = $this->db->query('SELECT', $sql);//_d($sql);//_v($oldRule);exit();
        foreach ($oldRule as $r) {//如果有日期在條件內, 取消條件開課
            $this->db->delete('rules', ['idx'=>$r['idx']]);
        }
        return true;
    }

    private function removeSameRuleLesson($idx){
        $rule = $this->db->select('rules',['idx'=>$idx]);//_v($rule);
        if(sizeof($rule)==0) return true;//已被前幾次清掉了
        $sql = "DELETE FROM `rules` 
                WHERE `matched`=0 
                AND `idx`!= $idx 
                AND `instructor`='{$rule[0]['instructor']}' 
                AND `start`= '{$rule[0]['start']}' 
                AND `end`= '{$rule[0]['end']}'";//_d($sql);
        $this->db->query('DELETE', $sql);
    }

    public function updateRuleLesson($data, $where){
        $ok = $this->db->update('rules', $data, $where);
        $this->removeSameRuleLesson($where['idx']);//移掉同區間不同雪場
        return $ok;
    }

    public function getExchageRate(){
        $sql = "SELECT * FROM `exchangeRate`";
        $exchangeRateInfo = $this->db->query('SELECT', $sql);
        $exchangeRateData = [];
        foreach ($exchangeRateInfo as $n => $e) {
            $exchangeRateData[$e['currency']] = $e['exchangeRate'];
        }
        return $exchangeRateData;
    }

    public function updateOrder($data, $where){
        if(empty($where)||empty($where['oidx'])){
            return false;
        }
        return $this->db->update('orders', $data, $where);
    }

    public function getOneOrderInfo($where){
        global $CLASSTYPE;
        //order
        $order = $this->db->select('orders', $where);
        if(empty($order)) return [];
        
        $order = $order[0];

        //若已清除
        if ($order['status']===Payment::PAYMENT_CANCELED||
            $order['status']===Payment::PAYMENT_TIMEOUT||
            $order['status']===Payment::PAYMENT_FAILURE||
            $order['status']===Payment::PAYMENT_REFUND) {
            $backup = empty($order['backup']) ? [] : json_decode($order['backup'],true);
            //_d($order['oidx']);_v($backup);
            if(empty($backup['lessons'])){//沒備份
                $order['schedule'] = [];
                return $order;
            }
            
            //欄位名稱轉換
            //_d($order['oidx']);_v($backup);
            foreach ($backup['lessons'] as $n => $s) {
                $s['studentNum'] = $s['students'];
                $order['schedule'][] = $s;
            }
            return $order;
        }

        //schedule
        $sql = "SELECT * FROM `schedules` WHERE `oidx`={$order['oidx']} 
                ORDER BY `date` ASC, `slot` ASC";
        $schedules = $this->db->query('SELECT', $sql);
        $order['schedule'] = $schedules;

        //order extra info
        $orderType = 'fix';//預設
        $acceptions = [];
        $ruleId = 0;
        foreach ($order['schedule'] as $s) {
            if($s['reservation']!=0){
                $orderType = 'reservation';
                $acceptions = $this->getAcception(['oidx'=>$order['oidx'],'accepted'=>'pending']);//拒絕過一定被管理員排課過
                break;
            }else if($s['rule']!=0){
                $orderType = 'rule';
                $ruleId = $s['rule'];
                break;
            }
        }//foreach 找出訂課種類
        $order['extraInfo'] = [
            'orderType' => $orderType,
            'classType' => $CLASSTYPE[$orderType],
            'ruleId'    => $ruleId,
            'arranged'  => (sizeof($acceptions)) ? true : false,
        ];

        return $order;
    }

    public function getGroupOrderInfo($gidx){
        $info = [];
        $sql = "SELECT o.`oidx`, o.`prepaid`, o.`payment`, o.`currency`, m.`name`, m.`email`, m.`phone`, m.`line`, m.`fbid`, m.`wechat` 
                FROM `orders` AS `o` 
                LEFT JOIN `members_v2` AS `m` ON o.`student`=m.`idx` 
                WHERE o.`gidx`={$gidx} AND o.`status`='success'";
        $info['students'] = $this->db->query('SELECT', $sql);
        $info['group'] = $this->getGroupLessons($gidx);
        return $info;
    }

    public function instructorObsolete($check_name){
        global $INST2223;
        return !in_array($check_name, $INST2223);
    }

    public function getInstructorInfo($where=null){
        if(empty($where)){
            $sql = "SELECT * FROM `instructorInfo`";
            $instructorInfo = $this->db->query('SELECT', $sql);
        }else{
            switch($where['type']){
                case 'picker':
                    $sql = "SELECT * FROM `instructorInfo` WHERE (`expertise`='{$where['expertise']}' OR `expertise`='both')";
                    break;
                case 'instructor':
                    $sql = "SELECT * FROM `instructorInfo` WHERE `name`='{$where['name']}'";
                    break;
                case 'reservation':
                    $sql = "SELECT * FROM `instructorInfo` WHERE `jobType`in('fulltime','support')";
                    break;
            }
            $instructorInfo = $this->db->query('SELECT', $sql);//_d($sql);
        }
        $instructorData = [];
        foreach ($instructorInfo as $n => $i) {
            $instructorData[$i['name']]=$i;
            $instructorData[$i['name']]['parks'] = empty($i['parks']) ? [] : json_decode($i['parks'], true);
        }//_j($parkData);
        ksort($instructorData);
        return $instructorData;
    }

    public function getParkInfo(){
        $sql = "SELECT * FROM `parkInfo`";
        $parkInfo = $this->db->query('SELECT', $sql);
        $parkData = [];
        foreach ($parkInfo as $n => $p) {
            $parkData[$p['name']]=$p;
            $parkData[$p['name']]['timeslot'] = json_decode($p['timeslot'], true);
        }//_j($parkData);
        return $parkData;
    }

    //查詢條件開課
    public function getRuleLessons($data){
        $sql = "SELECT * FROM `rules` 
                WHERE `matched`=0  
                AND `instructor`='{$data['instructor']}' 
                AND `park`='{$data['park']}'
                AND `start`<='{$data['date']}'
                AND `end`>='{$data['date']}'";_d($sql);
        $res = $this->db->query('SELECT', $sql);
        
        if(isset($res[0]['idx'])){
            $r = $res[0];
            $rule = ",ri={$r['idx']},rs={$r['start']},re={$r['end']},rc={$r['days']}d{$r['lessons']}c";
        }else{
            $rule = '';
        }
        return $rule;
    }

    public function getSchedulesResv($data, $instructors, &$distinctParks, $adminArrange=false){//_v($instructors);return [];
        global $AVAIPARKS, $INST2223;
        $day = ((int) date('N', strtotime($data['date']))%7);//_d($day);
        // $startTS = strtotime('-'.($day).' days', strtotime($data['date']));
        // $endTS = strtotime('+'.(6-$day).' days', strtotime($data['date']));
        if($adminArrange){//教練排課直接從申請開課的第一天開始,往後10天內
            $startTS = strtotime($data['date']);
            $endTS = strtotime('+10 days', strtotime($data['date']));
        }else{
            $startTS = strtotime('-3 days', strtotime($data['date']));
            $endTS = strtotime('+3 days', strtotime($data['date']));
        }
        $startDate = date('Y-m-d', $startTS);
        $endDate = date('Y-m-d', $endTS);
        $parkInfo = $this->getParkInfo();
        
        //先填入每個教練有開放的雪場
        $calendar = $schedule = $availableInstructors = [];

        foreach ($instructors as $instructor) {//foreach教練
            if(!in_array($instructor['name'], $INST2223)) continue;

            if($instructor['expertise']!='both' && $instructor['expertise']!=$data['expertise']){
                continue;//排除sb|ski教練
            }

            if(empty($instructor['parks'])){//教練沒開
                continue;
            }

            foreach ($instructor['parks'] as $park) {//foreach教練開課雪場
                if($data['park']!='any' && $park!=$data['park']){//只顯示使用者選的雪場
                    continue;
                }
                // if(!in_array($park, $AVAIPARKS)) continue;//暫不開放

                for($d=$startTS; $d<=$endTS; $d+=24*60*60){//每一天
                    $day = date('Y-m-d', $d);//_d($day);
                    if(!$adminArrange && $this->isIn3Days(strtotime($day))) continue;
                    if(in_array($instructor['name'], ['skidiy']) && $this->isInDays(strtotime($day),7)) continue;//skidiy教練需要一週前預約.
                    for($slot=1; $slot<=4; $slot++){//每一堂, 預設都有四堂, 最後日曆再去過濾2,3,4
                        if($parkInfo[$park]['courseHours']==3 && $slot>=3) continue;//三小時的課一天只有兩堂
                        if(in_array($park,['karuizawa']) && $slot==4 &&  // 藏王12/20才開放到5點
                            (strtotime($day)<strtotime('2024-12-23'))){
                            continue;//輕井澤 2023/12/22之後才可以開四堂課
                        }                        
                        $schedule[$instructor['name']][$day][$park][$slot] = [//設定日曆
                            'sidx'          => 0,
                            'expertise'     => $data['expertise'],
                            'rule'          => ",ri=reservation,rs={$day},re={$day},rc=1d1c",
                        ];
                        $availableInstructors[$instructor['name']] = $instructor['name'];
                    }//for每一堂

                }//for每一天

            }//foreach雪場

        }//foreach教練        
        //_j($schedule);exit();

        //排除指定開停課
        $disabledSlots = [];
        foreach ($availableInstructors as $instructor) {
            $sql = "SELECT * FROM `schedules` 
                    WHERE `instructor`='{$instructor}' 
                    AND DATE(`date`) BETWEEN '{$startDate}' AND '{$endDate}'
                    ORDER BY `date`,`slot`";//_d($sql);
            $lessons = $this->db->query('SELECT', $sql);

            foreach ($lessons as $n => $s) {//_v($s);
                if(empty($s['park']) || $s['expertise']=='disable'){//停課
                    $disabledSlots[$instructor][$s['date']][$s['slot']] = 'disabled';//稍後排除
                }
                if($s['oidx']!=0){//已訂課
                    //_d("{$s['date']},{$s['park']},{$s['slot']}");
                    $thisPark = isset($schedule[$instructor][$s['date']][$s['park']]) ? 
                                $schedule[$instructor][$s['date']][$s['park']] :
                                [];//訂課雪場, 若訂課雪場不在挑選的data['park']內, 一樣清掉
                    //_j($schedule[$instructor][$s['date']]);
                    unset($schedule[$instructor][$s['date']]);//取消該日所有雪場
                    //_j($schedule[$instructor][$s['date']]);
                    $schedule[$instructor][$s['date']][$s['park']] = $thisPark;//回存訂課雪場
                    //_j($schedule[$instructor][$s['date']]);
                    unset($schedule[$instructor][$s['date']][$s['park']][$s['slot']]);//取消已訂課雪場
                }
            }//foreach開課
        }//_j($schedule['ko']);exit();

        //排除團體開課
        // foreach ($availableInstructors as $instructor) {
        //     $sql = "SELECT * FROM `groups` 
        //             WHERE `instructor`='{$instructor}' 
        //             AND DATE(`start`) <= '{$endDate}' 
        //             AND DATE(`end`) >= '{$startDate}'";//_d($sql);
        //     $groups = $this->db->query('SELECT', $sql);//_v($groups);
        //     foreach ($groups as $n => $g) {
        //         $groupStart = strtotime($g['start']);
        //         $groupEnd = strtotime($g['end']);
        //         for($d=$groupStart; $d<=$groupEnd; $d+=24*60*60){//每一天
        //             $day = date('Y-m-d', $d);//_d($day);
        //             unset($schedule[$instructor][$day]);
        //         }//for每一天
        //     }//for每一團
        // }//for每個教練

        //重組日曆
        foreach ($schedule as $instructor => $arr1) {
            foreach ($arr1 as $date => $arr2) {
                foreach ($arr2 as $park => $arr3) {
                    if(!is_array($arr3)) continue;//沒有這堂課
                    foreach ($arr3 as $slot => $info) {
                        if(!isset($parkInfo[$park]['timeslot'][$slot])){//排除雪場沒有2,3,4堂的
                            continue;
                        }
                        if(isset($disabledSlots[$instructor][$date][$slot]) &&
                            $disabledSlots[$instructor][$date][$slot] == 'disabled'){//排除停課
                            continue;
                        }
                        if($instructor=='ko') continue;//ko測試不顯示
                        if($this->isWinterDay($parkInfo[$park]['winter'], $date)===false) continue;
                        $calendar[$date][$slot][$park][$instructor] = $info;
                    }
                }
            }
        }

        //補空的日期
        for($d=$startTS; $d<=$endTS; $d+=24*60*60){//每一天
            $day = date('Y-m-d', $d);
            if(!isset($calendar[$day])){
                $calendar[$day] = [];
            }
        }

        ksort($calendar);//_j($calendar);exit();
        return $calendar;
    }

    public function getSchedulesbkInst($data){//_v($data);
        $day = ((int) date('N', strtotime($data['date']))%7); //_d($day);
        $startTS = strtotime('-'.(7+$day).' days', strtotime($data['date']));
        $endTS = strtotime('+'.(13-$day).' days', strtotime($data['date']));

        if(isset($data['week']) && $data['week']=='n'){ // offset +1 week          
            $startTS = strtotime('+'.(7).' days', strtotime($data['cdate']));
            $endTS   = strtotime('+'.(27).' days', strtotime($data['cdate']));
        }else if(isset($data['week']) && $data['week']=='p'){ // offset -1 week
            $startTS = strtotime('-'.(7).' days', strtotime($data['cdate']));
            $endTS   = strtotime('+'.(13).' days', strtotime($data['cdate']));
        }

        $start = date('Y-m-d', $startTS);
        $end = date('Y-m-d', $endTS);

        //echo $data['cdate'].'星期('.$day.') flag = '.$data['week'].', from: '.$start.'~'.$end;
        $parkInfo = $this->getParkInfo();

        //指定開課
        $sql = "SELECT * FROM `schedules` 
                WHERE `instructor`='{$data['instructor']}' 
                AND DATE(`date`) BETWEEN '{$start}' AND '{$end}' 
                ORDER BY `date` ASC, `slot` ASC";//_d($sql);
        $schedules = $this->db->query('SELECT', $sql);

        $calendar = [];
        foreach ($schedules as $n => $s) {
            if(in_array($s['park'], ['taipei'])) continue;//台北跳過
            //if(in_array($s['park'], ['nozawa','shiga','furano','tomamu','sapporo','hakuba','tsugaike']) && $s['slot']>=2) continue; //一天一堂五小時

            if(!isset($parkInfo[$s['park']]['timeslot'][$s['slot']]) &&
                $s['park']!=''//disable, 雪場會是空的
            ){
                // continue;//排除雪場沒有2,3,4堂的
            }
            //--舊訂單需要呈現. if($parkInfo[$s['park']]['courseHours']==5 && $s['slot']>=2) continue;//一天只有一堂五小時

            $calendar[$s['date']][$s['slot']][$s['park']][$s['instructor']] = [
                'sidx'      => $s['sidx'],
                'expertise' => $s['expertise'],
                'rule'      => '',
                'oidx'      => $s['oidx'],
                'gidx'      => 0,
                'studentNum'=> $s['studentNum'],
            ];
        }//_j($calendar);exit();

        //條件開課
        $sql = "SELECT * FROM `rules` 
                WHERE `matched`=0  
                AND `instructor`='{$data['instructor']}' 
                AND DATE(`start`) <= '{$end}' 
                AND DATE(`end`) >= '{$start}'";//_d($sql);
        $rules = $this->db->query('SELECT', $sql);
        foreach ($rules as $n => $r) {
            $_start = strtotime($r['start']);
            $_end = strtotime($r['end']);
            for($d=$_start;$d<=$_end;$d+=24*60*60){//每一天
                $day = date('Y-m-d', $d);//_d($day);
                //if($data['expertise']==$r['expertise']||$r['expertise']=='both'){
                    for($s=1;$s<=4;$s++){
                        //排除雪場沒有2,3,4堂的
                        if(!isset($parkInfo[$r['park']]['timeslot'][$s])){
                            // continue;
                        }
                        //--舊訂單需要呈現. if($parkInfo[$r['park']]['courseHours']==5 && $s>=2) continue;//一天只有一堂五小時
                        if($d<$startTS||$d>$endTS) continue;
                        $calendar[$day][$s][$r['park']][$r['instructor']] = [
                            'sidx'      => 0,
                            'expertise' => $r['expertise'],
                            'rule'      => ",ri={$r['idx']},rs={$r['start']},re={$r['end']},rc={$r['days']}d{$r['lessons']}c",
                            'oidx'      => 0,
                            'gidx'      => 0,
                            'studentNum'=> 0,
                        ];
                    }//for
                //}//if
            }//for days
        }//foreach rule

        //團體開課
        $sql = "SELECT * FROM `groups` 
                WHERE `instructor`='{$data['instructor']}' 
                AND DATE(`start`) <= '{$end}' 
                AND DATE(`end`) >= '{$start}'";//_d($sql);
        $groups = $this->db->query('SELECT', $sql);
        foreach ($groups as $n => $r) {
            $start = strtotime($r['start']);
            $end = strtotime($r['end']);
            $studentNum = $this->getGroupLessonStudents($r['gidx']);
            for($d=$start;$d<=$end;$d+=24*60*60){//每一天
                $day = date('Y-m-d', $d);//_d($day);
                //if($data['expertise']==$r['expertise']||$r['expertise']=='both'){
                    for($s=9;$s<=9;$s++){
                        //排除雪場沒有2,3,4堂的
                        if(!isset($parkInfo[$r['park']]['timeslot'][$s])){
                            //continue;
                        }
                        if($d<$startTS||$d>$endTS) continue;
                        $lessonDays = ((strtotime($r['end']) - strtotime($r['start']))/(24*60*60))+1;
                        $calendar[$day][$s][$r['park']][$r['instructor']] = [
                            'sidx'      => 0,
                            'expertise' => $r['expertise'],
                            'rule'      => ",ri={$r['gidx']},rs={$r['start']},re={$r['end']},rc={$lessonDays}d0c",
                            'oidx'      => $r['oidx'],
                            'gidx'      => $r['gidx'],
                            'studentNum'=> $studentNum,
                        ];
                    }//for
                //}//if
            }//for days
        }//foreach group


        //補空的日期
        for($d=$startTS;$d<=$endTS;$d+=24*60*60){//每一天
            $day = date('Y-m-d', $d);
            if(!isset($calendar[$day])){
                $calendar[$day] = [];
            }
        }

        ksort($calendar);
        return $calendar;
    }

    public function isWinterDay($winter, $date){
        global $WINTERDAY;
        $res = ($date>=$WINTERDAY[$winter]['start'] && $date<=$WINTERDAY[$winter]['end']);
        //_d("{$winter},{$date},{$WINTERDAY[$winter]['start']},{$WINTERDAY[$winter]['end']}',{$res}'");
        //$res = ($date>='2019-12-01' && $date<='2020-04-15') ? true : $res;
        return $res;
    }

    public function isIn3Days($timestamp){
        if(isset($_SESSION['user_idx']) && in_array($_SESSION['user_idx'],[2,3])){
            return false;
        }
        $_after = time()+(24*2*60*60);
        return ($timestamp < $_after) ? true : false;
    }

    public function isInDays($timestamp, $days){//現在小於timestamp幾天
        if(isset($_SESSION['user_idx']) && in_array($_SESSION['user_idx'],[2,3])){
            return false;
        }
        $_after = time()+(24*$days*60*60);
        return ((time()<$timestamp) && ($timestamp < $_after) ) ? true:false;
        //return ($timestamp < $_after) ? true : false;
    }

    public function isOverDays($timestamp, $days){//現在大於timestamp幾天
        $_after = $timestamp + (24*$days*60*60);
        return (time() >= $_after) ? true : false;
    }

    public function getSchedules($data, &$distinctParks){
        global $AVAIPARKS;
        $day = ((int) date('N', strtotime($data['date']))%7);//_d($day);
        //$startTS = strtotime('-'.(7+$day).' days', strtotime($data['date']));
        //$endTS = strtotime('+'.(13-$day).' days', strtotime($data['date']));
        $startTS = strtotime('-3 days', strtotime($data['date']));
        $endTS = strtotime('+3 days', strtotime($data['date']));
        $start = date('Y-m-d', $startTS);
        $end = date('Y-m-d', $endTS);
        $parkInfo = $this->getParkInfo();
        
        //指定開課
        $sql = "SELECT * FROM `schedules` 
                WHERE (`expertise`='{$data['expertise']}' OR `expertise`='both') 
                AND `oidx` = 0 
                AND DATE(`date`) BETWEEN '{$start}' AND '{$end}'
                ORDER BY `date` ASC, `slot` ASC";//_d($sql);
        $schedules = $this->db->query('SELECT', $sql);

        $calendar = [];
        foreach ($schedules as $n => $s) {
            // if(!in_array($s['park'], $AVAIPARKS)) continue;//2023限雪場
            //if(empty($parkInfo[$s['park']]['winter'])) var_dump($s['park']);
            if($this->isWinterDay($parkInfo[$s['park']]['winter'], $s['date'])===false) continue;//雪季期間
            if($parkInfo[$s['park']]['courseHours']==3 && $s['slot']>=3) continue;//三小時的課一天只有兩堂
            if($this->isIn3Days(strtotime($s['date']))) continue;//管理員可協助預訂三天內的課程
            if($s['instructor']=='virtual') continue;//虛擬教練不顯示(應當不會發生,保險起見)
            if($s['instructor']=='ko' && $_SESSION['user_idx']!==48) continue;//ko測試不顯示
            if($data['park']!='any' && $data['park']!=$s['park'] ){
                continue;//只顯示這雪場的教練
            }
            if(!isset($parkInfo[$s['park']]['timeslot'][$s['slot']])){
                continue;//排除雪場沒有2,3,4堂的
            }
            if(in_array($s['park'],['karuizawa']) && $s['slot']==4 &&
                (strtotime($s['date'])<strtotime('2024-12-23'))){
                continue;//輕井澤 2023/12/22之後才可以開四堂課
            }
            
            $calendar[$s['date']][$s['slot']][$s['park']][$s['instructor']] = [
                'sidx'      => $s['sidx'],
                'expertise' => ($s['expertise']=='both') ? $data['expertise'] : $s['expertise'],
                'rule'      => '',
            ];
            $distinctParks[$s['park']] = $s['park'];
        }//_v($calendar);

        //條件開課
        $sql = "SELECT * FROM `rules` 
                WHERE `matched`=0  
                AND (
                        (`start`>='{$start}' AND `end`<='{$end}')
                        OR (`end`>='{$start}')
                        OR (`start`<='{$end}')
                    )";//_d($sql);
        $rules = $this->db->query('SELECT', $sql);
        foreach ($rules as $n => $r) {
            // if(!in_array($r['park'], $AVAIPARKS)) continue;//2023限雪場
            if($r['instructor']=='ko' && $_SESSION['user_idx']!==48) continue;//ko測試不顯示

            if($data['park']!='any' && ($data['park']!=$r['park']) ){//_d("{$r['instructor']}=> {$data['park']}!={$r['park']}");
                continue;//只顯示這雪場的教練
            }//_v($r);
            $start = strtotime($r['start']);
            $end = strtotime($r['end']);
            for($d=$start;$d<=$end;$d+=24*60*60){//每一天
                $day = date('Y-m-d', $d);//_d($day);
                if($this->isIn3Days(strtotime($day))) continue;
                if($data['expertise']==$r['expertise']||$r['expertise']=='both'){
                    for($s=1;$s<=4;$s++){
                        if($parkInfo[$r['park']]['courseHours']==3 && $s>=3) continue;//三小時的課一天只有兩堂
                        //排除雪場沒有2,3,4堂的
                        if(!isset($parkInfo[$r['park']]['timeslot'][$s])){
                            continue;
                        }
                        if(in_array($r['park'],['karuizawa']) && $s==4 &&
                            (strtotime($day)<strtotime('2024-12-23'))){
                            continue;//輕井澤 2023/12/23之後才可以開四堂課
                        }
                        if($d<$startTS||$d>$endTS) continue;
                        $calendar[$day][$s][$r['park']][$r['instructor']] = [
                            'sidx'      => 0,
                            'expertise' => ($r['expertise']=='both') ? $data['expertise'] : $r['expertise'],
                            'rule'      => ",ri={$r['idx']},rs={$r['start']},re={$r['end']},rc={$r['days']}d{$r['lessons']}c",
                        ];
                        $distinctParks[$r['park']] = $r['park'];
                    }//for
                }//if
            }//for days
        }//foreach rule

        //補空的日期
        for($d=$startTS;$d<=$endTS;$d+=24*60*60){//每一天
            $day = date('Y-m-d', $d);
            if(!isset($calendar[$day])){
                $calendar[$day] = [];
            }
        }

        ksort($calendar);
        return $calendar;
    }


    //可上課雪場
    public function setParks($data, $instructor){
        $notin = '';
        foreach ($data as $n => $p) {
            $notin .= "'{$p}',";
        }
        $notin = substr($notin, 0, -1);

        //刪除指定開課
        $sql = "DELETE FROM `schedules` 
                WHERE `oidx`=0 
                AND `expertise`!='disable' 
                AND `instructor`='{$instructor}' 
                AND `park` 
                NOT IN ({$notin})";//_d($sql);
        $this->db->query('DELETE', $sql);

        //刪除條件開課
        $sql = "DELETE FROM `rules` 
                WHERE `matched`=0 
                AND `instructor`='{$instructor}' 
                AND `park` 
                NOT IN ({$notin})";//_d($sql);
        $this->db->query('DELETE', $sql);

        //刪除團體課程
        $sql = "DELETE FROM `groups` 
                WHERE `oidx`='' 
                AND `instructor`='{$instructor}' 
                AND `park` 
                NOT IN ({$notin})";//_d($sql);
        $this->db->query('DELETE', $sql);

        return $this->db->update('instructorInfo', [
            'parks' => json_encode($data),
        ], [
            'name'  =>  $instructor,
        ]);
    }

    //開課
    public function setFixedLessons($data, $type){
        //先找出該課程
        $where = [
            'instructor'    => $data['instructor'],
            'date'          => $data['date'],
            'slot'          => $data['slot'],
        ];//_v($where);
        $lesson = $this->db->select('schedules', $where);//_v($lesson);

        if(!empty($lesson[0]['oidx'])){//已有訂單,不異動
            return false;
        }

        //若日期有在條件裡, 條件則刪除.
        $sql = "SELECT * FROM `rules` 
                WHERE `matched`=0 
                AND `instructor`='{$data['instructor']}'
                AND DATE(`start`)<='{$data['date']}'
                AND DATE(`end`)>='{$data['date']}'";
        $rules = $this->db->query('SELECT', $sql);
        if(sizeof($rules)>0){
            foreach ($rules as $n => $r) {
                $sql = "DELETE FROM `rules` WHERE `idx`={$r['idx']}";
                $this->db->query('DELETE', $sql);
            }//foreach每個條件
        }//若有重疊

        //若有日期在團體課裡, 就不設定
        // 有團課仍可開課
        // $sql = "SELECT * FROM `groups` 
        //         WHERE `instructor`='{$data['instructor']}'
        //         AND DATE(`start`)<='{$data['date']}'
        //         AND DATE(`end`)>='{$data['date']}'";
        // $groups = $this->db->query('SELECT', $sql);//_v($groups);exit();
        // if(sizeof($groups)>0){
        //     return false;
        // }


        switch ($type) {
            case 'enable'://開課
                if(empty($lesson)){//尚未開課, 新增課程
                    return $this->db->insert('schedules', $data);
                }
                //已開課, 更新課程
                return $this->db->update('schedules', [
                    'park'      =>  $data['park'],
                    'expertise' =>  $data['expertise']
                ], $where);
                break;

            case 'disable'://停課
                $data['park'] = '';
                $data['expertise'] = 'disable';
                if(empty($lesson)){//尚未開課, 新增停課
                    return $this->db->insert('schedules', $data);
                }
                return $this->db->update('schedules', $data, $where);
                break;

            case 'empty'://空堂
                if(!empty($lesson)){//已開課, 刪除課程
                    return $this->db->delete('schedules', $where);
                }
                break;
            default:
                # code...
                break;
        }//swtich

    }

    public function setRuledLessons($data){//_v($data);exit();
        //排除區間有指定開課或停課
        foreach ($data['park'] as $n => $p) {
            $sql = "SELECT `sidx` FROM `schedules`
                    WHERE `instructor`='{$data['instructor']}' 
                    AND DATE(`date`) BETWEEN '{$data['start']}' AND '{$data['end']}'";
            $lessons = $this->db->query('SELECT', $sql);
            if(sizeof($lessons)){//區間內若有課程,不可新增
                return 'setRuleFixedOverlapped';
            }
        }//foreach park

        //排除有重疊的條件開課
        $sql = "SELECT * FROM `rules` 
                    WHERE `matched`=0 
                    AND `instructor`='{$data['instructor']}' 
                    AND (
                            (DATE(`start`) <= '{$data['start']}' AND DATE(`end`) >= '{$data['start']}') 
                            OR
                            (DATE(`start`) <= '{$data['end']}' AND DATE(`end`) >= '{$data['end']}') 
                        )";//_d($sql);exit();
        $rules = $this->db->query('SELECT', $sql);//_v($rules);

        $ruledPark = [];
        if(sizeof($rules)!=0){
            foreach ($rules as $n => $r) {
                if($r['start']!=$data['start']||$r['end']!=$data['end']){//若有區間不同的條件, 不可新增
                    return 'setRuleOverlapped';
                }
                $ruledPark[$r['park']] = $r['park'];//同區間已設定過的條件, 但不同雪場.
            }
        }

        //排除有重疊的團體課
        // 有團課仍可開課
        // $sql = "SELECT * FROM `groups` 
        //             WHERE `instructor`='{$data['instructor']}' 
        //             AND (
        //                     (DATE(`start`) <= '{$data['start']}' AND DATE(`end`) >= '{$data['start']}') 
        //                     OR
        //                     (DATE(`start`) <= '{$data['end']}' AND DATE(`end`) >= '{$data['end']}') 
        //                 )";//_d($sql);exit();
        // $groups = $this->db->query('SELECT', $sql);//_v($rules);
        // if(sizeof($groups)>0){
        //     return 'setRuleGroupOverlapped';
        // }

        //新增的條件
        foreach ($data['park'] as $n => $p) {
            if($p=='all') continue;
            if(!in_array($p, $ruledPark)){//_d("add {$p}");//要不同雪場才能新增
                $in = $data;
                $in['park'] = $p;
                $this->db->insert('rules', $in);
            }//if
        }//foreach

        return 'setRuleSuccess';
    }

    //團體課程
    public function setGroupLessons($data){
        //排除區間有指定開課或停課
        // 有團課仍可開課
        // $sql = "SELECT `sidx` FROM `schedules`
        //         WHERE `instructor`='{$data['instructor']}' 
        //         AND DATE(`date`) BETWEEN '{$data['start']}' AND '{$data['end']}'";
        // $lessons = $this->db->query('SELECT', $sql);//_d($sql);
        // if(sizeof($lessons)){//區間內若有課程,不可新增
        //     return 'setRuleFixedOverlapped';
        // }

        // //排除有重疊的條件開課
        // $sql = "SELECT * FROM `rules` 
        //             WHERE `instructor`='{$data['instructor']}' 
        //             AND `matched` = 0 
        //             AND (
        //                     (DATE(`start`) <= '{$data['start']}' AND DATE(`end`) >= '{$data['start']}') 
        //                     OR
        //                     (DATE(`start`) <= '{$data['end']}' AND DATE(`end`) >= '{$data['end']}') 
        //                 )";_d($sql);//exit();
        // $rules = $this->db->query('SELECT', $sql);//_v($rules);
        // if(sizeof($rules)){//區間內若有課程,不可新增
        //     return 'setRuleOverlapped';
        // }

        //排除有重疊的團體開課
        $sql = "SELECT * FROM `groups` 
                    WHERE `instructor`='{$data['instructor']}' 
                    AND (
                            (DATE(`start`) <= '{$data['start']}' AND DATE(`end`) >= '{$data['start']}') 
                            OR
                            (DATE(`start`) <= '{$data['end']}' AND DATE(`end`) >= '{$data['end']}') 
                        )";_d($sql);//exit();
        $groups = $this->db->query('SELECT', $sql);//_v($rules);
        if(sizeof($groups)){//區間內若有課程,不可新增
            return 'setRuleGroupOverlapped';
        }

        $ok = $this->db->insert('groups', $data);//_d("set={$ok}.");exit();
        return ($ok) ? 'setGroupSuccess' : 'setGroupFail';
    }

    public function getGroupLessons($gidx=null, $open=1){
        if(empty($gidx)){
            $and = empty($open) ? 'AND `open`=0' : 'AND `open`=1';
            $closeDate = date('Y-m-d', strtotime('-3 days'));
            $sql = "SELECT * FROM `groups` WHERE `start`>'{$closeDate}' {$and} ORDER BY `start` ASC";//_d($sql);
            return $this->db->query('SELECT', $sql);
        }else{
            $sql = "SELECT * FROM `groups` WHERE `gidx`={$gidx}";//_d($sql);
            $res = $this->db->query('SELECT', $sql);
            return $res[0];
        }
    }

    public function getGroupLessonStudents($gidx){
        $sql = "SELECT COUNT(`oidx`) AS `studentNum` 
                FROM `orders` 
                WHERE `gidx`={$gidx} 
                AND `status`='success'";
        $res =$this->db->query('SELECT', $sql);//_d($sql);
        return (int)$res[0]['studentNum'];
    }

    public function deleteRuledLessons($where){
        return $this->db->delete('rules', $where);
    }

    public function deleteFixedLessons($where){
        if(empty($where['sidx'])||empty($where['instructor'])){
            return false;
        }
        $sql = "DELETE FROM `schedules` 
                WHERE `sidx`={$where['sidx']} 
                AND `instructor`='{$where['instructor']}'
                AND `oidx`=0";
        return $this->db->delete('schedules', $where);
    }

    public function deleteGroupLessons($where){
        return $this->db->delete('groups', $where);
    }

    public function distinctInstructorName($calendar){
        $distinctInstructors = [];
        foreach ($calendar as $date => $slots) {//_v($slots);
          foreach ($slots as $slot => $parks) {//_v($parks);
            foreach ($parks as $park => $lessons) {//_d($park);
              foreach ($lessons as $instructor => $lesson) {
                // if($instructor=='virtual') continue;
                  $distinctInstructors[$instructor] = $instructor;
              }
            }
          }
        }
        return $distinctInstructors;
    }

    public function distinctParkName($calendar){
        $distinctParks = [];
        foreach ($calendar as $date => $slots) {//_v($slots);
          foreach ($slots as $slot => $parks) {//_v($parks);
            foreach ($parks as $park => $lessons) {//_d($park);
                if($park==='disable') continue;
                if(sizeof($lessons)==0) continue;
                $distinctParks[$park] = $park;
            }
          }
        }//_v($distinctParks);
        return $distinctParks;
    }

    public function distinctGroupParkInstruct($lessons){
        $distinctInfo = ['park'=>[],'instructor'=>[]];
        foreach ($lessons as $n => $lesson) {
            $distinctInfo['park'][$lesson['park']] = $lesson['park'];
            $distinctInfo['instructor'][$lesson['instructor']] = $lesson['instructor'];
        }
        return $distinctInfo;
    }

    public function notify($data){
        return $this->db->insert('notify', $data);
    }

    public function rollbackOrder($oidx, $type){//訂單逾時或交易失敗,使其回復開課狀態
        if(empty($oidx)) return false;

        $o = $this->getOneOrderInfo(['oidx'=>$oidx]);//_v($o);
        $this->db->update('orders', ['status'=>$type], ['oidx'=>$oidx]);//訂單標注逾時

        $orderType = 'fix';//預設
        $ruleId = 0;
        foreach ($o['schedule'] as $s) {
            if($s['reservation']!=0){
                $orderType = 'reservation';
                break;
            }else if($s['rule']!=0){
                $orderType = 'rule';
                $ruleId = $s['rule'];
                break;
            }
        }//foreach 找出訂課種類

        switch ($orderType) {
            case 'fix'://固定開課, 復原
                $this->db->update('schedules', [
                                'oidx'=>0, 'studentNum'=>0, 'fee'=>0,
                                'arranged'=>0, 'rule'=>0, 'reservation'=>0,
                                'noshow'=>0,
                            ], [
                                'oidx'=>$oidx
                ]);
                break;
            case 'rule'://條件&預約開課
                if($ruleId){//刪除原來條件
                    $this->db->delete('rules',[
                        'idx'   => $ruleId,
                    ]);
                }
                $this->db->delete('schedules', [
                                'oidx'=>$oidx
                ]);
                break;
            case 'reservation'://刪除開課
                $this->db->delete('schedules', [
                                'oidx'=>$oidx
                ]);
                break;
            default:
                # code...
                break;
        }//switch orderType

        $this->log([
            'severity'  =>  $type,
            'user'      =>  'rollbackOrder',
            'oidx'      =>  $oidx,
            'resp'      =>  $orderType,
            'msg'       =>  json_encode($o, JSON_UNESCAPED_UNICODE),
        ]);
        return true;
    }

    public function getOrdersGroup($filter){
        $where = '';
        if ($filter['year']!=='all') {
            $where .= "YEAR(`start`)='{$filter['year']}' AND ";
        }
        if ($filter['month']!=='all') {
            $where .= "Month(`start`)='{$filter['month']}' AND ";
        }
        if ($filter['park']!=='all') {
            $where .= "`park`='{$filter['park']}' AND ";
        }
        if ($filter['instructor']!=='all') {
            $where .= "`instructor`='{$filter['instructor']}' AND ";
        }
        $where = empty($where) ? null : ' WHERE '.substr($where, 0, -5);

        $sql = "SELECT * FROM `groups` {$where} ORDER BY `start` ASC";//_d($sql);
        $res = $this->db->query('SELECT', $sql);//_v($res);exit();

        $orders = [];
        foreach ($res as $lesson) {
            $sql = "SELECT * FROM `orders` WHERE `gidx`={$lesson['gidx']} AND `status` IN ('success','canceled')";
            $order = $this->db->query('SELECT', $sql);
            $orders[] = [
                'lesson' => $lesson,
                'orders' => $order,
            ];
        }
        return $orders;
    }

    public function getGroupOrders($filter){
        $where = '';
        if ($filter['year']!=='all') {
            $where .= "YEAR(`start`)='{$filter['year']}' AND ";
        }
        if ($filter['month']!=='all') {
            $where .= "Month(`start`)='{$filter['month']}' AND ";
        }
        //篩選保險狀態
        if (isset($filter['insurance']) && $filter['insurance']!=='all') {
            $where .= "`insuranceChecked`='{$filter['insurance']}' AND ";
        }

        $where = empty($where) ? "`status`='success'" : substr($where, 0, -5)." AND `status`='success'";

        $sql = "SELECT o.`oidx`,o.`insuranceMemo`, o.`insuranceChecked`, g.`start`, g.`end`, g.`title`, m.`email` FROM `orders` AS `o` 
                LEFT JOIN `groups` AS `g` ON o.`gidx`=g.`gidx` 
                LEFT JOIN `members_v2` AS `m` ON o.`student`=m.`idx`
                WHERE {$where} ORDER BY g.`start`";//_d($sql);

        $res = $this->db->query('SELECT', $sql);//_v($res);
        return $res;
    }

    public function getOrders($filter){//_v($filter);
        //查已取消訂單
        if ($filter['status']===Payment::PAYMENT_CANCELED||
            $filter['status']===Payment::PAYMENT_CANCEL||
            $filter['status']===Payment::PAYMENT_CANCELING||
            $filter['status']===Payment::PAYMENT_TIMEOUT||
            $filter['status']===Payment::PAYMENT_FAILURE||
            $filter['status']===Payment::PAYMENT_NOSHOW||
            $filter['status']===Payment::PAYMENT_REFUND) {
            $sql = "SELECT * FROM `orders` WHERE `status`='{$filter['status']}'";
            $res = $this->db->query('SELECT', $sql);
            $orders = [];
            foreach ($res as $n => $r) { 
                // 解開Backup TODO
                // $backup = json_decode($r['backup'], true);
                // if(empty($backup['lessons'])) continue;//沒備份到
                // //檢查教練
                // foreach ($backup['lessons'] as $n => $s) {
                // }
                $orders[$r['oidx']] = $r;
            }
            return $orders;
        }

        //先查排課
        $where = '';
        if ($filter['year']!=='all') {
            $where .= "YEAR(`date`)='{$filter['year']}' AND ";
        }
        if ($filter['month']!=='all') {
            $where .= "Month(`date`)='{$filter['month']}' AND ";
        }
        if ($filter['park']!=='all') {
            $where .= "`park`='{$filter['park']}' AND ";
        }
        if ($filter['instructor']!=='all') {
            $where .= "`instructor`='{$filter['instructor']}' AND ";
        }

        $where = empty($where) ? null : ' AND '.substr($where, 0, -5);

        $sql = "SELECT `instructor`, `park`, Min(`date`) AS `date`, `oidx`  
                FROM `schedules` 
                WHERE `oidx`!=0 {$where} 
                GROUP BY `oidx` 
                ORDER BY Min(`date`) ASC";//_d($sql);

        $res = $this->db->query('SELECT', $sql);//_v($res);

        if( empty($res) ){//查無資料
            return [];
        }

        //串訂單號碼
        $orders = [];
        $oidxs = $combo_oidx = '';
        foreach ($res as $n => $r) {
            if(!empty($r['oidx'])){
                $orders[$r['oidx']] = $r;
                $oidxs .= "{$r['oidx']},";
            }
        }
        //ksort($orders);SpiderMan::p($orders);
        $oidxs = substr($oidxs, 0, -1);

        //再查訂單
        $where = '';
        if ($filter['status']!=='all') {
            $where .= "`status`='{$filter['status']}' AND ";
        }
        //篩選保險狀態
        if (isset($filter['insurance']) && $filter['insurance']!=='all') {
            $where .= "`insuranceChecked`='{$filter['insurance']}' AND ";
        }

        $where = empty($where) ? null : ' AND '.substr($where, 0, -5);

        $sql = "SELECT * FROM `orders` WHERE `oidx` IN ({$oidxs}) {$where}";//_d($sql);
        $res = $this->db->query('SELECT', $sql);//_v($res);

        foreach ($res as $n => $r) {
            $orders[$r['oidx']] = array_merge($orders[$r['oidx']], $r);
        }

        foreach ($orders as $oidx => $r) {
            if ( empty($r['status']) ){//移除訂單狀況不符的
                unset($orders[$oidx]);
            }
        }

        //_v($orders);
        return $orders;
    }

    public function getLastAcception($oidx, $instructor){
        $sql = "SELECT * FROM `acceptions` 
                WHERE `oidx`={$oidx} 
                AND `instructor`='{$instructor}'
                ORDER BY `idx` DESC LIMIT 1";
       return $this->db->query('SELECT', $sql);
    }
    public function getAcception($where){
        return $this->db->select('acceptions', $where);
    }
    public function addAcception($data){
        return $this->db->insert('acceptions', $data);
    }
    public function setAcception($data, $where){
        return $this->db->update('acceptions', $data, $where);
    }
    public function acceptionHistory($oidx, &$str){
        $strDef = ['wait'=>'等待中', 'true'=>'已接受', 'pending'=>'已拒絕'];
        $acceptions = $this->getAcception(['oidx'=>$oidx]);
        $str = '';
        $lastAcception = false;
        if(sizeof($acceptions)){
            foreach ($acceptions as $a) {
                $str.=ucfirst($a['instructor']).":{$strDef[$a['accepted']]}.";
                $lastAcception = ($a['accepted']==='true') ? true : false;
            }
        }
        $str = empty($str) ? '舊系統預約' : $str;
        return $lastAcception;
    }

    public function isInWeekOrder($firstDate){
        if(empty($firstDate)) return false;
        return ((strtotime($firstDate)-time()) < 7*24*60*60);
    }

    public function addFollow($data){
        return $this->db->insert('follow', $data);
    }

    public function getFollow($where){
        unset($where['createDateTime']);
        return $this->db->select('follow', $where);
    }
}

class follow{
    public $db = null;

    public function __construct(){
        $this->db = new DB();
    }

    public function getList($student){
        $sql = "SELECT * FROM `follow` WHERE `student`={$student} AND `deleted`=0 AND `date` > CURDATE() ORDER BY `date` ASC";
        return $this->db->query('SELECT', $sql);
    }

    public function delete($student, $idx){
        $sql = "UPDATE `follow` SET `deleted`=1 WHERE `student`={$student} AND `idx`={$idx}";
        return $this->db->query('UPDATE', $sql);
    }
}