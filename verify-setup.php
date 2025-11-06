<?php
/**
 * SKidiyog Environment Verification Script
 * Checks PHP version, extensions, database connection, and file permissions
 */

echo "=== SKidiyog Environment Verification ===\n\n";

// 1. PHP Version & SAPI
echo "=== PHP Configuration ===\n";
echo "PHP Version: " . phpversion() . "\n";
echo "PHP SAPI: " . php_sapi_name() . "\n";
echo "OS: " . php_uname() . "\n\n";

// 2. Required Extensions
echo "=== Required Extensions ===\n";
$extensions = ['mysqli', 'json', 'curl', 'mbstring', 'openssl'];
$all_loaded = true;

foreach ($extensions as $ext) {
    $status = extension_loaded($ext) ? "✓ Loaded" : "✗ Missing";
    echo "Extension $ext: $status\n";
    if (!extension_loaded($ext)) {
        $all_loaded = false;
    }
}
echo "\n";

// 3. Database Connection
echo "=== Database Connection Test ===\n";

// Get database credentials from environment or db.class.php constants
$db_error = null;

// Try to get credentials from environment variables
$db_host = getenv('DB_HOST') ?: 'skidiy-rds-master.cgseduwrbkzc.ap-northeast-1.rds.amazonaws.com';
$db_user = getenv('DB_USER') ?: 'dba';
$db_pass = getenv('DB_PASS') ?: 'dba_Skidiy66';
$db_name = getenv('DB_NAME') ?: 'skidiy';

try {
    $conn = @mysqli_connect($db_host, $db_user, $db_pass, $db_name);

    if ($conn) {
        echo "✓ MySQL Connected Successfully\n";
        echo "  Host: " . $db_host . "\n";
        echo "  Database: " . $db_name . "\n";
        echo "  Server Version: " . mysqli_get_server_info($conn) . "\n";

        // Check tables
        $result = mysqli_query($conn, "SHOW TABLES");
        $table_count = mysqli_num_rows($result);
        echo "  Tables Found: " . $table_count . "\n";

        mysqli_close($conn);
    } else {
        $db_error = mysqli_connect_error();
        echo "✗ Connection Failed\n";
        echo "  Error: " . $db_error . "\n";
        echo "  Check: DB_HOST, DB_USER, DB_PASS, DB_NAME\n";
    }
} catch (Exception $e) {
    $db_error = $e->getMessage();
    echo "✗ Exception: " . $db_error . "\n";
}

echo "\n";

// 4. Environment Variables (Zeabur)
echo "=== Environment Variables ===\n";
echo "DB_HOST: " . (getenv('DB_HOST') ? getenv('DB_HOST') : "not set") . "\n";
echo "DB_USER: " . (getenv('DB_USER') ? getenv('DB_USER') : "not set") . "\n";
echo "DB_PASS: " . (getenv('DB_PASS') ? "***" : "not set") . "\n";
echo "DB_NAME: " . (getenv('DB_NAME') ? getenv('DB_NAME') : "not set") . "\n";
echo "SECRET_KEY: " . (getenv('SECRET_KEY') ? "***" : "not set") . "\n";
echo "\n";

// 5. File Permissions
echo "=== File & Directory Permissions ===\n";
$directories = [
    'includes/',
    'database/',
    'assets/',
    'bkAdmin/',
    'uploads/',
];

foreach ($directories as $dir) {
    $path = __DIR__ . '/' . $dir;
    if (is_dir($path)) {
        $readable = is_readable($path) ? "✓" : "✗";
        echo "$readable Directory: $dir\n";
    } else {
        echo "⚠ Directory not found: $dir\n";
    }
}

echo "\n";

// 6. Critical Files
echo "=== Critical Files ===\n";
$files = [
    'includes/sdk.php',
    'includes/config.php',
    'database/parks.json',
    'database/articles.json',
    '.htaccess',
];

foreach ($files as $file) {
    $path = __DIR__ . '/' . $file;
    $exists = file_exists($path) ? "✓" : "✗";
    echo "$exists File: $file\n";
}

echo "\n";

// 7. Summary
echo "=== Verification Summary ===\n";
if ($all_loaded && $config_loaded && empty($db_error)) {
    echo "✓ Environment Configured Correctly\n";
    echo "✓ All extensions loaded\n";
    echo "✓ Database connection successful\n";
    echo "✓ System ready for deployment\n";
} else {
    echo "⚠ Issues Detected:\n";
    if (!$all_loaded) echo "  • Some PHP extensions are missing\n";
    if (!$config_loaded) echo "  • Config file not found\n";
    if (!empty($db_error)) echo "  • Database connection failed\n";
    echo "\nPlease fix the issues above before proceeding.\n";
}

echo "\n=== End of Verification ===\n";
?>
