<?php
/**
 * Simple health check endpoint
 * Does NOT require database connection
 * Used for monitoring and debugging
 */

header('Content-Type: application/json');

$health = [
    'status' => 'ok',
    'timestamp' => date('Y-m-d H:i:s'),
    'php_version' => phpversion(),
    'server' => $_SERVER['SERVER_NAME'] ?? 'unknown',
];

// Check PHP extensions
$extensions = ['mysqli', 'json', 'curl', 'mbstring'];
$health['extensions'] = [];
foreach ($extensions as $ext) {
    $health['extensions'][$ext] = extension_loaded($ext);
}

// Try to connect to database (with timeout)
ini_set("mysqli.connect_timeout", 5);
$db_host = getenv('DB_HOST') ?: 'skidiy-rds-master.cgseduwrbkzc.ap-northeast-1.rds.amazonaws.com';
$db_user = getenv('DB_USER') ?: 'dba';
$db_pass = getenv('DB_PASS') ?: 'dba_Skidiy66';
$db_name = getenv('DB_NAME') ?: 'skidiyog';
$db_port = getenv('DB_PORT') ?: '33668';

$conn = @mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);
if ($conn) {
    $health['database'] = 'connected';
    $health['database_version'] = mysqli_get_server_info($conn);
    mysqli_close($conn);
} else {
    $health['database'] = 'disconnected';
    $health['database_error'] = mysqli_connect_error();
    $health['status'] = 'degraded';
}

http_response_code($health['database'] === 'connected' ? 200 : 503);
echo json_encode($health, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>
