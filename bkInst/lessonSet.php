<?php
require('../includes/auth.php');
require('../includes/sdk.php');

$in = $_POST;//_v($in);exit();
$action = $in['action'];
unset($in['action']);

$in['instructor'] = $_SESSION['SKIDIY']['instructor'];

$ko = new ko();
$error = '';

switch($action){
    //開課雪場
    case 'park':
        $ok = $ko->setParks($in['park'], $in['instructor']);
        Header("Location: lessons.php");
        exit();
        break;

    //指定開課
    case 'fixed':
        if(empty($in['slot'])){
            Header("Location: lessons.php?msg=lessonSetNoSlot");
            exit();
        }
        $start = strtotime($in['start']);
        $end = strtotime($in['end']);
        for($d=$start;$d<=$end;$d+=24*60*60){//每一天
            $day = date('Y-m-d', $d);//_d($day);
            foreach ($in['slot'] as $n => $slot) {//每一堂
                $lesson = [
                    'instructor'    => $in['instructor'],
                    'date'          => $day,
                    'slot'          => $slot,
                    'park'          => $in['park'],
                    'expertise'     => $in['expertise'],
                    'createDateTime'=> date('Y-m-d H:i:s'),
                ];
                $ok = $ko->setFixedLessons($lesson, $in['type']);
            }//每一堂
        }//每一天
        break;

    //條件開課
    case 'rule':
        $ok = $ko->setRuledLessons($in);//_d($ok);
        if($ok==='setRuleSuccess'){
            Header("Location: lessons.php?msg={$ok}");
        }else{
            Header("Location: lessons.php?msg={$ok}");
        }
        exit();
        break;

    //團體開課
    case 'group':
    //_v($in);exit();
        //TODO: 刪除重複
        $in['createDateTime'] = date('Y-m-d H:i:s');
        $ok = $ko->setGroupLessons($in);
        if($ok==='setGroupSuccess'){
            Header("Location: lessons.php?msg={$ok}");
        }else{
            Header("Location: lessons.php?msg={$ok}");
        }
        exit();
        break;

    //刪除課程
    case 'delete':
        switch ($in['type']) {
            case 'rule':
                $ok = $ko->deleteRuledLessons([
                    'instructor'    => $in['instructor'],
                    'idx'           => $in['idx'],
                ]);
                break;
            case 'fixed':
                $ok = $ko->deleteFixedLessons([
                    'instructor'    => $in['instructor'],
                    'sidx'          => $in['idx'],
                ]);
                break;
            case 'group':
                $ok = $ko->deleteGroupLessons([
                    'instructor'    => $in['instructor'],
                    'gidx'          => $in['idx'],
                ]);
                break;
            case 'disable':
                $ok = $ko->deleteFixedLessons([
                    'instructor'    => $in['instructor'],
                    'sidx'          => $in['idx'],
                ]);
                break;
            default:
                # code...
                break;
        }
        //Header("Location: lessons.php?msg={$ok}&date={$in['date']}");
        Header("Location: schedules.php?date={$in['date']}");
        exit();
        break;
}

Header("Location: lessons.php?msg={$ok}");
