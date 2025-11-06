<?php
/**
 * Execute SQL initialization script
 * This script reads and executes init.sql
 */

header('Content-Type: text/plain; charset=utf-8');

// Security token
if (($_GET['token'] ?? '') !== 'skidiyog_import_2025') {
    http_response_code(403);
    die('Access denied');
}

echo "=== SQL Initialization ===\n\n";

// Read the SQL file
$sqlFile = __DIR__ . '/init.sql';
if (!file_exists($sqlFile)) {
    echo "❌ Error: init.sql not found at $sqlFile\n";
    exit(1);
}

echo "Reading SQL file: $sqlFile\n";
$sql = file_get_contents($sqlFile);
echo "SQL file size: " . strlen($sql) . " bytes\n\n";

// Parse SQL statements (split by semicolon)
$statements = array_filter(array_map('trim', explode(';', $sql)));

echo "Found " . count($statements) . " SQL statements\n\n";

// Try to connect with different credentials
$connections = [
    [
        'host' => 'tpe1.clusters.zeabur.com',
        'port' => 22554,
        'user' => 'root',
        'pass' => 'Sk1d1y0g@MySQL2025',
    ],
    [
        'host' => 'localhost',
        'port' => 3306,
        'user' => 'root',
        'pass' => '',
    ]
];

$conn = null;
foreach ($connections as $cred) {
    echo "Trying {$cred['user']}@{$cred['host']}:{$cred['port']}...\n";
    ini_set('mysqli.connect_timeout', 5);
    $conn = @new mysqli(
        $cred['host'],
        $cred['user'],
        $cred['pass'],
        'skidiyog',
        $cred['port']
    );

    if (!$conn->connect_error) {
        echo "✅ Connected!\n\n";
        break;
    } else {
        echo "❌ " . $conn->connect_error . "\n";
        $conn = null;
    }
}

if (!$conn) {
    echo "\n❌ Could not connect to MySQL with any credentials\n";
    exit(1);
}

// Execute each statement
$success = 0;
$failed = 0;

foreach ($statements as $i => $statement) {
    if (empty($statement)) continue;

    // Truncate long statements for display
    $display = strlen($statement) > 80 ? substr($statement, 0, 80) . '...' : $statement;
    echo "[$i] " . $display . "\n";

    if ($conn->query($statement)) {
        echo "    ✓ OK\n";
        $success++;
    } else {
        echo "    ✗ " . $conn->error . "\n";
        $failed++;
    }
}

echo "\n=== Results ===\n";
echo "Successful: $success\n";
echo "Failed: $failed\n";

// Verify tables exist
echo "\n=== Verification ===\n";
$result = $conn->query("SHOW TABLES");
if ($result) {
    echo "Tables in database: " . $result->num_rows . "\n";
    while ($row = $result->fetch_row()) {
        echo "  - {$row[0]}\n";
    }
}

$conn->close();
echo "\n✅ Initialization complete!\n";
?>
