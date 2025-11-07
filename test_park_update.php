#!/usr/bin/env php
<?php
/**
 * Test script to verify park update functionality
 */

require_once(__DIR__ . '/includes/db.class.php');
require_once(__DIR__ . '/includes/mj.class.php');

echo "=== Testing PARKS Update Functionality ===\n\n";

// Initialize PARKS class
$PARKS = new PARKS();

// Test data
$park_name = 'naeba';
$test_content = "\n【測試更新 " . date('Y-m-d H:i:s') . "】\n這是直接測試腳本的更新內容。\n\n";

echo "1. Reading current 'about' content for {$park_name}...\n";
$current_content = $PARKS->info($park_name, 'about');
if ($current_content) {
    echo "   Current length: " . strlen($current_content) . " characters\n";
    echo "   Preview: " . substr($current_content, 0, 100) . "...\n\n";
} else {
    echo "   ⚠️  No content found!\n\n";
}

echo "2. Attempting to update with test content...\n";
$update_data = array('content' => $test_content . $current_content);
$result = $PARKS->update($park_name, 'about', $update_data);

if ($result !== false && $result !== null) {
    echo "   ✅ Update returned: {$result} rows affected\n\n";
} else {
    echo "   ❌ Update failed or returned false/null\n\n";
}

echo "3. Re-reading content to verify...\n";
$new_content = $PARKS->info($park_name, 'about');
if ($new_content) {
    $has_test_text = strpos($new_content, '【測試更新') !== false;
    echo "   New length: " . strlen($new_content) . " characters\n";
    echo "   Test text found: " . ($has_test_text ? "YES ✅" : "NO ❌") . "\n";
    echo "   Preview: " . substr($new_content, 0, 150) . "...\n\n";

    if ($has_test_text) {
        echo "✅ SUCCESS: Park update is working!\n";
        exit(0);
    } else {
        echo "❌ FAILURE: Update didn't persist!\n";
        exit(1);
    }
} else {
    echo "   ❌ Failed to read content after update\n\n";
    exit(1);
}
?>
