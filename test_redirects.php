<?php
/**
 * Test 301 Redirects
 *
 * Usage: php test_redirects.php
 * Or visit in browser: https://skidiyog.zeabur.app/test_redirects.php
 */

require_once __DIR__ . '/includes/article_mapping.php';

echo "<!DOCTYPE html>\n<html>\n<head>\n";
echo "<meta charset='UTF-8'>\n";
echo "<meta name='robots' content='noindex, nofollow'>\n";
echo "<title>301 Redirect Test Results</title>\n";
echo "<style>
body { font-family: Arial, sans-serif; max-width: 1200px; margin: 20px auto; padding: 20px; }
h1 { color: #2c3e50; border-bottom: 3px solid #3498db; padding-bottom: 10px; }
h2 { color: #34495e; margin-top: 30px; }
table { width: 100%; border-collapse: collapse; margin: 20px 0; }
th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
th { background-color: #3498db; color: white; }
tr:hover { background-color: #f5f5f5; }
.success { color: #27ae60; font-weight: bold; }
.error { color: #e74c3c; font-weight: bold; }
.info { background-color: #ecf0f1; padding: 15px; margin: 20px 0; border-left: 4px solid #3498db; }
code { background-color: #f8f9fa; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
a { color: #3498db; text-decoration: none; }
a:hover { text-decoration: underline; }
</style>
</head>\n<body>\n";

echo "<h1>🔍 301 Redirect Test Results</h1>\n";
echo "<p class='info'>✅ <strong>Purpose:</strong> Verify that old article URLs redirect correctly to new SEO-friendly URLs</p>\n";

// Test article mapping loading
echo "<h2>📊 Article Mapping Status</h2>\n";
if (isset($ARTICLE_MAPPING) && !empty($ARTICLE_MAPPING)) {
    echo "<p class='success'>✓ Article mapping loaded successfully</p>\n";
    echo "<p>Total articles in mapping: <strong>" . count($ARTICLE_MAPPING) . "</strong></p>\n";
} else {
    echo "<p class='error'>✗ Failed to load article mapping</p>\n";
    exit();
}

// Test redirect functions
echo "<h2>🧪 Function Tests</h2>\n";
echo "<table>\n";
echo "<tr><th>Test</th><th>Function</th><th>Result</th><th>Status</th></tr>\n";

$tests = [
    ['articleExists(1)', articleExists(1), 'true', 'Article 1 exists'],
    ['articleExists(999)', articleExists(999), 'false', 'Article 999 does not exist'],
    ['getArticleTitle(1)', getArticleTitle(1), '日本自助滑雪春雪篇，四月也可以滑雪', 'Get title'],
    ['getArticleOldUrl(1)', getArticleOldUrl(1), 'article.php?idx=1', 'Get old URL'],
    ['getArticleNewUrl(1)', getArticleNewUrl(1), '/article/日本自助滑雪春雪篇，四月也可以滑雪-1', 'Get new URL'],
];

foreach ($tests as $test) {
    $funcName = $test[0];
    $result = $test[1];
    $expected = $test[2];
    $description = $test[3];

    $status = ($result == $expected || (is_bool($result) && $result == ($expected === 'true')))
        ? "<span class='success'>✓ PASS</span>"
        : "<span class='error'>✗ FAIL</span>";

    echo "<tr>\n";
    echo "<td>$description</td>\n";
    echo "<td><code>$funcName</code></td>\n";
    echo "<td><code>" . htmlspecialchars(var_export($result, true)) . "</code></td>\n";
    echo "<td>$status</td>\n";
    echo "</tr>\n";
}

echo "</table>\n";

// List all article redirects
echo "<h2>📝 All Article Redirects</h2>\n";
echo "<p>Click on old URLs to test the redirect (should show 301 in browser dev tools)</p>\n";
echo "<table>\n";
echo "<tr><th>IDX</th><th>Title</th><th>Old URL</th><th>New URL</th><th>Test</th></tr>\n";

ksort($ARTICLE_MAPPING);
foreach ($ARTICLE_MAPPING as $idx => $data) {
    echo "<tr>\n";
    echo "<td><strong>$idx</strong></td>\n";
    echo "<td>" . htmlspecialchars($data['title']) . "</td>\n";
    echo "<td><code><a href='article.php?idx=$idx' target='_blank'>" . htmlspecialchars($data['old_url']) . "</a></code></td>\n";
    echo "<td><code>" . htmlspecialchars($data['new_url']) . "</code></td>\n";
    echo "<td><a href='article.php?idx=$idx' target='_blank'>🔗 Test</a></td>\n";
    echo "</tr>\n";
}

echo "</table>\n";

// Footer
echo "<div class='info' style='margin-top: 40px;'>\n";
echo "<p><strong>📌 How to verify 301 redirects:</strong></p>\n";
echo "<ol>\n";
echo "<li>Open browser Developer Tools (F12) → Network tab</li>\n";
echo "<li>Click on any \"Test\" link above</li>\n";
echo "<li>Check the Network tab - you should see:<br>\n";
echo "   - First request: <code>article.php?idx=X</code> with status <strong>301 Moved Permanently</strong><br>\n";
echo "   - Second request: New URL with status <strong>200 OK</strong> (or 404 if new route not implemented yet)</li>\n";
echo "</ol>\n";
echo "<p><strong>⚠️ Note:</strong> The new URLs (<code>/article/{slug}-{idx}</code>) will return 404 until the new routing is implemented. The important part is that the <strong>301 redirect happens</strong>.</p>\n";
echo "</div>\n";

echo "<p style='text-align: center; color: #7f8c8d; margin-top: 40px;'>Generated: " . date('Y-m-d H:i:s') . "</p>\n";

echo "</body>\n</html>";
