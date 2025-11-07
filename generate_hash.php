<?php
/**
 * Generate password hash for admin login
 */
header('Content-Type: text/plain; charset=utf-8');

$password = $_GET['password'] ?? 'skidiy2024';

echo "=== Password Hash Generator ===\n\n";
echo "Password: {$password}\n\n";

$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
echo "Generated Hash:\n{$hash}\n\n";

echo "Verification Test:\n";
echo "password_verify('{$password}', hash) = " . (password_verify($password, $hash) ? "TRUE ✓" : "FALSE ✗") . "\n\n";

echo "=== To use this hash ===\n";
echo "Set Zeabur environment variable:\n";
echo "ADMIN_PASSWORD_HASH={$hash}\n";
?>
