<?php
/**
 * Debug FAQ Fetch - 調試 FAQ 抓取功能
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>FAQ 抓取調試</h1>";
echo "<style>body { font-family: monospace; padding: 20px; } pre { background: #f5f5f5; padding: 15px; overflow: auto; }</style>";

// 測試抓取單一 FAQ
$faqId = 'faq.general.009';
$lang = 'zh';
$url = "https://faq.diy.ski/faq/{$faqId}-{$lang}.html";

echo "<h2>步驟 1: 測試 cURL 請求</h2>";
echo "URL: <a href='$url' target='_blank'>$url</a><br><br>";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_USERAGENT, 'SkiDIY-Proxy/1.0');

$html = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP 狀態碼: <strong>$httpCode</strong><br>";
echo "HTML 長度: <strong>" . strlen($html) . "</strong> bytes<br>";
if ($error) {
    echo "cURL 錯誤: <span style='color: red;'>$error</span><br>";
}

if ($httpCode !== 200 || !$html) {
    die("<p style='color: red;'>❌ 無法抓取 FAQ 頁面</p>");
}

echo "<p style='color: green;'>✅ cURL 請求成功</p>";

// 顯示 HTML 片段
echo "<h2>步驟 2: HTML 內容預覽</h2>";
echo "<pre>" . htmlspecialchars(substr($html, 0, 1000)) . "\n...(truncated)</pre>";

// 測試 DOMDocument 解析
echo "<h2>步驟 3: DOMDocument 解析</h2>";

$dom = new DOMDocument();
libxml_use_internal_errors(true); // 抑制 HTML 解析警告
$loaded = @$dom->loadHTML('<?xml encoding="UTF-8">' . $html);
libxml_clear_errors();

echo "loadHTML 結果: " . ($loaded ? "✅ 成功" : "❌ 失敗") . "<br><br>";

$xpath = new DOMXPath($dom);

// 測試各種 XPath 查詢
echo "<h3>3.1 查找 h1 標籤</h3>";
$h1List = $xpath->query('//h1');
echo "找到 h1 標籤數量: <strong>" . $h1List->length . "</strong><br>";
if ($h1List->length > 0) {
    foreach ($h1List as $i => $h1) {
        echo "  h1[$i]: " . htmlspecialchars($h1->textContent) . "<br>";
    }
} else {
    echo "<span style='color: orange;'>嘗試其他方法...</span><br>";
    $h1Tags = $dom->getElementsByTagName('h1');
    echo "getElementsByTagName 找到: <strong>" . $h1Tags->length . "</strong><br>";
    if ($h1Tags->length > 0) {
        echo "  內容: " . htmlspecialchars($h1Tags->item(0)->textContent) . "<br>";
    }
}

echo "<h3>3.2 查找 class='badge' 的元素</h3>";
$badgeList = $xpath->query("//*[contains(@class, 'badge')]");
echo "找到 badge 數量: <strong>" . $badgeList->length . "</strong><br>";
if ($badgeList->length > 0) {
    foreach ($badgeList as $i => $badge) {
        echo "  badge[$i]: " . htmlspecialchars($badge->textContent) . "<br>";
    }
}

echo "<h3>3.3 查找 class='faq-content' 的元素</h3>";
$contentList = $xpath->query("//*[contains(@class, 'faq-content')]");
echo "找到 faq-content 數量: <strong>" . $contentList->length . "</strong><br>";
if ($contentList->length > 0) {
    $firstContent = $contentList->item(0);
    $paragraphs = $firstContent->getElementsByTagName('p');
    echo "  內含 p 標籤數量: " . $paragraphs->length . "<br>";
    if ($paragraphs->length > 0) {
        echo "  第一個 p 內容: " . htmlspecialchars($paragraphs->item(0)->textContent) . "<br>";
    }
}

echo "<h3>3.4 查找 Schema.org 資料</h3>";
$schemaList = $xpath->query('//script[@type="application/ld+json"]');
echo "找到 ld+json script 數量: <strong>" . $schemaList->length . "</strong><br>";
if ($schemaList->length > 0) {
    $schemaContent = $schemaList->item(0)->textContent;
    echo "  Schema 資料長度: " . strlen($schemaContent) . " bytes<br>";
    $schemaData = json_decode($schemaContent, true);
    if ($schemaData) {
        echo "  JSON 解析: ✅ 成功<br>";
        echo "  @type: " . ($schemaData['@type'] ?? 'N/A') . "<br>";
        echo "  mainEntity 數量: " . (count($schemaData['mainEntity'] ?? [])) . "<br>";
    } else {
        echo "  JSON 解析: ❌ 失敗<br>";
    }
}

// 測試使用正則表達式提取
echo "<h2>步驟 4: 備用方案（正則表達式）</h2>";

preg_match('/<h1[^>]*>(.*?)<\/h1>/s', $html, $h1Matches);
echo "正則提取 h1: " . (isset($h1Matches[1]) ? "✅ " . htmlspecialchars($h1Matches[1]) : "❌ 失敗") . "<br>";

preg_match('/<p[^>]*class="badge"[^>]*>(.*?)<\/p>/s', $html, $badgeMatches);
if (!isset($badgeMatches[1])) {
    preg_match('/class="badge"[^>]*>(.*?)</s', $html, $badgeMatches);
}
echo "正則提取 badge: " . (isset($badgeMatches[1]) ? "✅ " . htmlspecialchars($badgeMatches[1]) : "❌ 失敗") . "<br>";

preg_match('/<div class="faq-content">(.*?)<\/div>/s', $html, $contentMatches);
if (isset($contentMatches[1])) {
    preg_match('/<p[^>]*>(.*?)<\/p>/s', $contentMatches[1], $pMatches);
    echo "正則提取答案: " . (isset($pMatches[1]) ? "✅ " . htmlspecialchars(substr($pMatches[1], 0, 100)) . "..." : "❌ 失敗") . "<br>";
}

// 完整測試
echo "<h2>步驟 5: 完整測試（使用修正後的邏輯）</h2>";

require_once 'includes/faq_proxy.php';

echo "<pre>";
$faq = fetchFAQContent($faqId, $lang);
print_r($faq);
echo "</pre>";

if ($faq && !empty($faq['question'])) {
    echo "<p style='color: green; font-size: 20px;'>✅ FAQ 抓取成功！</p>";
} else {
    echo "<p style='color: red; font-size: 20px;'>❌ FAQ 抓取失敗</p>";
}
?>
