<?php
session_start();

header('Content-Type: text/plain; charset=UTF-8');

function mask($value) {
    if ($value === null) {
        return 'NULL';
    }
    $len = strlen($value);
    if ($len <= 4) {
        return str_repeat('*', $len);
    }
    return substr($value, 0, 4) . str_repeat('*', $len - 4);
}

$sources = [
    'getenv' => [
        'ADMIN_USERNAME' => getenv('ADMIN_USERNAME') ?: null,
        'ADMIN_PASSWORD_HASH' => getenv('ADMIN_PASSWORD_HASH') ?: null,
    ],
    '_ENV' => [
        'ADMIN_USERNAME' => $_ENV['ADMIN_USERNAME'] ?? null,
        'ADMIN_PASSWORD_HASH' => $_ENV['ADMIN_PASSWORD_HASH'] ?? null,
    ],
    '_SERVER' => [
        'ADMIN_USERNAME' => $_SERVER['ADMIN_USERNAME'] ?? null,
        'ADMIN_PASSWORD_HASH' => $_SERVER['ADMIN_PASSWORD_HASH'] ?? null,
    ],
];

echo "=== SKIDIYOG Admin Env Debug ===\n\n";
foreach ($sources as $label => $vars) {
    echo "[$label]\n";
    foreach ($vars as $key => $val) {
        echo sprintf("  %-22s: %s\n", $key, mask($val));
    }
    echo "\n";
}

echo "PHP version: " . phpversion() . "\n";
echo "Loaded via: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'CLI') . "\n";
echo "\n請部署後造訪 /bkAdmin/env-dump.php 並回傳輸出，完成後記得刪除此檔案。\n";
