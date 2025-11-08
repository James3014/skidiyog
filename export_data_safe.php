<?php
/**
 * Export data from local database as PHP arrays
 * This creates a PHP file that can be executed on Zeabur to import data
 */
$source_db_file = __DIR__ . '/data/skidiyog.db';

if (!file_exists($source_db_file)) {
    die("ERROR: Source database not found at {$source_db_file}\n");
}

echo "Exporting data from {$source_db_file}...\n";

$pdo = new PDO('sqlite:' . $source_db_file);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Export parks
$parks = $pdo->query("SELECT * FROM parks")->fetchAll(PDO::FETCH_ASSOC);
echo "Found " . count($parks) . " parks\n";

// Export instructors
$instructors = $pdo->query("SELECT * FROM instructors")->fetchAll(PDO::FETCH_ASSOC);
echo "Found " . count($instructors) . " instructors\n";

// Export articles
$articles = $pdo->query("SELECT * FROM articles")->fetchAll(PDO::FETCH_ASSOC);
echo "Found " . count($articles) . " articles\n";

// Create PHP import file
$output_file = __DIR__ . '/import_from_arrays.php';
$php_code = "<?php\n";
$php_code .= "/**\n";
$php_code .= " * Auto-generated data import file\n";
$php_code .= " * Generated: " . date('Y-m-d H:i:s') . "\n";
$php_code .= " */\n";
$php_code .= "header('Content-Type: text/plain; charset=utf-8');\n\n";
$php_code .= "echo \"=== Database Import from Arrays ===\\n\\n\";\n\n";

// Add data arrays
$php_code .= "// Parks data\n";
$php_code .= "\$parks_data = " . var_export($parks, true) . ";\n\n";

$php_code .= "// Instructors data\n";
$php_code .= "\$instructors_data = " . var_export($instructors, true) . ";\n\n";

$php_code .= "// Articles data\n";
$php_code .= "\$articles_data = " . var_export($articles, true) . ";\n\n";

// Add import logic
$php_code .= <<<'IMPORT_LOGIC'
$target_db_file = '/data/skidiyog.db';
echo "Target database: {$target_db_file}\n";
echo "Exists: " . (file_exists($target_db_file) ? "YES" : "NO") . "\n\n";

try {
    $pdo = new PDO('sqlite:' . $target_db_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Import parks
    echo "Importing parks...\n";
    $imported = 0;
    foreach ($parks_data as $park) {
        $columns = array_keys($park);
        $placeholders = array_fill(0, count($columns), '?');
        $sql = "INSERT OR REPLACE INTO parks (" . implode(',', $columns) . ") VALUES (" . implode(',', $placeholders) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_values($park));
        $imported++;
    }
    echo "  Imported: {$imported} parks ✓\n";

    // Import instructors
    echo "Importing instructors...\n";
    $imported = 0;
    foreach ($instructors_data as $instructor) {
        $columns = array_keys($instructor);
        $placeholders = array_fill(0, count($columns), '?');
        $sql = "INSERT OR REPLACE INTO instructors (" . implode(',', $columns) . ") VALUES (" . implode(',', $placeholders) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_values($instructor));
        $imported++;
    }
    echo "  Imported: {$imported} instructors ✓\n";

    // Import articles
    echo "Importing articles...\n";
    $imported = 0;
    foreach ($articles_data as $article) {
        $columns = array_keys($article);
        $placeholders = array_fill(0, count($columns), '?');
        $sql = "INSERT OR REPLACE INTO articles (" . implode(',', $columns) . ") VALUES (" . implode(',', $placeholders) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_values($article));
        $imported++;
    }
    echo "  Imported: {$imported} articles ✓\n";

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

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
?>
IMPORT_LOGIC;

file_put_contents($output_file, $php_code);

echo "\nExport complete!\n";
echo "Created: {$output_file}\n";
echo "File size: " . filesize($output_file) . " bytes\n";
?>
