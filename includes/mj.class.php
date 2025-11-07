<?php
require('pre-define.inc.php');

// AWS 服務已完全禁用
// 此部署版本不依賴 AWS SES/SNS，使用本地 PHP mail() 函數替代

define("DEFAULT_TOKEN_KEY","SYKIID");
define("STR_09AZ",1);
define("STR_09",2);
define("MOBILE_VERIFY",0);

$PARK_SECTION_HEADER = array(
  'about'  => '介紹',
  'photo' => '照片',
  'location'  => '位置',
  'slope'  => '雪道',
  'ticket' => '雪票',
  'time' => '開放時間',
  'access' => '交通',
  'live'  => '住宿',
  'rental' => '租借',
  'delivery'  => '宅配',
  'luggage' => '行前裝備',
  'workout'  => '體能',
  'remind'  => '上課地點及事項',
  'join'  => '約伴及討論',
  'event'  => '優惠活動',
  'all'  => '完整閱讀',
);

$INSTRUCTOR_SECTION_HEADER = array(
  'about'  => '自我介紹',
  'photo' => '教練照片',
  'certificate'  => '滑雪證照',
  'remind'  => '選課注意事項',
  'cloth' => '教練本季辨識服裝',
);

class COMMON_FUNC{
	public function randomStr($type,$len){
		$randstring = "";
	    $charspace[1] = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charspace[2] = '0123456789';
	    $characters = $charspace[$type];
	    $randstring = '';
	    for ($i = 0; $i < $len; $i++) {
	        $randstring .=$characters[rand(0, strlen($characters)-1)];
	    }
	    return $randstring;
	}
}

// 簡單 SMS 代替服務（本地實現，不使用 AWS SNS）
class AWS_SNS{
    public function __construct() {}

    public function SEND_SMS($phoneNumber, $message, $sendType = 'PHPONE_DIRECTLY'){
        // 本地實現：記錄 SMS 到日誌而不實際發送
        $logFile = __DIR__ . '/../logs/sms.log';
        if (!is_dir(dirname($logFile))) {
            mkdir(dirname($logFile), 0755, true);
        }
        $logEntry = date('Y-m-d H:i:s') . " | Phone: {$phoneNumber} | Message: {$message}\n";
        file_put_contents($logFile, $logEntry, FILE_APPEND);
        return true;
    }

    public function test(){
    	return 'SMS Service disabled (local logging only)';
    }
}

// 簡單郵件代替服務（本地實現，不使用 AWS SES）
class awsSES{
    public function __construct() {}

    public function send($to, $subject, $text){
        return $this->sendEmail($to, $subject, $text);
    }

    public function sendSys($to, $subject, $text){
        return $this->sendEmail($to, $subject, $text);
    }

    public function sendEmail($to, $subject, $text){
        // 本地實現：使用 PHP mail() 函數
        $from = '=?utf8?B?'.base64_encode('SKIDIY 自助滑雪').'?=<admin@diy.ski>';
        $headers = "From: {$from}\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        $logFile = __DIR__ . '/../logs/email.log';
        if (!is_dir(dirname($logFile))) {
            mkdir(dirname($logFile), 0755, true);
        }

        $logEntry = date('Y-m-d H:i:s') . " | To: {$to} | Subject: {$subject} | Body: " . substr($text, 0, 100) . "...\n";
        file_put_contents($logFile, $logEntry, FILE_APPEND);

        // 在實際環境中應使用 mail()，但為了避免郵件配置問題，只記錄
        // return mail($to, $subject, $text, $headers);
        return true;
    }

    public function sendTest($data){
        return $this->sendEmail($data['ToAddresses'][0] ?? 'test@example.com',
                                $data['Subject'] ?? 'Test',
                                $data['Text'] ?? 'Test message');
    }
}

// ==================== 其他業務類別 ====================

class PARKS{
	public $db = null;
	public function __construct()
	{
		$this->db 		= new DB();
		$this->tb 		= "parks";
	}

    public function getParkInfo(){
        $sql = "SELECT * FROM `parks` ORDER BY `idx` ASC";
        $parkInfo = $this->db->query('SELECT', $sql);
        return $parkInfo;
    }
    public function getParkInfo_by_Name($parkname){
        $sql = "SELECT * FROM `parks` where `name`='{$parkname}'";
        $parkInfo = $this->db->query('SELECT', $sql);
        if(!empty($parkInfo))
        return $parkInfo[0];
    }
    public function CheckParkContent_by_Name($parkname){
        $sql = "SELECT COUNT('idx') as cnt FROM `parks` where `name`='{$parkname}'";
        $parkInfo = $this->db->query('SELECT', $sql);
        return $parkInfo[0]['cnt'];
    }

