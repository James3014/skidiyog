<?php
/**
 * FAQ Proxy Component - 透過伺服器端代理載入 faq.diy.ski 的 FAQ
 *
 * 解決 CORS 問題，同時保留 Schema.org 結構化資料以利 SEO
 *
 * Usage:
 * require_once 'includes/faq_proxy.php';
 * renderFAQProxy(['faq.general.009', 'faq.general.010'], 'zh');
 *
 * @param array $faqIds FAQ ID 陣列
 * @param string $lang 語言代碼 (zh, en, th)
 */

function renderFAQProxy($faqIds, $lang = 'zh') {
    if (empty($faqIds)) {
        return;
    }

    $faqs = [];
    foreach ($faqIds as $faqId) {
        $faq = fetchFAQContent($faqId, $lang);
        if ($faq) {
            $faqs[] = $faq;
        }
    }

    if (empty($faqs)) {
        echo '<p style="color: #e74c3c;">無法載入 FAQ 內容</p>';
        return;
    }

    // 注入 Schema.org 結構化資料到 <head>
    injectFAQSchema($faqs);

    // 渲染 FAQ 區塊
    renderFAQBlocks($faqs, $lang);
}

/**
 * 透過 cURL 抓取 FAQ 內容（繞過 CORS）
 */
function fetchFAQContent($faqId, $lang) {
    $url = "https://faq.diy.ski/faq/{$faqId}-{$lang}.html";

    // 檢查快取（如果有 APCu）
    if (function_exists('apcu_fetch')) {
        $cacheKey = "faq_{$faqId}_{$lang}";
        $cached = apcu_fetch($cacheKey);
        if ($cached !== false) {
            return $cached;
        }
    }

    // 使用 cURL 抓取內容
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_USERAGENT, 'SkiDIY-Proxy/1.0');

    $html = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200 || !$html) {
        error_log("Failed to fetch FAQ {$faqId}: HTTP {$httpCode}");
        return null;
    }

    // 解析 HTML - 使用更健壯的方法
    $dom = new DOMDocument();
    libxml_use_internal_errors(true); // 抑制 HTML5 警告
    $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
    libxml_clear_errors();

    $xpath = new DOMXPath($dom);

    // 提取問題 - 多種方法嘗試
    $question = '';

    // 方法 1: XPath
    $h1 = $xpath->query('//h1')->item(0);
    if ($h1) {
        $question = trim($h1->textContent);
    }

    // 方法 2: getElementsByTagName (備用)
    if (empty($question)) {
        $h1Tags = $dom->getElementsByTagName('h1');
        if ($h1Tags->length > 0) {
            $question = trim($h1Tags->item(0)->textContent);
        }
    }

    // 方法 3: 正則表達式 (最後備用)
    if (empty($question)) {
        if (preg_match('/<h1[^>]*>(.*?)<\/h1>/s', $html, $matches)) {
            $question = trim(strip_tags($matches[1]));
        }
    }

    // 提取分類標籤
    $badge = '';
    $badgeElement = $xpath->query('//*[contains(@class, "badge")]')->item(0);
    if ($badgeElement) {
        $badge = trim($badgeElement->textContent);
    }

    // 備用方法
    if (empty($badge) && preg_match('/class=["\']badge["\'][^>]*>(.*?)</s', $html, $matches)) {
        $badge = trim(strip_tags($matches[1]));
    }

    // 提取答案
    $answer = '';

    // 方法 1: XPath 查找 faq-content 內的 p 標籤
    $answerElement = $xpath->query('//*[contains(@class, "faq-content")]//p')->item(0);
    if ($answerElement) {
        $answer = $dom->saveHTML($answerElement);
    }

    // 方法 2: 查找所有 class="card" section 內的第一個 p
    if (empty($answer)) {
        $sections = $xpath->query('//section[contains(@class, "card")]');
        if ($sections->length > 0) {
            $firstSection = $sections->item(0);
            $paragraphs = $firstSection->getElementsByTagName('p');
            if ($paragraphs->length > 0) {
                $answer = $dom->saveHTML($paragraphs->item(0));
            }
        }
    }

    // 方法 3: 正則表達式提取
    if (empty($answer)) {
        if (preg_match('/<div[^>]*class="faq-content"[^>]*>(.*?)<\/div>/s', $html, $matches)) {
            if (preg_match('/<p[^>]*>(.*?)<\/p>/s', $matches[1], $pMatches)) {
                $answer = '<p>' . $pMatches[1] . '</p>';
            }
        }
    }

    // 提取 Schema.org 資料
    $schemaData = null;
    $schemaScript = $xpath->query('//script[@type="application/ld+json"]')->item(0);
    if ($schemaScript) {
        $schemaJson = trim($schemaScript->textContent);
        $schemaData = json_decode($schemaJson, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("FAQ {$faqId}: Schema JSON parse error - " . json_last_error_msg());
            $schemaData = null;
        }
    }

    $faq = [
        'id' => $faqId,
        'question' => $question,
        'answer' => $answer,
        'badge' => $badge,
        'url' => "https://faq.diy.ski/faq/{$faqId}?lang={$lang}",
        'schemaData' => $schemaData
    ];

    // 快取 1 小時
    if (function_exists('apcu_store')) {
        apcu_store($cacheKey, $faq, 3600);
    }

    return $faq;
}

