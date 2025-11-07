<?php
/**
 * Import data from JSON export to Volume database
 */
header('Content-Type: text/plain; charset=utf-8');

echo "=== Database Import from JSON ===\n\n";

$json_file = __DIR__ . '/data_export.json';
echo "1. JSON file: {$json_file}\n";
echo "   Exists: " . (file_exists($json_file) ? "YES" : "NO") . "\n";

if (!file_exists($json_file)) {
    echo "\n❌ ERROR: JSON file not found!\n";
    exit(1);
}

$json_content = file_get_contents($json_file);
$data = json_decode($json_content, true);

if ($data === null) {
    echo "\n❌ ERROR: Failed to parse JSON!\n";
    exit(1);
}

echo "   Parks: " . count($data['parks']) . "\n";
echo "   Instructors: " . count($data['instructors']) . "\n";
echo "   Articles: " . count($data['articles']) . "\n";

$target_db_file = '/data/skidiyog.db';
echo "\n2. Target database: {$target_db_file}\n";
echo "   Exists: " . (file_exists($target_db_file) ? "YES" : "NO") . "\n";

echo "\n3. Connecting to database...\n";
try {
    $pdo = new PDO('sqlite:' . $target_db_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "   ✓ Connected\n";
} catch (Exception $e) {
    echo "   ✗ Connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n4. Clearing existing data...\n";
try {
    $pdo->exec("DELETE FROM parks");
    $pdo->exec("DELETE FROM instructors");
    $pdo->exec("DELETE FROM articles");
    echo "   ✓ Tables cleared\n";
} catch (Exception $e) {
    echo "   Note: " . $e->getMessage() . "\n";
}

// Import parks
echo "\n5. Importing parks...\n";
$imported = 0;
foreach ($data['parks'] as $park) {
    $columns = array_keys($park);
    $placeholders = array_fill(0, count($columns), '?');
    $sql = "INSERT OR REPLACE INTO parks (" . implode(',', $columns) . ") VALUES (" . implode(',', $placeholders) . ")";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array_values($park));
    $imported++;
}
echo "   Imported: {$imported} parks ✓\n";

// Import instructors
echo "\n6. Importing instructors...\n";
$imported = 0;
foreach ($data['instructors'] as $instructor) {
    $columns = array_keys($instructor);
    $placeholders = array_fill(0, count($columns), '?');
    $sql = "INSERT OR REPLACE INTO instructors (" . implode(',', $columns) . ") VALUES (" . implode(',', $placeholders) . ")";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array_values($instructor));
    $imported++;
}
echo "   Imported: {$imported} instructors ✓\n";

// Import articles
echo "\n7. Importing articles...\n";
$imported = 0;
foreach ($data['articles'] as $article) {
    $columns = array_keys($article);
    $placeholders = array_fill(0, count($columns), '?');
    $sql = "INSERT OR REPLACE INTO articles (" . implode(',', $columns) . ") VALUES (" . implode(',', $placeholders) . ")";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array_values($article));
    $imported++;
}
echo "   Imported: {$imported} articles ✓\n";

echo "\n=== Import Complete ===\n";
echo "\nVerification:\n";
echo "Parks: " . $pdo->query("SELECT COUNT(*) FROM parks")->fetchColumn() . "\n";
echo "Instructors: " . $pdo->query("SELECT COUNT(*) FROM instructors")->fetchColumn() . "\n";
echo "Articles: " . $pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn() . "\n";

echo "\nSample parks:\n";
$samples = $pdo->query("SELECT name, cname FROM parks LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
foreach ($samples as $sample) {
    echo "  - {$sample['name']}: {$sample['cname']}\n";
}
?>