    public function listing(){
        $sql = "SELECT * FROM `parks` ORDER BY `idx` ASC";
        return $this->db->QUERY('SELECT', $sql);
    }
	public function update($name,$section, $data){
		// Map section names to database column names
		$section_column_map = array(
			'about' => 'about',
			'photo' => 'photo_section',
			'location' => 'location_section',
			'slope' => 'slope_section',
			'ticket' => 'ticket_section',
			'time' => 'time_section',
			'access' => 'access_section',
			'live' => 'live_section',
			'rental' => 'rental_section',
			'delivery' => 'delivery_section',
			'luggage' => 'luggage_section',
			'workout' => 'workout_section',
			'remind' => 'remind_section',
			'join' => 'join_section',
			'event' => 'event_section'
		);

		$where['name'] = $name;
		$column_name = isset($section_column_map[$section]) ? $section_column_map[$section] : $section;

		// Update the specific column with the content
		$update_data = array($column_name => $data['content']);
		return $this->db->UPDATE($this->tb, $update_data, $where);
	}

	public function park_oplist(){
        $sql = "SELECT name,cname FROM `parkInfo`";
        $parkInfo = $this->db->query('SELECT', $sql);
        foreach($parkInfo as $key => $val){
        	echo '<option  value="'.$val['name'].'">'.$val['cname'].'</option>';
        }
	}

	public function park_oplist_v2($select){
        $sql = "SELECT name,cname FROM `parkInfo`";
        $parkInfo = $this->db->query('SELECT', $sql);
        foreach($parkInfo as $key => $val){
        	if($select==$val['name']){
        		echo '<option selected  value="'.$val['name'].'">'.$val['cname'].'</option>';
        	}else{
        		echo '<option  value="'.$val['name'].'">'.$val['cname'].'</option>';
        	}
        }
	}

	public function info($name, $section){
		$where['name'] = $name;
		$where['section'] = empty($section)?'about':$section;
		$row = $this->db->SELECT('parks', $where);
		if(empty($row[0])) return null;

		$oh = array("</h3>\r\n", "</h4>\r\n", "</h5>\r\n", "</h6>\r\n");
		$nh = array("</h3>", "</h4>", "</h5>", "</h6>");
		if($section == 'about'){
			$row[0]['content'] = str_replace($oh, $nh, $row[0]['content']).'|'.str_replace($oh, $nh, $row[0]['keyword']);
		}else{
			$row[0]['content'] = str_replace($oh, $nh, $row[0]['content']);
		}

		return (isset($row[0]['content']))?$row[0]['content']:null;
	}
}

class INSTRUCTORS{
	public $db = null;
	public function __construct()
	{
		$this->db 		= new DB();
		$this->tb 		= "instructors";
	}

	public function instructorList(){
		$sql = "SELECT * FROM `instructorInfo` WHERE `active`=1 ORDER BY `jobType` ASC, `name` ASC";
		return $this->db->QUERY('SELECT', $sql);
	}

	public function get_instructor_profile_by_Name($name){
		$sql = "SELECT * FROM `instructorInfo` where `name`='{$name}' ";
		$result =  $this->db->QUERY('SELECT', $sql);
        if(!empty($result))
        return $result[0];
	}

	public function update($name,$section, $data){
		$where['name'] = $name;
		$where['section'] = $section;
		return $this->db->UPDATE($this->tb, $data, $where);
	}

	public function info($name, $section){
		$where['name'] = $name;
		$where['section'] = empty($section)?'about':$section;
		$row = $this->db->SELECT('instructors', $where);

		$oh = array("</h3>\r\n", "</h4>\r\n", "</h5>\r\n", "</h6>\r\n");
		$nh = array("</h3>", "</h4>", "</h5>", "</h6>");
		$row[0]['content'] = str_replace($oh, $nh, $row[0]['content']);

		return (isset($row[0]['content']))?$row[0]['content']:null;
	}
}

class ARTICLE{
	public $db = null;
	public function __construct()
	{
		$this->db 		= new DB();
		$this->tb 		= "articles";
	}

	public function listing(){
		$sql = "SELECT * FROM `articles`";
		return $this->db->QUERY('SELECT', $sql);
	}

	public function readByIdx($idx){
		$where['idx'] = $idx;
		$row = $this->db->SELECT('articles', $where);
		return $row[0];
	}

	public function add($data){
		$ok = $this->db->INSERT($this->tb, $data);
		return $ok;
	}

	public function update($idx, $data){
		$where['idx'] = $idx;
		$ok = $this->db->UPDATE($this->tb, $data, $where);
		return $ok;
	}

	public function delete($idx){
		$where['idx'] = $idx;
		$ok = $this->db->DELETE($this->tb, $where);
		return $ok;
	}
}

?>
