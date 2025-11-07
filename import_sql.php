<?php
/**
 * Import SQL dump to Volume database
 */
header('Content-Type: text/plain; charset=utf-8');

echo "=== SQL Import Script ===\n\n";

$sql_file = __DIR__ . '/data_dump.sql';
echo "1. SQL file: {$sql_file}\n";
echo "   Exists: " . (file_exists($sql_file) ? "YES" : "NO") . "\n";

if (!file_exists($sql_file)) {
    echo "\n❌ ERROR: SQL file not found!\n";
    exit(1);
}

$sql = file_get_contents($sql_file);
echo "   Size: " . strlen($sql) . " bytes\n";

echo "\n2. Connecting to /data/skidiyog.db...\n";
$pdo = new PDO('sqlite:/data/skidiyog.db');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "3. Executing SQL dump...\n";
try {
    $pdo->exec($sql);
    echo "   ✓ Import successful\n";
} catch (Exception $e) {
    echo "   ✗ Import failed: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n4. Verification:\n";
$parks = $pdo->query("SELECT COUNT(*) FROM parks")->fetchColumn();
$instructors = $pdo->query("SELECT COUNT(*) FROM instructors")->fetchColumn();
$articles = $pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();

echo "   Parks: {$parks}\n";
echo "   Instructors: {$instructors}\n";
echo "   Articles: {$articles}\n";

echo "\n5. Sample data:\n";
$sample = $pdo->query("SELECT name, cname FROM parks LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
foreach ($sample as $row) {
    echo "   - {$row['name']}: {$row['cname']}\n";
}

echo "\n=== Import Complete ===\n";
?>
