<?php
class Payment
{
    const PAYMENT_CREATED   = 'create';
    const PAYMENT_SUCCESS   = 'success';
    const PAYMENT_TIMEOUT   = 'timeout';
    const PAYMENT_CANCELING = 'canceling';
    const PAYMENT_CANCEL    = 'cancel';
    const PAYMENT_CANCELED  = 'canceled';
    const PAYMENT_FAILURE   = 'fail';
    const PAYMENT_REFUND    = 'refund';
    const PAYMENT_NOSHOW    = 'noshow';

    const STATUS_NAME = [
        self::PAYMENT_CREATED   => '🔄 付款交易中',
        self::PAYMENT_SUCCESS   => '✅ 交易成功',
        self::PAYMENT_TIMEOUT   => '🕟 交易逾時',
        self::PAYMENT_CANCELING => '🚥 訂單取消確認中',
        self::PAYMENT_CANCEL    => '🚦 訂單取消中..',
        self::PAYMENT_CANCELED  => '🚫 訂單已取消',
        self::PAYMENT_FAILURE   => '❌ 交易失敗',
        self::PAYMENT_REFUND    => '🔄 已刷退',
        self::PAYMENT_NOSHOW    => '😡 No Show',
    ];

    const ECPAY_SERVICE_URL     = 'https://payment.ecpay.com.tw/Cashier/AioCheckOut';
    const ECPAY_CALLBACK_URL    = 'https://diy.ski';
    const ECPay_OrderResultURL  = self::ECPAY_CALLBACK_URL . "/ecpay/OrderResultURL.php";//付款結束後導回的頁面
    const ECPay_ReturnURL       = self::ECPAY_CALLBACK_URL . "/ecpay/ReturnURL.php";//回傳信用卡交易結果

    const ADMIN_EMAIL           = 'admin@diy.ski';
    //const ADMIN_EMAIL           = 'ericko@inn-com.tw';

    private $ECPayAccount = [
        /*'A' =>[//Jeter I
            'MerchantID'=>  '3004045',
            'HashKey'   =>  'UWJLgbBRMeSBehhj',
            'HashIV'    =>  'RWzZYWKOH56G9TXs',
        ],*/
        'B' =>[//Jeter II
            'MerchantID'=>  '1138289',
            'HashKey'   =>  'KzUP20jByorCF1VW',
            'HashIV'    =>  '43KKbxab9pyYVWnP',
        ],
        /*'C' =>[//James I
            'MerchantID'=>  '3002890',
            'HashKey'   =>  'pXkPSxThh53eHmbI',
            'HashIV'    =>  '9zaEYGTZOklGMWZ2',
        ],
        'D' =>[//James II
            'MerchantID'=>  '3001846',
            'HashKey'   =>  'mKRAV1V6PrvJcKwv',
            'HashIV'    =>  'KvMAcsHP0tQ09E3C',
        ],
        'E' =>[//Jeter III
            'MerchantID'=>  '3034178',
            'HashKey'   =>  'kIUWd7NufdXWDS3N',
            'HashIV'    =>  '5WxWLWsK92N6K0Hr',
        ],*/
    ];

    public function __construct()
    {
        $this->db = new db();
    }

    public function readOrder($oidx)
    {
        $info['info'] = $this->db->select('orders', ['oidx'=>$oidx]);
        $sql = "SELECT * FROM `schedules` WHERE `oidx`={$oidx} OR `combo_oidx`={$oidx}";
        $info['schedules'] = $this->db->query('SELECT', $sql);
        return $info;
    }

    public function createLog($data)
    {
        $this->db->insert('log', [
            'severity'  => 'ecPay',
            'msg'       => json_encode($data),
            'resp'      => empty($data['resp']) ? '-' : $data['resp'],
            'ip'        => $_SERVER['REMOTE_ADDR']
        ]);
    }

    public function readOrderByNo($orderNo)
    {
        $order = $this->db->select('orders', ['orderNo'=>$orderNo]);
        return isset($order[0]) ? $order[0] : null;
    }

    public function getECPayAccount($id)
    {
        return $this->ECPayAccount[$id];
    }

    //Callback from ECPay
    public function updateOrder($data, $where)
    {
        return $this->db->update('orders', $data, $where);
    }

