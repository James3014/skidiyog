<?php
	// xx : x : xxx
	// type:fail|success:no
	// 類型：帳號:10 , 保險：11
	// 錯誤：0 成功：1
	// 失敗
	define("REG_DUP",		100001);
	define("REG_FAIL",		100002);
	define("LOGIN_FAIL",	100003);
	define("DIFF_PW_IN",	100004);
	define("PWRSET_TK_ERR",	100005);
	define("NULL_INPUT",	100006);
	define("NULL_ACCOUNT",	100007);
	define("MOBILE_CHECK_FAIL"	,100008);
	define("SECURITY_FAIL"		,100009);
	define("ACCOUNT_MODIFY_FAIL"				,100010);

	// 成功
	define("REG_OK",		101001);
	define("LOGIN_OK",		101002);
	define("PWDUPDATE_OK",	101003);
	define("PWRESET_REQ",	101004);
	define("MAIL_CHECK_OK",	101005);
	define("MOBILE_CHECK_OK",101006);
	define("TWO_FA_AUTH_DONE",101007);
	define("ACCOUNT_MODIFY_OK"				,101008);
	define("MODIFY_OK"						,101009); // 存檔成功



	// 保險 失敗
	define("NULL_INFO",		111001);	// 資料不齊
	define("ERR_PASSPORT",	111002);	// 護照格式錯誤
	define("ERR_EMAIL",		111003);	// 信箱格式錯誤
	define("ERR_ADDR",		111004);	// 地址格式錯誤
	define("ERR_PHONE",		111005);	// 電話格式錯誤
	define("ERR_TWID",		111006);	// 身分證格式錯誤
	define("ERR_BIRTH",		111007);	// 生日格式錯誤
	define("ERR_PASSPORT_N",111008);	// 護照名稱錯誤
	define("ERR_NULL_OID",	111009);	// 訂單編號不正確
	define("ERR_INFO_DUP",	111010);	// 保單資料重複
	define("ERR_CHINESE_N",	111011);	// 中文名稱格式錯誤
	define("ERR_NULL_COU",	111012);	// 國籍未填寫
	define("ERR_NULL_DA_DATE",		111013);	// 出發或回國日期 未填寫
	define("ERR_NULL_DAY1_DATE",	111014);	// 第一天上課日期 未填寫
	define("ERR_INSURANCE_OVEFR",	111015);	// 保險人數大於訂單上課人數
	define("ERR_BIRTH15",			111016);	// 未滿15歲

	// 保險 成功
	define("INSURANCE_OK",	110001);	// 保險資料無誤
	// 保險 狀態
	define("INSURANCE_STATUS_COLECTING"	,	110002);	// 團員資料填寫中...
	define("INSURANCE_STATUS_WRITE_DONE",	110003);	// 團員資料已填寫齊全.
	define("INSURANCE_STATUS_ALLPASS"	,	110004);	// 所有「人員」已核保
	define("INSURANCE_STATUS_NO_ALLPASS",	110005);	// 部分「人員」未核保
	define("INSURANCE_STATUS_NULL_DATA",	110006);	// 尚未收到任何保單
	define("INSURANCE_STATUS_INTERNAL_ERR",	110007);	// 內部錯誤 (EX:主揪沒填單)
	define("INSURANCE_STATUS_ALL_DENY"	,	110008);	// 所有「人員」皆未核保
	define("INSURANCE_STATUS_COLECTING_DONE"	,	110009);	// 團員資料填完成（尚未送出核保按鈕
	define("INSURANCE_STATUS_ORDER_CANCELED"	,	110010);	// 訂單已取消

	
	
	$INSURANCE_STATUS_LABEL[INSURANCE_STATUS_COLECTING] 	= '<font color=#02c736>團員資料填寫中...</font>';
	$INSURANCE_STATUS_LABEL[INSURANCE_STATUS_WRITE_DONE] 	= '<font color=#02c736>團員資料已填寫齊全</font>';
	$INSURANCE_STATUS_LABEL[INSURANCE_STATUS_ALLPASS] 		= '<cB>✅所有「團員」皆已核保</cB>';
	$INSURANCE_STATUS_LABEL[INSURANCE_STATUS_NO_ALLPASS] 	= '<cR>🚫部分「團員」未核保</cR>';
	$INSURANCE_STATUS_LABEL[INSURANCE_STATUS_ALL_DENY] 		= '<cR>🚫所有「團員」皆未核保</cR>';
	$INSURANCE_STATUS_LABEL[INSURANCE_STATUS_NULL_DATA] 	= '<cR>🚫尚未收到任何資料</cR>';
	$INSURANCE_STATUS_LABEL[INSURANCE_STATUS_INTERNAL_ERR] 	= '<cR>🚫Internal ERR</cR>';
	$INSURANCE_STATUS_LABEL[INSURANCE_STATUS_COLECTING_DONE]= '<font color=#02c736>團員資料填完成，但尚未送出核保按鈕</font>';
	$INSURANCE_STATUS_LABEL[INSURANCE_STATUS_ORDER_CANCELED]= '<font color=#02c736>團員資料填完成，但訂單已取消</font>';




	$POST_RESPONSE['DUP_REG']	="重複註冊";
	$POST_RESPONSE['REG_FAIL']	="註冊失敗";
	$POST_RESPONSE['LOGIN_FAIL']="登入失敗";
	$POST_RESPONSE['NULL_INPUT']="資料填寫不完整";	
	$POST_RESPONSE['ERR_PASSPORT']	="護照格式錯誤";
	$POST_RESPONSE['ERR_EMAIL']		="信箱格式錯誤";
	$POST_RESPONSE['ERR_ADDR']		="地址填寫不完整";
	$POST_RESPONSE['ERR_PHONE']		="電話填寫不完整";



	$POST_RESPONSE['REG_OK']		="註冊成功";
	$POST_RESPONSE['LOGIN_OK']		="登入成功";
	$POST_RESPONSE['PWDUPDATE_OK']	="密碼更新成功";
	$POST_RESPONSE['PWRESET_REQ']	="密碼確認信已寄出";
	

	$COUNTRY_CODE['TW']=array("code"=>'886',"name"=>'Taiwan');
	$COUNTRY_CODE['CN']=array("code"=>'86', "name"=>'China');
	$COUNTRY_CODE['HK']=array("code"=>'852',"name"=>'Hong Kong');
	$COUNTRY_CODE['MA']=array("code"=>'853',"name"=>'Macau');	
	$COUNTRY_CODE['JP']=array("code"=>'81', "name"=>'Japan');	
	$COUNTRY_CODE['SG']=array("code"=>'65', "name"=>'Singapore');

	$COUNTRY_CODE2['886']	=array("sname"=>'TW',"name"=>'Taiwan'		,"cname"=>'台灣');
	$COUNTRY_CODE2['86']	=array("sname"=>'CN',"name"=>'China'		,"cname"=>'大陸');
	$COUNTRY_CODE2['852']	=array("sname"=>'HK',"name"=>'Hong Kong'	,"cname"=>'香港');	
	$COUNTRY_CODE2['853']	=array("sname"=>'MA',"name"=>'Macau'		,"cname"=>'澳門');	
	$COUNTRY_CODE2['81']	=array("sname"=>'JP',"name"=>'Japan'		,"cname"=>'日本');	
	$COUNTRY_CODE2['65']	=array("sname"=>'SG',"name"=>'Singapore'	,"cname"=>'新加坡');		

	// 上課天數
	$SKI_LEVEL_CLASS_DAYS[0]='未填';	
	$SKI_LEVEL_CLASS_DAYS[1]='無';
	$SKI_LEVEL_CLASS_DAYS[2]='1-3天';
	$SKI_LEVEL_CLASS_DAYS[3]='3-7天';
	$SKI_LEVEL_CLASS_DAYS[4]='7 天以上';
	// 滑雪天數
	$SKI_LEVEL_PLAY_DAYS[0]='未填';
	$SKI_LEVEL_PLAY_DAYS[1]='無';
	$SKI_LEVEL_PLAY_DAYS[2]='1-3 天';
	$SKI_LEVEL_PLAY_DAYS[3]='3-14 天';
	$SKI_LEVEL_PLAY_DAYS[4]='15 天以上';
	// 連續轉彎
	$SKI_LEVEL_CONT_TURN[0]='未填';	
	$SKI_LEVEL_CONT_TURN[1]='否';	
	$SKI_LEVEL_CONT_TURN[2]='綠線';	
	$SKI_LEVEL_CONT_TURN[3]='紅線';
	$SKI_LEVEL_CONT_TURN[4]='黑線';

	$COUNTRY_STR[0] = 'n/a';
	$COUNTRY_STR[1] = '台灣';
	$COUNTRY_STR[2] = '香港';
	$COUNTRY_STR[3] = '大陸';





?>