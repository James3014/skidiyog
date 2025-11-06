<?php
require('includes/sdk.php');
$_POST['instructors'] = json_encode($_POST['instructors']);
$ko = new ko();
switch ($_REQUEST['action']) {
	case 'create':
		$_POST['student'] = $_SESSION['user_idx'];
		$_POST['createDateTime'] = date('Y-m-d h:i:s');
		$idx = $ko->getFollow($_POST);
		if($idx){
			echo json_encode([
				'success'=> true,
				'idx'=> $idx,
				'message'=> '此追蹤條件已設定過。',
			], JSON_UNESCAPED_UNICODE);
		}else{
			$idx = $ko->addFollow($_POST);
			if(!empty($idx)){
				echo json_encode([
					'success'=> true,
					'idx'=> $idx,
					'message'=> '追蹤設定完成。',
				], JSON_UNESCAPED_UNICODE);
			}else{
				echo json_encode([
					'success'=> false,
					'message'=> '追蹤設定失敗。',
				], JSON_UNESCAPED_UNICODE);
			}
		}
		break;
	default:
		# code...
		break;
}
?>