<?php
/**
 * Import data from local database to Volume database
 * This script should be run ONCE after Volume is mounted
 */
header('Content-Type: text/plain; charset=utf-8');

echo "=== Database Import Script ===\n\n";

// Source: old local database
$source_db_file = __DIR__ . '/data/skidiyog.db';
echo "1. Source database: {$source_db_file}\n";
echo "   Exists: " . (file_exists($source_db_file) ? "YES" : "NO") . "\n";

// Target: new Volume database
$target_db_file = '/data/skidiyog.db';
echo "\n2. Target database: {$target_db_file}\n";
echo "   Exists: " . (file_exists($target_db_file) ? "YES" : "NO") . "\n";

if (!file_exists($source_db_file)) {
    echo "\n❌ ERROR: Source database not found!\n";
    exit(1);
}

echo "\n3. Connecting to source database...\n";
$source_pdo = new PDO('sqlite:' . $source_db_file);
$source_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "4. Connecting to target database...\n";
$target_pdo = new PDO('sqlite:' . $target_db_file);
$target_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Copy parks data
echo "\n5. Copying parks data...\n";
$parks = $source_pdo->query("SELECT * FROM parks")->fetchAll(PDO::FETCH_ASSOC);
echo "   Found " . count($parks) . " parks\n";

$copied = 0;
foreach ($parks as $park) {
    $columns = array_keys($park);
    $placeholders = array_fill(0, count($columns), '?');
    $sql = "INSERT OR REPLACE INTO parks (" . implode(',', $columns) . ") VALUES (" . implode(',', $placeholders) . ")";
    $stmt = $target_pdo->prepare($sql);
    $stmt->execute(array_values($park));
    $copied++;
}
echo "   Copied: {$copied} parks ✓\n";

// Copy instructors data
echo "\n6. Copying instructors data...\n";
$instructors = $source_pdo->query("SELECT * FROM instructors")->fetchAll(PDO::FETCH_ASSOC);
echo "   Found " . count($instructors) . " instructors\n";

$copied = 0;
foreach ($instructors as $instructor) {
    $columns = array_keys($instructor);
    $placeholders = array_fill(0, count($columns), '?');
    $sql = "INSERT OR REPLACE INTO instructors (" . implode(',', $columns) . ") VALUES (" . implode(',', $placeholders) . ")";
    $stmt = $target_pdo->prepare($sql);
    $stmt->execute(array_values($instructor));
    $copied++;
}
echo "   Copied: {$copied} instructors ✓\n";

// Copy articles data
echo "\n7. Copying articles data...\n";
$articles = $source_pdo->query("SELECT * FROM articles")->fetchAll(PDO::FETCH_ASSOC);
echo "   Found " . count($articles) . " articles\n";

$copied = 0;
foreach ($articles as $article) {
    $columns = array_keys($article);
    $placeholders = array_fill(0, count($columns), '?');
    $sql = "INSERT OR REPLACE INTO articles (" . implode(',', $columns) . ") VALUES (" . implode(',', $placeholders) . ")";
    $stmt = $target_pdo->prepare($sql);
    $stmt->execute(array_values($article));
    $copied++;
}
echo "   Copied: {$copied} articles ✓\n";

echo "\n=== Import Complete ===\n";
echo "\nVerification:\n";
echo "Parks: " . $target_pdo->query("SELECT COUNT(*) FROM parks")->fetchColumn() . "\n";
echo "Instructors: " . $target_pdo->query("SELECT COUNT(*) FROM instructors")->fetchColumn() . "\n";
echo "Articles: " . $target_pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn() . "\n";
?>
