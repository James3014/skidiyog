<?php
// 超簡單的環境變數檢查,不連接資料庫
header('Content-Type: text/plain; charset=utf-8');

echo "=== Environment Variables Debug ===\n\n";

$vars = ['DB_HOST', 'DB_USER', 'DB_PASS', 'DB_PASSWORD', 'DB_NAME', 'DB_PORT'];

foreach ($vars as $var) {
    $value = getenv($var);
    if ($var === 'DB_PASS' || $var === 'DB_PASSWORD') {
        echo "$var = " . ($value ? '[***HIDDEN***]' : '[NOT SET]') . "\n";
    } else {
        echo "$var = " . ($value ?: '[NOT SET]') . "\n";
    }
}

echo "\n=== PHP Info ===\n";
echo "PHP Version: " . phpversion() . "\n";
echo "mysqli extension: " . (extension_loaded('mysqli') ? 'Loaded' : 'NOT LOADED') . "\n";

echo "\n=== Server Info ===\n";
echo "HTTP_HOST: " . ($_SERVER['HTTP_HOST'] ?? 'not set') . "\n";
echo "DOCUMENT_ROOT: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'not set') . "\n";
echo "SCRIPT_FILENAME: " . ($_SERVER['SCRIPT_FILENAME'] ?? 'not set') . "\n";
?>
