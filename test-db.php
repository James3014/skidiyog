<?php
// 最簡單的資料庫連接測試
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== Database Connection Test ===\n\n";

// 顯示環境變數
echo "Environment Variables:\n";
echo "DB_HOST: " . getenv('DB_HOST') . "\n";
echo "DB_USER: " . getenv('DB_USER') . "\n";
echo "DB_NAME: " . getenv('DB_NAME') . "\n";
echo "DB_PORT: " . getenv('DB_PORT') . "\n";
echo "DB_PASS: " . (getenv('DB_PASS') ? '[SET]' : '[NOT SET]') . "\n\n";

// 嘗試連接
$host = getenv('DB_HOST') ?: 'tpe1.clusters.zeabur.com';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$db = getenv('DB_NAME') ?: 'skidiyog';
$port = (int)(getenv('DB_PORT') ?: 3306);

echo "Attempting connection to:\n";
echo "Host: $host\n";
echo "Port: $port\n";
echo "User: $user\n";
echo "Database: $db\n\n";

// 設定超時
ini_set('default_socket_timeout', 5);
ini_set('mysqli.connect_timeout', 5);

$start = microtime(true);
$mysqli = @new mysqli($host, $user, $pass, $db, $port);
$time = microtime(true) - $start;

echo "Connection attempt took: " . round($time, 2) . " seconds\n\n";

if ($mysqli->connect_error) {
    echo "❌ Connection FAILED\n";
    echo "Error code: " . $mysqli->connect_errno . "\n";
    echo "Error message: " . $mysqli->connect_error . "\n";
    exit(1);
} else {
    echo "✅ Connection SUCCESSFUL\n";
    echo "MySQL version: " . $mysqli->server_info . "\n";

    // 測試查詢
    $result = $mysqli->query("SHOW TABLES");
    if ($result) {
        echo "Tables found: " . $result->num_rows . "\n";
        while ($row = $result->fetch_array()) {
            echo "  - " . $row[0] . "\n";
        }
    }

    $mysqli->close();
}
?>
