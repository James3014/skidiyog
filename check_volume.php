<?php
/**
 * Check Zeabur Volume mount status
 */
header('Content-Type: text/plain; charset=utf-8');

echo "=== Zeabur Volume Check ===\n\n";

// Check /data directory
echo "1. /data directory:\n";
echo "   Exists: " . (is_dir('/data') ? "YES" : "NO") . "\n";
if (is_dir('/data')) {
    echo "   Writable: " . (is_writable('/data') ? "YES" : "NO") . "\n";
    $perms = fileperms('/data');
    echo "   Permissions: " . substr(sprintf('%o', $perms), -4) . "\n";
}

// Check if /data is a mount point
echo "\n2. Mount check:\n";
$df_output = shell_exec('df -h /data 2>&1');
echo "   df -h /data:\n";
echo "   " . str_replace("\n", "\n   ", trim($df_output)) . "\n";

// Check database file
$db_file = '/data/skidiyog.db';
echo "\n3. Database file:\n";
echo "   Path: $db_file\n";
echo "   Exists: " . (file_exists($db_file) ? "YES" : "NO") . "\n";

if (file_exists($db_file)) {
    echo "   Size: " . filesize($db_file) . " bytes\n";
    echo "   Readable: " . (is_readable($db_file) ? "YES" : "NO") . "\n";
    echo "   Writable: " . (is_writable($db_file) ? "YES" : "NO") . "\n";
    $perms = fileperms($db_file);
    echo "   Permissions: " . substr(sprintf('%o', $perms), -4) . "\n";

    // Check owner
    $stat = stat($db_file);
    echo "   Owner UID: " . $stat['uid'] . "\n";
    echo "   Owner GID: " . $stat['gid'] . "\n";
}

// Check current process user
echo "\n4. Current process:\n";
echo "   User: " . get_current_user() . "\n";
echo "   UID: " . getmyuid() . "\n";
echo "   GID: " . getmygid() . "\n";

// Check local data directory
$local_db = __DIR__ . '/data/skidiyog.db';
echo "\n5. Local ./data directory:\n";
echo "   Path: " . dirname($local_db) . "\n";
echo "   Exists: " . (is_dir(dirname($local_db)) ? "YES" : "NO") . "\n";
if (file_exists($local_db)) {
    echo "   Local DB exists: YES\n";
    echo "   Local DB size: " . filesize($local_db) . " bytes\n";
}

// Try to create a test file in /data
echo "\n6. Write test to /data:\n";
$test_file = '/data/test_write_' . time() . '.txt';
$write_result = @file_put_contents($test_file, 'test');
if ($write_result !== false) {
    echo "   Write to /data: SUCCESS\n";
    @unlink($test_file);
} else {
    echo "   Write to /data: FAILED\n";
    echo "   Error: " . error_get_last()['message'] . "\n";
}

echo "\n=== End Volume Check ===\n";
?>
