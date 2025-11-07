#!/usr/bin/env php
<?php
/**
 * Database Health Check Script
 * Verifies that database tables exist and contain data
 */

require_once(__DIR__ . '/includes/db.class.php');

echo "🔍 SKIDIY Database Health Check\n";
echo "================================\n\n";

$db = new DB();

if ($db->error) {
    echo "❌ Database connection failed: {$db->error}\n";
    exit(1);
}

echo "✅ Database connection successful\n";
echo "📁 Database file: " . DB_FILE . "\n\n";

// Check tables
$tables = ['parks', 'instructors', 'articles'];
$total_issues = 0;

foreach ($tables as $table) {
    echo "Checking table: {$table}\n";

    $sql = "SELECT COUNT(*) as count FROM `{$table}`";
    $result = $db->QUERY('SELECT', $sql);

    if ($db->error) {
        echo "  ❌ Error: {$db->error}\n";
        $total_issues++;
    } else {
        $count = $result[0]['count'] ?? 0;
        if ($count > 0) {
            echo "  ✅ {$count} records found\n";
        } else {
            echo "  ⚠️  Table is empty\n";
            $total_issues++;
        }
    }
    echo "\n";
}

// Check sample data
echo "Sample Parks Data:\n";
$sql = "SELECT name, cname FROM parks LIMIT 5";
$parks = $db->QUERY('SELECT', $sql);

if (!empty($parks)) {
    foreach ($parks as $park) {
        echo "  • {$park['name']} ({$park['cname']})\n";
    }
} else {
    echo "  ⚠️  No parks data found\n";
    $total_issues++;
}

echo "\n================================\n";
if ($total_issues === 0) {
    echo "✅ All checks passed! Database is healthy.\n";
    exit(0);
} else {
    echo "⚠️  Found {$total_issues} issue(s). Please review.\n";
    exit(1);
}
?>
