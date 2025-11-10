<?php
/**
 * SKidiyog Simplified Configuration
 * No authentication required - only backend editing and frontend viewing
 * Database configuration handled by includes/db.class.php
 */

// Preview/crawl protection (can be overridden via environment variables)
define('SKID_PREVIEW_TOKEN', getenv('SKID_PREVIEW_TOKEN') ?: 'f58c7d4b-0e68-4965-97d1-6e9d8b6a4c39');
define('SKID_PREVIEW_TOKEN_ENFORCED', getenv('SKID_PREVIEW_TOKEN_ENFORCED') !== '0');
define('SKID_PREVIEW_RENDER_PARAM', getenv('SKID_PREVIEW_RENDER_PARAM') ?: 'render');
define('SKID_PREVIEW_RENDER_VALUE', getenv('SKID_PREVIEW_RENDER_VALUE') ?: 'static');

// System messages array (minimal)
$SYSMSG = [];

// System messages (same for all environments)
$SYSMSG['email_error'] = 'Email信箱格式錯誤！';
$SYSMSG['reset_sent'] = '重置密碼信件已寄送！';
$SYSMSG['reset_fail'] = '重置信件寄送失敗，請與我們聯繫，謝謝！';
$SYSMSG['email_not_found'] = '查無您的Email信箱，請您確認是否輸入正確。';
$SYSMSG['idx_error'] = '系統處理異常，請與我們反應，謝謝！';
$SYSMSG['auth_fail'] = 'Email登入失敗，請重新確認，謝謝！';
$SYSMSG['fbsdklogin_fail'] = 'Facebook帳號登入失敗，請與我們聯繫，謝謝！';
$SYSMSG['fblogin_success'] = 'Facebook登入成功！歡迎您進入自助滑雪平台。';
$SYSMSG['emaillogin_success'] = 'E-mail登入成功！歡迎您進入自助滑雪平台。';
$SYSMSG['allpay_success'] = '付款成功！預祝您有個愉快的滑雪假期^^';
$SYSMSG['allpay_fail'] = '付款交易失敗，煩請請重新操作或與我們聯繫，謝謝！';
$SYSMSG['order_fail'] = '訂單交易失敗，請重新再試一次，如仍有問題請儘速與我們聯繫，謝謝！';
$SYSMSG['order_taken'] = '該上課日期已被預約，請重新選課預約，謝謝！';
$SYSMSG['payment_fail'] = '網路交易失敗，請重新再試一次，如仍有問題請儘速與我們聯繫，謝謝！';
$SYSMSG['create_member_success'] = '新會員註冊註冊成功！';
$SYSMSG['create_member_fail'] = '註冊失敗，請再重新輸入一次，仍有問題請與我們聯繫！';
$SYSMSG['reset_success'] = '重置密碼成功，請使用新密碼登入，謝謝！';
$SYSMSG['instructor_info_error'] = '教練資料異常，請聯繫系統管理者。';
$SYSMSG['used_refer'] = '該推薦連結已失效，請重新索取。';
$SYSMSG['arranged'] = '排課已完成。';
$SYSMSG['update_order_success'] = '訂單修改成功。';
$SYSMSG['update_order_none'] = '訂單無異動。';
$SYSMSG['order_closed'] = '訂單系統維護中，預計10/1晚上10點開放，造成您的不便請見諒。';
$SYSMSG['config_member_success'] = '帳號修改成功。';
$SYSMSG['member_already_exist'] = '這個電子郵件信箱已經有人使用，請更改您的郵件信箱，重新註冊';
$SYSMSG['reset_mail_sent'] = '重置密碼信件已寄送！';
$SYSMSG['reset_mail_not_sent'] = '重置密碼信件寄送失敗，請與我們聯繫，謝謝！';
$SYSMSG['reset_password_success'] = '重置密碼成功，請使用新密碼登入，謝謝！';
$SYSMSG['wrong_password'] = '密碼錯誤，請您確認是否輸入正確';
$SYSMSG['config_member_fail'] = '無法更新資料，請與我們聯繫，謝謝';
$SYSMSG['config_member_success'] = '資料已經更新，謝謝！';

// Return an empty array to indicate successful configuration
return $SYSMSG;
?>
