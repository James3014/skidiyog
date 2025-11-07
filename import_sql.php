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

echo "3. Clearing existing data...\n";
try {
    $pdo->exec("DELETE FROM parks");
    $pdo->exec("DELETE FROM instructors");
    $pdo->exec("DELETE FROM articles");
    echo "   ✓ Tables cleared\n";
} catch (Exception $e) {
    echo "   Note: " . $e->getMessage() . "\n";
}

echo "\n4. Executing SQL dump...\n";
try {
    // Skip CREATE TABLE statements, only import data
    $sql_lines = explode("\n", $sql);
    $insert_sql = [];
    foreach ($sql_lines as $line) {
        if (preg_match('/^INSERT INTO/', $line)) {
            $insert_sql[] = $line;
        }
    }
    $pdo->exec(implode("\n", $insert_sql));
    echo "   ✓ Import successful\n";
} catch (Exception $e) {
    echo "   ✗ Import failed: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n5. Verification:\n";
$parks = $pdo->query("SELECT COUNT(*) FROM parks")->fetchColumn();
$instructors = $pdo->query("SELECT COUNT(*) FROM instructors")->fetchColumn();
$articles = $pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();

echo "   Parks: {$parks}\n";
echo "   Instructors: {$instructors}\n";
echo "   Articles: {$articles}\n";

echo "\n6. Sample data:\n";
$sample = $pdo->query("SELECT name, cname FROM parks LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
foreach ($sample as $row) {
    echo "   - {$row['name']}: {$row['cname']}\n";
}

echo "\n=== Import Complete ===\n";
?>