/**
 * 注入 Schema.org 結構化資料到 <head>
 */
function injectFAQSchema($faqs) {
    $mainEntity = [];

    foreach ($faqs as $faq) {
        if (isset($faq['schemaData']['mainEntity'])) {
            $mainEntity = array_merge($mainEntity, $faq['schemaData']['mainEntity']);
        }
    }

    if (empty($mainEntity)) {
        return;
    }

    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => $mainEntity
    ];

    echo '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
}

/**
 * 渲染 FAQ 區塊
 */
function renderFAQBlocks($faqs, $lang) {
    ?>
    <style>
        .faq-proxy-container {
            margin: 40px 0; background: #f8f9fa;
            border-radius: 8px; padding: 30px;
        }
        .faq-proxy-header {
            color: #2c3e50; font-size: 28px; margin-bottom: 30px;
            padding-bottom: 15px; border-bottom: 3px solid #3498db;
        }
        .faq-proxy-item {
            margin-bottom: 20px; background: white; border-radius: 6px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1); overflow: hidden;
            transition: box-shadow 0.3s ease;
        }
        .faq-proxy-item:hover { box-shadow: 0 4px 8px rgba(0,0,0,0.15); }
        .faq-proxy-badge {
            display: inline-block; background: #e3f2fd; color: #1565c0;
            padding: 8px 16px; margin: 15px 0 0 20px; border-radius: 12px; font-size: 14px;
        }
        .faq-proxy-question {
            padding: 20px; cursor: pointer; display: flex;
            justify-content: space-between; align-items: center;
            font-weight: 500; font-size: 18px; color: #2c3e50;
            transition: background-color 0.3s ease;
        }
        .faq-proxy-question:hover { background: #ecf0f1; }
        .faq-proxy-question::before {
            content: "Q"; display: inline-block; width: 30px; height: 30px;
            line-height: 30px; text-align: center; background: #3498db;
            color: white; border-radius: 50%; margin-right: 15px;
            font-weight: bold; flex-shrink: 0;
        }
        .faq-proxy-icon {
            font-size: 24px; color: #3498db;
            transition: transform 0.3s ease; flex-shrink: 0;
        }
        .faq-proxy-item.active .faq-proxy-icon { transform: rotate(180deg); }
        .faq-proxy-answer {
            max-height: 0; overflow: hidden;
            transition: max-height 0.3s ease, padding 0.3s ease;
            padding: 0 20px;
        }
        .faq-proxy-item.active .faq-proxy-answer {
            max-height: 2000px; padding: 0 20px 20px 65px;
        }
        .faq-proxy-answer p { line-height: 1.8; color: #555; margin: 10px 0; }
        .faq-proxy-link {
            display: inline-block; margin-top: 10px; color: #3498db;
            text-decoration: none; font-size: 14px;
        }
        .faq-proxy-link:hover { text-decoration: underline; }
    </style>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <div class="faq-proxy-container">
        <h3 class="faq-proxy-header">常見問題</h3>

        <?php foreach ($faqs as $index => $faq): ?>
        <div class="faq-proxy-item" data-faq-index="<?= $index ?>">
            <?php if ($faq['badge']): ?>
            <div class="faq-proxy-badge"><?= htmlspecialchars($faq['badge']) ?></div>
            <?php endif; ?>

            <div class="faq-proxy-question">
                <span><?= htmlspecialchars($faq['question']) ?></span>
                <i class="material-icons faq-proxy-icon">expand_more</i>
            </div>

            <div class="faq-proxy-answer">
                <div><?= $faq['answer'] ?></div>
                <a href="<?= htmlspecialchars($faq['url']) ?>" class="faq-proxy-link" target="_blank" rel="noopener">
                    查看完整說明 →
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const faqItems = document.querySelectorAll('.faq-proxy-item');

        faqItems.forEach(item => {
            const question = item.querySelector('.faq-proxy-question');

            question.addEventListener('click', function() {
                item.classList.toggle('active');
            });
        });

        console.log('✅ FAQ Proxy loaded:', faqItems.length, 'items');
    });
    </script>
    <?php
}

/**
 * 根據分類推薦相關 FAQ（使用代理）
 */
function renderRecommendedFAQsProxy($category, $limit = 5, $lang = 'zh') {
    $faqMapping = [
        'general' => [
            'faq.general.009',
            'faq.general.010',
            'faq.general.011',
            'faq.general.012',
            'faq.general.013',
        ],
        'kids' => [
            'faq.general.009',
            'faq.grouping.007',
            'faq.grouping.008',
        ],
        'gear' => [
            'faq.general.011',
        ],
        'booking' => [
            'faq.general.012',
            'faq.general.013',
        ],
        'instructor' => [
            'faq.course.005',
            'faq.course.006',
        ]
    ];

    $faqIds = isset($faqMapping[$category])
        ? array_slice($faqMapping[$category], 0, $limit)
        : [];

    if (empty($faqIds)) {
        return;
    }

    renderFAQProxy($faqIds, $lang);
}

/**
 * 為雪場頁面推薦專屬 FAQ（使用代理）
 */
function renderParkFAQsProxy($parkName, $lang = 'zh') {
    $generalFaqs = [
        'faq.general.009',
        'faq.general.010',
        'faq.general.011',
    ];

    renderFAQProxy($generalFaqs, $lang);
}
?>
