<?php
require('../includes/sdk.php'); 


//_d($_GET['cmd']);
switch ($_REQUEST['cmd']) {	
	case 'inst_update': // 後台 教練編輯
		$INSTRUCTORS = new INSTRUCTORS();
		$SECTION_HEADER = array(
	      'about'  => '自我介紹',
	      'photo' => '教練照片',
	      'certificate'  => '滑雪證照',
	      'remind'  => '選課注意事項',
	      'cloth' => '教練本季辨識服裝',    
	    );
		if($_POST['about']=='' ){
			echo NULL_INPUT;
			//break;
		}else{		
			  foreach($SECTION_HEADER as $key => $val){
			  	//echo 'key:'.$key.'\r\n';
			  	$update_data['content']   = $_REQUEST[$key];
			  	//var_dump($update_data);
			  	$INSTRUCTORS->update($_REQUEST['qname'],$key,$update_data); 	
			  }	
			  echo MODIFY_OK;
		}	
		break;
	case 'park_update': // 後台 雪場編輯
		$PARKS = new PARKS();
		if($_POST['about']=='' ){
			echo NULL_INPUT;
			//break;
		}else{		
			  foreach($PARK_SECTION_HEADER as $key => $val){
			  	// echo 'key:'.$key.'\r\n';
			  	if($key != "all"){
				  	$update_data['content']   = $_REQUEST[$key];
				  	// var_dump($update_data);
				  	$PARKS->update($_REQUEST['qname'],$key,$update_data); 	
			  	}
			  }	
			  echo MODIFY_OK;
		}	
		break;	
	case 'article_update': // 後台 文章編輯
		$ARTICLE = new ARTICLE();
		if($_POST['content']=='' ){
			echo NULL_INPUT;
			//break;
		}else{		
			$update_data['article']		= $_REQUEST['content'];
			$update_data['title']		= $_REQUEST['title'];
			$update_data['keyword']   	= $_REQUEST['keyword'];
			$update_data['tags']   		= $_REQUEST['tags'];			
			$ARTICLE->update($_REQUEST['qidx'],$update_data); 
			echo MODIFY_OK;
		}	
		break;		
	case 'article_add': // 後台 文章編輯
		$ARTICLE = new ARTICLE();
		if($_POST['new_title']=='' ){
			echo NULL_INPUT;
		}else{		
			$add_data['title']   	= $_REQUEST['new_title'];
			$add_data['article']   	= $_REQUEST['new_content'];			
			$add_data['keyword']   	= $_REQUEST['new_keyword'];
			$add_data['tags']   	= $_REQUEST['new_tags'];
			$ARTICLE->add($add_data); 
			echo MODIFY_OK;
		}	
		break;				
	default:
		# code...
		break;
}

?>