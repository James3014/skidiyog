<?php
/**
 * Check database tables
 */
header('Content-Type: text/plain; charset=utf-8');

echo "=== Database Tables Check ===\n\n";

$pdo = new PDO('sqlite:/data/skidiyog.db');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "1. List all tables:\n";
$tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name")->fetchAll(PDO::FETCH_COLUMN);
foreach ($tables as $table) {
    echo "   - {$table}\n";
}

echo "\n2. parkInfo table:\n";
try {
    $count = $pdo->query("SELECT COUNT(*) FROM parkInfo")->fetchColumn();
    echo "   Exists: YES\n";
    echo "   Rows: {$count}\n";

    if ($count > 0) {
        echo "\n   Sample data:\n";
        $sample = $pdo->query("SELECT name, cname FROM parkInfo LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($sample as $row) {
            echo "     - {$row['name']}: {$row['cname']}\n";
        }
    }
} catch (Exception $e) {
    echo "   Exists: NO\n";
    echo "   Error: " . $e->getMessage() . "\n";
}

echo "\n3. parks table:\n";
try {
    $count = $pdo->query("SELECT COUNT(*) FROM parks")->fetchColumn();
    echo "   Exists: YES\n";
    echo "   Rows: {$count}\n";

    if ($count > 0) {
        echo "\n   Sample data:\n";
        $sample = $pdo->query("SELECT name, cname FROM parks LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($sample as $row) {
            echo "     - {$row['name']}: {$row['cname']}\n";
        }
    }
} catch (Exception $e) {
    echo "   Exists: NO\n";
    echo "   Error: " . $e->getMessage() . "\n";
}
?>
