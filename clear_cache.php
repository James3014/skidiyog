<?php
/**
 * Clear PHP opcache and show current database path
 */
header('Content-Type: text/plain; charset=utf-8');

echo "=== Cache Clear & Path Check ===\n\n";

// Clear opcache
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "1. Opcache cleared: YES\n\n";
} else {
    echo "1. Opcache: NOT AVAILABLE\n\n";
}

// Show what the code SHOULD do
echo "2. Database path logic:\n";
if (is_dir('/data') && is_writable('/data')) {
    echo "   /data exists and writable: YES\n";
    echo "   Should use: /data/skidiyog.db\n";
} else {
    echo "   /data exists and writable: NO\n";
    echo "   Should use: ./data/skidiyog.db\n";
}

echo "\n3. Loading db.class.php...\n";
require_once(__DIR__ . '/includes/db.class.php');

echo "   Actual DB_FILE: " . DB_FILE . "\n";
echo "   File exists: " . (file_exists(DB_FILE) ? "YES" : "NO") . "\n";
if (file_exists(DB_FILE)) {
    echo "   File writable: " . (is_writable(DB_FILE) ? "YES" : "NO") . "\n";
}

echo "\n=== End Cache Clear ===\n";
?>