    //付款結果通知
    public function getOrderInfoForNotify($orderNo)
    {
        $order = $this->db->select('orders', ['orderNo'=>$orderNo]);
        $sql = "SELECT * FROM `schedules` WHERE `oidx`={$order[0]['oidx']} OR `combo_oidx`={$order[0]['oidx']}";
        $schedules = $this->db->query('SELECT', $sql);

        $sql = "SELECT * FROM `members` WHERE `idx`={$order[0]['student']}";
        $student = $this->db->query('SELECT', $sql);

        //讀教練, 雪場, 日期, 人次
        $studentNum = 0;
        $virtual = false;
        foreach ($schedules as $n => $s) {
            if ($s['instructor']==='virtual'){
                $virtual = true;
            }
            $instructor[$s['instructor']] = $s['instructor'];
            $park[$s['park']] = $s['park'];
            $date[$s['date']] = $s['date'];
            $expertise[$s['expertise']] = $s['expertise'];
            $studentNum += $s['studentNum'] + $s['combo_studentNum'];
        }
        ksort($date);
        
        $wherein = "('". implode("','", $instructor) . "')";
        $sql = "SELECT `name`, `email` FROM `members` WHERE `type`='instructor' AND `name` IN {$wherein}";
        $instructor = $this->db->query('SELECT', $sql);
        foreach ($instructor as $n => $r) {
            $instructorName[] = $r['name'];
            $instructorEmail[] = $r['email'];
        }

        $info = [
            'oidx'          =>  $order[0]['oidx'],
            'student'       =>  $student[0],
            'instructorName'=>  empty($instructorName) ? '' : implode(',', $instructorName),
            'instructorEmail'=> empty($instructorEmail) ? '' : implode(',', $instructorEmail),
            'park'          =>  implode(',', $park),
            'date'          =>  implode(',', $date),
            'expertise'     =>  implode(',', $expertise),
            'studentNum'    =>  $studentNum,
            'lessons'       =>  sizeof($schedules),
            'orderNo'       =>  $order[0]['orderNo'],
            'price'         =>  $order[0]['price'],
            'discount'      =>  $order[0]['discount'],
            'specialDiscount'=> $order[0]['specialDiscount'],
            'prepaid'       =>  $order[0]['prepaid'],

            'exchangeRate'  =>  $order[0]['exchangeRate'],
            'currency'      =>  $order[0]['currency'],
            'paid'          =>  $order[0]['paid'],
            'payment'       =>  $order[0]['payment'],
            'virtual'       =>  $virtual,
            'requirement'   =>  $order[0]['requirement'],
            'note'          =>  $order[0]['note'],
            'memo'          =>  $order[0]['memo'],
        ];

        return $info;
    }

    //訂單逾時需清除訂課
    public function updateOrderTimeout(){//crontab
        $now = date('Y-m-d H:i:s', strtotime('-20 minutes'));
        $sql = "SELECT * FROM `orders` WHERE `status`='".self::PAYMENT_CREATED."' AND `allpay_PaymentType`='' AND `createDateTime`<'{$now}'";
        //SpiderMan::p($sql);exit();
        $orders = $this->db->query('SELECT', $sql);
        foreach ($orders as $n => $o) {
            $sch = $this->db->select('schedules', ['oidx'=>$o['oidx']]);
            if( isset($sch[0]['oidx']) && $sch[0]['oidx']==$o['oidx'] ){//開課
                $ok = $this->db->update('schedules', [
                        'oidx'              => 0,
                        'studentNum'        => 0,
                        'fee'               => 0,
                    ],[
                        'oidx'              => $o['oidx']
                ]);
            }else{//湊班
                $ok = $this->db->update('schedules', [
                        'combo_oidx'        => 0,
                        'combo_studentNum'  => 0,
                        'combo_fee'         => 0,
                    ],[
                        'combo_oidx'        => $o['oidx']
                ]);
            }
            $ok = $this->db->update('orders',[
                    'status'=>self::PAYMENT_TIMEOUT
                ],[
                    'oidx'              => $o['oidx']
            ]);
            echo ($ok) ? "oidx:{$o['oidx']} timeout successful.\n" : "oidx:{$o['oidx']} timeout process fail!\n";
        }
    }

    //交易失敗需清除訂課
    public function updateOrderFail($oidx){//ecpay_ReturnURL
        $sch = $this->db->select('schedules', ['oidx'=>$oidx]);
        if( isset($sch[0]['oidx']) && $sch[0]['oidx']==$oidx ){//開課
            $ok = $this->db->update('schedules', [
                        'oidx'              => 0,
                        'studentNum'        => 0,
                        'fee'               => 0,
                    ],[
                        'oidx'              => $oidx
            ]);
        }else{//湊班
            $ok = $this->db->update('schedules', [
                        'combo_oidx'        => 0,
                        'combo_studentNum'  => 0,
                        'combo_fee'         => 0,
                    ],[
                        'combo_oidx'        => $oidx
            ]);
        }
        $ok = $this->db->update('orders',[
                'status'=>self::PAYMENT_FAILURE
            ],[
                'oidx'              => $oidx
        ]);
    }

}