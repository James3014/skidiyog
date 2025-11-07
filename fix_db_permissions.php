#!/usr/bin/env php
<?php
/**
 * One-time script to fix database file permissions
 * Run this once after deployment to fix readonly database issue
 */

require_once(__DIR__ . '/includes/db.class.php');

echo "=== Database Permission Fix Script ===\n\n";

$db_file = DB_FILE;
$db_dir = dirname($db_file);

echo "1. Database file: {$db_file}\n";
echo "2. Parent directory: {$db_dir}\n\n";

// Check current status
echo "3. Current status:\n";
echo "   File exists: " . (file_exists($db_file) ? "YES" : "NO") . "\n";
echo "   File readable: " . (is_readable($db_file) ? "YES" : "NO") . "\n";
echo "   File writable: " . (is_writable($db_file) ? "YES" : "NO") . "\n";
echo "   Dir writable: " . (is_writable($db_dir) ? "YES" : "NO") . "\n\n";

// Get current permissions
if (file_exists($db_file)) {
    $perms = fileperms($db_file);
    echo "   Current file permissions: " . substr(sprintf('%o', $perms), -4) . "\n\n";
}

// Try multiple methods to fix permissions
echo "4. Attempting permission fixes:\n";

// Method 1: chmod with full permissions
$chmod_result = @chmod($db_file, 0666);
echo "   Method 1 (chmod 0666): " . ($chmod_result ? "SUCCESS" : "FAILED") . "\n";
clearstatcache();

// Method 2: Use shell command
$shell_result = @shell_exec("chmod 666 " . escapeshellarg($db_file) . " 2>&1");
echo "   Method 2 (shell chmod): " . ($shell_result === null ? "SUCCESS" : "OUTPUT: {$shell_result}") . "\n";
clearstatcache();

// Method 3: Copy and replace
if (file_exists($db_file)) {
    $backup_file = $db_file . '.backup';
    $temp_file = $db_file . '.temp';

    if (copy($db_file, $backup_file)) {
        echo "   Method 3a (backup created): SUCCESS\n";

        if (copy($db_file, $temp_file)) {
            @chmod($temp_file, 0666);
            if (@unlink($db_file) && @rename($temp_file, $db_file)) {
                echo "   Method 3b (copy-replace): SUCCESS\n";
            } else {
                echo "   Method 3b (copy-replace): FAILED\n";
                @unlink($temp_file);
            }
        } else {
            echo "   Method 3b (temp copy): FAILED\n";
        }
    } else {
        echo "   Method 3 (backup): FAILED\n";
    }
}
clearstatcache();

echo "\n5. Final status:\n";
echo "   File writable: " . (is_writable($db_file) ? "YES ✅" : "NO ❌") . "\n";

if (file_exists($db_file)) {
    $perms = fileperms($db_file);
    echo "   Final file permissions: " . substr(sprintf('%o', $perms), -4) . "\n";
}

// Test write
echo "\n6. Testing database write:\n";
$db = new DB();
if ($db->error) {
    echo "   Connection ERROR: {$db->error}\n";
} else {
    $test_data = array('about' => '<pre>【PERMISSION FIX TEST ' . date('Y-m-d H:i:s') . '】</pre>');
    $test_where = array('name' => 'naeba');
    $result = $db->UPDATE('parks', $test_data, $test_where);

    if ($db->error) {
        echo "   Write ERROR: {$db->error}\n";
    } else {
        echo "   Write SUCCESS: {$result} rows affected ✅\n";
    }
}

echo "\n=== End Permission Fix ===\n";
?>
