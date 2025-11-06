<?php
// Test if database configuration is being loaded correctly
header('Content-Type: text/plain; charset=utf-8');

echo "=== Database Configuration Test ===\n\n";

// Load db.class.php
require_once __DIR__ . '/includes/db.class.php';

echo "Configuration loaded:\n";
echo "DB_HOST: " . DB_HOST . "\n";
echo "DB_USER: " . DB_USER . "\n";
echo "DB_PASS length: " . strlen(DB_PASS) . " characters\n";
echo "DB_PASS empty?: " . (empty(DB_PASS) ? 'YES' : 'NO') . "\n";
echo "DB_DB: " . DB_DB . "\n";
echo "DB_PORT: " . DB_PORT . "\n\n";

echo "First 5 chars of password: " . substr(DB_PASS, 0, 5) . "...\n\n";

// Test connection with explicit parameters
echo "Testing connection...\n";
$host = DB_HOST;
$user = DB_USER;
$pass = DB_PASS;
$db = DB_DB;
$port = (int)DB_PORT;

echo "Parameters:\n";
echo "  host: $host\n";
echo "  user: $user\n";
echo "  pass length: " . strlen($pass) . "\n";
echo "  db: $db\n";
echo "  port: $port\n\n";

ini_set('mysqli.connect_timeout', 10);
$conn = @new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    echo "❌ Connection failed: " . $conn->connect_error . "\n";
    echo "Error number: " . $conn->connect_errno . "\n";
} else {
    echo "✅ Connection successful!\n";
    echo "Server version: " . $conn->server_info . "\n";
    $conn->close();
}
?>
