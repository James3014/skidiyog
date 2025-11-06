<?php
/**
 * Direct MySQL Connection Test with various approaches
 * Last updated: 2025-11-06
 */

header('Content-Type: text/plain; charset=utf-8');

echo "=== MySQL Connection Diagnostic ===\n\n";

// Test 1: Standard mysqli connection
echo "[Test 1] Standard mysqli_connect\n";
echo "Parameters:\n";
echo "  Host: tpe1.clusters.zeabur.com\n";
echo "  Port: 22554\n";
echo "  User: root\n";
echo "  Pass: Sk1d1y0g@MySQL2025 (length: " . strlen('Sk1d1y0g@MySQL2025') . ")\n";
echo "  DB: skidiyog\n\n";

ini_set('mysqli.connect_timeout', 10);
ini_set('default_socket_timeout', 10);

$conn1 = @mysqli_connect(
    'tpe1.clusters.zeabur.com',
    'root',
    'Sk1d1y0g@MySQL2025',
    'skidiyog',
    22554
);

if ($conn1) {
    echo "✅ SUCCESS - Connected!\n";
    echo "MySQL Version: " . mysqli_get_server_info($conn1) . "\n";

    // Try to create tables
    echo "\nAttempting to create tables...\n";

    $sql = "CREATE TABLE IF NOT EXISTS parks (
        idx INT PRIMARY KEY,
        name VARCHAR(100),
        cname VARCHAR(100)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    if (mysqli_query($conn1, $sql)) {
        echo "✅ Parks table created/verified\n";
    } else {
        echo "❌ Error: " . mysqli_error($conn1) . "\n";
    }

    $sql2 = "CREATE TABLE IF NOT EXISTS instructors (
        idx INT PRIMARY KEY,
        name VARCHAR(100),
        cname VARCHAR(100)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    if (mysqli_query($conn1, $sql2)) {
        echo "✅ Instructors table created/verified\n";
    } else {
        echo "❌ Error: " . mysqli_error($conn1) . "\n";
    }

    $sql3 = "CREATE TABLE IF NOT EXISTS articles (
        idx INT PRIMARY KEY,
        title VARCHAR(255),
        article LONGTEXT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    if (mysqli_query($conn1, $sql3)) {
        echo "✅ Articles table created/verified\n";
    } else {
        echo "❌ Error: " . mysqli_error($conn1) . "\n";
    }

    mysqli_close($conn1);

    echo "\n=== SUCCESS ===\n";
    echo "Database is ready!\n";
    echo "Visit: https://skidiyog.zeabur.app/ to test the application\n";

} else {
    echo "❌ FAILED\n";
    echo "Error: " . mysqli_connect_error() . "\n";
    echo "Error Code: " . mysqli_connect_errno() . "\n";

    // Additional diagnostics
    echo "\n=== Diagnostics ===\n";
    echo "PHP Version: " . phpversion() . "\n";
    echo "MySQLi Extension: " . (extension_loaded('mysqli') ? 'Loaded' : 'NOT LOADED') . "\n";
    echo "MySQLi Timeout: " . ini_get('mysqli.connect_timeout') . " seconds\n";
    echo "Socket Timeout: " . ini_get('default_socket_timeout') . " seconds\n";
}
?>
