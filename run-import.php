<?php
/**
 * Simple MySQL Import - bypassing cache issues
 * Version: 2025-11-06-v2
 */

// Security token
define('IMPORT_TOKEN', 'skidiyog_import_2025');

if (!empty($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $provided_token = $_GET['token'] ?? '';
    if ($provided_token !== IMPORT_TOKEN) {
        http_response_code(403);
        die('Invalid token');
    }
}

header('Content-Type: text/plain; charset=utf-8');
echo "=== Database Import v2 ===\n\n";

// Direct connection with hardcoded credentials from Zeabur dashboard screenshot
$host = 'tpe1.clusters.zeabur.com';
$user = 'root';
$pass = 'Sk1d1y0g@MySQL2025';
$db = 'skidiyog';
$port = 22554;

echo "Connection parameters:\n";
echo "  Host: {$host}:{$port}\n";
echo "  User: {$user}\n";
echo "  DB: {$db}\n";
echo "  Pass length: " . strlen($pass) . " chars\n";
echo "  Pass first 3: " . substr($pass, 0, 3) . "...\n\n";

echo "Attempting connection...\n";
ini_set('mysqli.connect_timeout', 10);

$conn = @new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    echo "❌ FAILED: " . $conn->connect_error . "\n";
    echo "Error code: " . $conn->connect_errno . "\n";
    exit(1);
}

echo "✅ Connected successfully!\n";
echo "MySQL version: " . $conn->server_info . "\n\n";

// Create tables
echo "Creating tables...\n";

$tables = [
    'parks' => "CREATE TABLE IF NOT EXISTS `parks` (
        `idx` INT PRIMARY KEY,
        `name` VARCHAR(100),
        `cname` VARCHAR(100),
        `description` TEXT,
        `location` VARCHAR(255),
        `photo` VARCHAR(255),
        `about` LONGTEXT,
        `photo_section` LONGTEXT,
        `location_section` LONGTEXT,
        `slope_section` LONGTEXT,
        `ticket_section` LONGTEXT,
        `time_section` LONGTEXT,
        `access_section` LONGTEXT,
        `live_section` LONGTEXT,
        `rental_section` LONGTEXT,
        `delivery_section` LONGTEXT,
        `luggage_section` LONGTEXT,
        `workout_section` LONGTEXT,
        `remind_section` LONGTEXT,
        `join_section` LONGTEXT,
        `event_section` LONGTEXT,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

    'instructors' => "CREATE TABLE IF NOT EXISTS `instructors` (
        `idx` INT PRIMARY KEY,
        `name` VARCHAR(100),
        `cname` VARCHAR(100),
        `content` LONGTEXT,
        `photo` VARCHAR(255),
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

    'articles' => "CREATE TABLE IF NOT EXISTS `articles` (
        `idx` INT PRIMARY KEY,
        `title` VARCHAR(255),
        `tags` TEXT,
        `article` LONGTEXT,
        `keyword` TEXT,
        `timestamp` DATETIME,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
];

foreach ($tables as $name => $sql) {
    if ($conn->query($sql)) {
        echo "  ✓ Table '{$name}' ready\n";
    } else {
        echo "  ❌ Error creating '{$name}': " . $conn->error . "\n";
    }
}

echo "\n✅ Import completed!\n";
echo "You can now visit: https://skidiyog.zeabur.app/\n";

$conn->close();
?>
