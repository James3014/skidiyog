<?php
/**
 * Test MySQL connectivity with various approaches
 */

header('Content-Type: text/plain; charset=utf-8');

echo "=== MySQL Connection Test ===\n\n";

// 嘗試多種密碼和認證組合
$attempts = [
    [
        'host' => 'tpe1.clusters.zeabur.com',
        'port' => 22554,
        'user' => 'root',
        'pass' => 'Sk1d1y0g@MySQL2025',
        'name' => 'Full password'
    ],
    [
        'host' => 'tpe1.clusters.zeabur.com',
        'port' => 22554,
        'user' => 'root',
        'pass' => '',
        'name' => 'No password'
    ],
    [
        'host' => '127.0.0.1',
        'port' => 3306,
        'user' => 'root',
        'pass' => '',
        'name' => 'Localhost MySQL'
    ]
];

foreach ($attempts as $attempt) {
    echo "Attempt: {$attempt['name']}\n";
    echo "  {$attempt['user']}@{$attempt['host']}:{$attempt['port']}\n";

    ini_set('mysqli.connect_timeout', 5);
    $conn = @new mysqli(
        $attempt['host'],
        $attempt['user'],
        $attempt['pass'],
        'skidiyog',
        $attempt['port']
    );

    if ($conn->connect_error) {
        echo "  ❌ Failed: " . $conn->connect_error . "\n";
    } else {
        echo "  ✅ Connected!\n";
        echo "  MySQL: " . $conn->server_info . "\n";

        // Check if tables exist
        $result = $conn->query("SHOW TABLES");
        if ($result) {
            echo "  Tables: " . $result->num_rows . " found\n";
        }
        $conn->close();
    }
    echo "\n";
}

// 嘗試使用 PDO
echo "=== Testing with PDO ===\n";
if (extension_loaded('pdo_mysql')) {
    try {
        $pdo = new PDO('mysql:host=tpe1.clusters.zeabur.com;port=22554;dbname=skidiyog', 'root', 'Sk1d1y0g@MySQL2025');
        echo "✅ PDO connected!\n";
        $pdo = null;
    } catch (PDOException $e) {
        echo "❌ PDO failed: " . $e->getMessage() . "\n";
    }
} else {
    echo "⚠️ PDO MySQL extension not loaded\n";
}
?>
