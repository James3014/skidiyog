<?php
/**
 * Test FAQ File Access
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>FAQ File Access Test</h1>";
echo "<style>body { font-family: monospace; padding: 20px; } pre { background: #f5f5f5; padding: 15px; }</style>";

$faqId = 'faq.general.009';
$lang = 'zh';
$filePath = __DIR__ . "/faq/{$faqId}-{$lang}.html";

echo "<h2>File Path</h2>";
echo "<pre>$filePath</pre>";

echo "<h2>File Exists?</h2>";
$exists = file_exists($filePath);
echo $exists ? "✅ Yes" : "❌ No";
echo "<br>";

if ($exists) {
    echo "<h2>File Size</h2>";
    echo filesize($filePath) . " bytes<br>";

    echo "<h2>File Contents (first 1000 chars)</h2>";
    $content = file_get_contents($filePath);
    echo "<pre>" . htmlspecialchars(substr($content, 0, 1000)) . "...</pre>";

    echo "<h2>Regex Extraction Test</h2>";

    // Test h1
    if (preg_match('/<h1>(.*?)<\/h1>/s', $content, $matches)) {
        echo "✅ h1 found: <strong>" . htmlspecialchars($matches[1]) . "</strong><br>";
    } else {
        echo "❌ h1 not found<br>";
    }

    // Test badge
    if (preg_match('/<p class="badge">(.*?)<\/p>/s', $content, $matches)) {
        echo "✅ badge found: <strong>" . htmlspecialchars($matches[1]) . "</strong><br>";
    } else {
        echo "❌ badge not found<br>";
    }

    // Test faq-content
    if (preg_match('/<div class="faq-content">(.*?)<\/div>/s', $content, $matches)) {
        echo "✅ faq-content found: <strong>" . htmlspecialchars(substr($matches[1], 0, 100)) . "...</strong><br>";
    } else {
        echo "❌ faq-content not found<br>";
    }

    // Test Schema
    if (preg_match('/<script type="application\/ld\+json">(.*?)<\/script>/s', $content, $matches)) {
        echo "✅ Schema.org found<br>";
        $schema = json_decode($matches[1], true);
        if ($schema) {
            echo "  Type: " . ($schema['@type'] ?? 'N/A') . "<br>";
            echo "  Question: " . ($schema['mainEntity'][0]['name'] ?? 'N/A') . "<br>";
        }
    } else {
        echo "❌ Schema.org not found<br>";
    }
}
?>
