<?php
/**
 * Debug database status
 */
require_once(__DIR__ . '/includes/db.class.php');

header('Content-Type: text/plain; charset=utf-8');

echo "=== Database Debug Info ===\n\n";

echo "1. Database file path:\n";
echo "   " . DB_FILE . "\n\n";

echo "2. File exists: ";
echo file_exists(DB_FILE) ? "YES" : "NO";
echo "\n\n";

if (file_exists(DB_FILE)) {
    echo "3. File permissions:\n";
    echo "   Readable: " . (is_readable(DB_FILE) ? "YES" : "NO") . "\n";
    echo "   Writable: " . (is_writable(DB_FILE) ? "YES" : "NO") . "\n";
    echo "   Size: " . filesize(DB_FILE) . " bytes\n\n";
}

echo "4. Parent directory:\n";
$dir = dirname(DB_FILE);
echo "   Path: {$dir}\n";
echo "   Exists: " . (file_exists($dir) ? "YES" : "NO") . "\n";
echo "   Writable: " . (is_writable($dir) ? "YES" : "NO") . "\n\n";

echo "5. Test database connection:\n";
$db = new DB();
if ($db->error) {
    echo "   ERROR: " . $db->error . "\n";
} else {
    echo "   Connection: OK\n";
}

echo "\n6. Test query:\n";
$result = $db->QUERY('SELECT', "SELECT name, substr(about, 1, 50) as about_preview FROM parks WHERE name='naeba'");
if ($db->error) {
    echo "   ERROR: " . $db->error . "\n";
} else {
    echo "   Result: " . json_encode($result, JSON_UNESCAPED_UNICODE) . "\n";
}

echo "\n7. Test write:\n";
$test_data = array('about' => '<pre>【DEBUG ' . date('Y-m-d H:i:s') . '】TEST' . "\n</pre>");
$test_where = array('name' => 'naeba');
$update_result = $db->UPDATE('parks', $test_data, $test_where);
if ($db->error) {
    echo "   ERROR: " . $db->error . "\n";
} else {
    echo "   Rows affected: " . $update_result . "\n";
}

echo "\n8. Verify write:\n";
$verify = $db->QUERY('SELECT', "SELECT substr(about, 1, 80) as about_preview FROM parks WHERE name='naeba'");
if ($db->error) {
    echo "   ERROR: " . $db->error . "\n";
} else {
    echo "   Result: " . json_encode($verify, JSON_UNESCAPED_UNICODE) . "\n";
}

echo "\n=== End Debug ===\n";
?>
