<?php
/**
 * Check environment variables for admin login
 */
header('Content-Type: text/plain; charset=utf-8');

echo "=== Environment Variables Check ===\n\n";

$envReader = function($key) {
    $value = getenv($key);
    if ($value !== false && $value !== '') {
        return $value;
    }
    if (isset($_ENV[$key]) && $_ENV[$key] !== '') {
        return $_ENV[$key];
    }
    if (isset($_SERVER[$key]) && $_SERVER[$key] !== '') {
        return $_SERVER[$key];
    }
    return null;
};

$username = $envReader('ADMIN_USERNAME');
$password_hash = $envReader('ADMIN_PASSWORD_HASH');

echo "1. ADMIN_USERNAME: " . ($username ? "SET (value: {$username})" : "NOT SET") . "\n";
echo "2. ADMIN_PASSWORD_HASH: " . ($password_hash ? "SET (value: {$password_hash})" : "NOT SET") . "\n\n";

if ($password_hash) {
    echo "3. Testing password verification:\n";
    $test_password = 'skidiy2024';
    $verify_result = password_verify($test_password, $password_hash);
    echo "   password_verify('skidiy2024', hash) = " . ($verify_result ? "TRUE ✓" : "FALSE ✗") . "\n\n";

    // Show hash info
    $hash_info = password_get_info($password_hash);
    echo "4. Hash info:\n";
    echo "   Algorithm: " . $hash_info['algoName'] . "\n";
    echo "   Options: " . json_encode($hash_info['options']) . "\n\n";
}

// Generate a new hash for reference
echo "5. Generate new hash for 'skidiy2024':\n";
$new_hash = password_hash('skidiy2024', PASSWORD_BCRYPT, ['cost' => 12]);
echo "   New hash: {$new_hash}\n";
echo "   Verify test: " . (password_verify('skidiy2024', $new_hash) ? "TRUE ✓" : "FALSE ✗") . "\n";

echo "\n=== End Check ===\n";
?>
