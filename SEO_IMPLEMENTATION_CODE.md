# SKIDIY SEO 優化 - 實施代碼示例

## 1️⃣ AI Search Meta 標籤實施

### 位置: pageHeader.php (第 35-36 行之後)

```php
<?php
// ============================================
// AI Search Optimization Meta Tags (新增)
// ============================================
?>

<!-- AI 爬蟲友善指令 -->
<meta name="allow-ai" content="true">

<!-- 進階 robots 指令 (全站統一適用非預覽模式) -->
<?php
if (!defined('SKID_PREVIEW_MODE') || !SKID_PREVIEW_MODE) {
    ?>
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
    <meta name="googlebot" content="index, follow, max-snippet:-1, max-image-preview:large, notranslate">
    <meta name="bingbot" content="index, follow, max-snippet:-1, max-image-preview:large">
    <meta name="slurp" content="index, follow, max-snippet:-1, max-image-preview:large">
    <?php
} ?>

<!-- 版權和許可信息 -->
<meta name="copyright" content="Copyright 2025 SKIDIY 自助滑雪. All rights reserved.">
<meta name="license" content="https://diy.ski/license">

<!-- Crawl 效率優化 -->
<meta name="Crawl-delay" content="1">

<!-- 語言宣告 (AI 友善) -->
<meta http-equiv="Content-Language" content="zh-TW">
<?php
```

---

## 2️⃣ Meta 描述優化函數

### 位置: includes/sdk.php (或新建 includes/seo_helpers.php)

```php
<?php
/**
 * SEO Meta Description Optimizer
 * 確保描述長度符合 Google 最佳實踐 (120-155 字元)
 * 
 * @param string $text 原始描述文本
 * @param int $maxLength 最大字元數 (預設 155)
 * @return string 優化後的描述
 */
function optimizeMetaDescription($text, $maxLength = 155) {
    // 移除 HTML 標籤
    $text = trim(strip_tags($text));
    
    // 移除多餘空白
    $text = preg_replace('/\s+/', ' ', $text);
    
    // 檢查長度
    if (strlen($text) <= $maxLength) {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
    
    // 在字數限制內截斷，並在最後一個單詞邊界處停止
    $text = substr($text, 0, $maxLength);
    $lastSpace = strrpos($text, ' ');
    
    if ($lastSpace > $maxLength - 50) { // 確保有足夠文本
        $text = substr($text, 0, $lastSpace);
    }
    
    return htmlspecialchars($text . '...', ENT_QUOTES, 'UTF-8');
}

/**
 * 取得優化後的 Park 描述
 * 格式: 【雪場名稱】地點 + 特色 + 預約資訊
 */
function getParkOptimizedDescription($parkData) {
    $parts = [];
    
    // 雪場名稱
    if (!empty($parkData['cname'])) {
        $parts[] = '【' . $parkData['cname'] . '】';
    }
    
    // 地點和特色
    if (!empty($parkData['location'])) {
        $parts[] = '位於 ' . strip_tags($parkData['location']);
    }
    
    // 主要特色 (最多取 2 個關鍵字)
    $keywords = [];
    if (!empty($parkData['about'])) {
        if (strpos($parkData['about'], '初學者') !== false) $keywords[] = '初學者友善';
        if (strpos($parkData['about'], '粉雪') !== false) $keywords[] = '優質粉雪';
        if (strpos($parkData['about'], '家庭') !== false) $keywords[] = '家庭友善';
    }
    if (!empty($keywords)) {
        $parts[] = implode('、', array_slice($keywords, 0, 2));
    }
    
    // 預約資訊
    $parts[] = '提供中文教練課程、可線上預約';
    
    $description = implode('。', $parts);
    
    return optimizeMetaDescription($description);
}

/**
 * 取得優化後的 Article 描述
 */
function getArticleOptimizedDescription($articleData) {
    $description = !empty($articleData['seo']['description']) 
        ? $articleData['seo']['description']
        : substr(strip_tags($articleData['content']), 0, 120);
    
    return optimizeMetaDescription($description);
}
?>
```

### 在 pageHeader.php 中使用

```php
<?php
// 在 park 目標的 description 部分 (約第 79 行)
if ($target == 'park') {
    // 使用優化函數而不是直接輸出
    $optimizedDescription = getParkOptimizedDescription($park_basic_info);
    ?>
    <meta name="description" content="<?=$optimizedDescription?>" />
    <meta property="og:description" content="<?=$optimizedDescription?>" />
    <?php
}
// Article 類似處理
?>
```

---

## 3️⃣ SkiResort Schema 增強

### 位置: park.php (第 30-84 行)

```php
<?php
// 原始 schema 定義後，添加以下欄位

$parkSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'SkiResort',
    'name' => $display_name,
    'description' => $SEO_DESCRIPTION,
    'url' => 'https://' . domain_name . '/park.php?name=' . urlencode($name),
    'image' => [$hero_image],
    'touristType' => 'Skiers',
    
    // ========== 新增欄位 ==========
    
    // 1. 官方網站連結 (如有)
    'sameAs' => array_filter([
        !empty($park_info['official_url']) ? $park_info['official_url'] : null,
        'https://en.wikipedia.org/wiki/' . urlencode($display_name), // 如果有維基頁面
    ]),
    
    // 2. 營業時間 (如果在 park_info 中有提取)
    'openingHoursSpecification' => [
        '@type' => 'OpeningHoursSpecification',
        'dayOfWeek' => [
            'Monday', 'Tuesday', 'Wednesday', 'Thursday', 
            'Friday', 'Saturday', 'Sunday'
        ],
        'opens' => !empty($park_info['open_time']) 
            ? substr($park_info['open_time'], 0, 5) 
            : '08:00',
        'closes' => !empty($park_info['close_time']) 
            ? substr($park_info['close_time'], 0, 5) 
            : '17:00',
        'validFrom' => date('Y-m-d', strtotime('December 1')),
        'validThrough' => date('Y-m-d', strtotime('March 31 next year'))
    ],
    
    // 3. 聯絡方式
    'contactPoint' => [
        '@type' => 'ContactPoint',
        'contactType' => 'Customer Service',
        'telephone' => '+886-2-2881-0000', // SKIDIY 統一客服電話
        'email' => 'service@diy.ski',
        'availableLanguage' => ['zh-TW', 'en', 'ja'],
        'areaServed' => 'TW'
    ],
    
    // 4. 設施特性 (AI 提取關鍵資訊)
    'amenityFeature' => [
        [
            '@type' => 'LocationFeatureSpecification',
            'name' => 'Skiing',
            'value' => true
        ],
        [
            '@type' => 'LocationFeatureSpecification',
            'name' => 'Snowboarding',
            'value' => true
        ],
        [
            '@type' => 'LocationFeatureSpecification',
            'name' => 'Ski Lessons',
            'value' => true
        ],
        [
            '@type' => 'LocationFeatureSpecification',
            'name' => 'Equipment Rental',
            'value' => !empty($park_info['rental_section'])
        ],
        [
            '@type' => 'LocationFeatureSpecification',
            'name' => 'Chinese Language Support',
            'value' => true
        ],
        [
            '@type' => 'LocationFeatureSpecification',
            'name' => 'Beginner Friendly',
            'value' => strpos($park_info['about'] ?? '', '初學者') !== false
        ],
        [
            '@type' => 'LocationFeatureSpecification',
            'name' => 'Night Skiing',
            'value' => !empty($park_info['night_skiing'])
        ]
    ],
    
    // 5. 預訂動作 (讓 AI 知道如何預約)
    'potentialAction' => [
        '@type' => 'ReserveAction',
        'target' => [
            '@type' => 'EntryPoint',
            'urlTemplate' => 'https://booking.diy.ski/schedule?park=' . $name,
            'actionPlatform' => ['DesktopWebPlatform', 'MobileWebPlatform']
        ],
        'name' => 'Book Ski Lesson'
    ],
    
    // 6. 評等 (如果有)
    'aggregateRating' => !empty($park_rating) ? [
        '@type' => 'AggregateRating',
        'ratingValue' => $park_rating['value'],
        'ratingCount' => $park_rating['count'],
        'bestRating' => 5,
        'worstRating' => 1
    ] : null,
    
    // 7. 地點詳情 (既有的但改進)
    'address' => !empty($park_info['address']) ? [
        '@type' => 'PostalAddress',
        'streetAddress' => strip_tags($park_info['address']),
        'addressLocality' => !empty($park_info['location']) 
            ? strip_tags($park_info['location']) 
            : 'Japan',
        'addressCountry' => 'JP'
    ] : [
        '@type' => 'PostalAddress',
        'addressCountry' => 'JP',
        'addressRegion' => !empty($park_info['location']) 
            ? strip_tags($park_info['location']) 
            : null
    ]
];

// 移除空值
$parkSchema = array_filter($parkSchema, function($value) {
    return $value !== null && $value !== '';
});
?>
```

---

## 4️⃣ Article Schema 增強

### 位置: article.php (第 33-61 行)

```php
<?php
// 在現有 schema 之後添加

// 計算字數
$articleBodyText = strip_tags($article_content_html);
$wordCount = str_word_count($articleBodyText);

// 提取關鍵字 (從 article_raw)
$keywords = [];
if (!empty($article_raw['keywords'])) {
    $keywords = array_map('trim', explode(',', $article_raw['keywords']));
} else {
    // 自動提取 (使用標題中的詞語)
    $keywords = ['滑雪', '教練', 'SKI', explode(' ', $article_title)[0]];
}

// 擴展 schema
$articleSchema = array_merge($articleSchema, [
    // 文章內容本身
    'articleBody' => $articleBodyText,
    
    // 關鍵字
    'keywords' => implode(', ', array_slice($keywords, 0, 5)),
    
    // 字數 (Google 注重)
    'wordCount' => $wordCount,
    
    // 文章分類
    'articleSection' => 'Travel Guide',
    'author' => [
        '@type' => 'Organization',
        'name' => 'SKIDIY 自助滑雪',
        'url' => 'https://diy.ski',
        'logo' => [
            '@type' => 'ImageObject',
            'url' => 'https://diy.ski/assets/images/logo-skidiy.png',
            'width' => 200,
            'height' => 200
        ]
    ],
    
    // 出版商詳情
    'publisher' => [
        '@type' => 'Organization',
        'name' => 'SKIDIY 自助滑雪',
        'logo' => [
            '@type' => 'ImageObject',
            'url' => 'https://diy.ski/assets/images/logo-skidiy.png',
            'width' => 200,
            'height' => 200
        ]
    ],
    
    // 圖片詳情
    'image' => [
        [
            '@type' => 'ImageObject',
            'url' => $article_hero,
            'width' => 1200,
            'height' => 630
        ]
    ],
    
    // 指定主要實體 (AI 會優先提取)
    'mainEntity' => [
        '@type' => 'Thing',
        'name' => $article_title,
        'description' => $SEO_DESCRIPTION
    ]
]);

// 移除空值
$articleSchema = array_filter($articleSchema);
?>
```

---

## 5️⃣ Twitter/LinkedIn Meta 標籤

### 位置: pageHeader.php (在 OG 標籤區塊的末尾)

```php
<?php
// 在所有 og: 標籤之後添加

// ========== Twitter Card Meta Tags ==========
?>
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="@skidiy">
<meta name="twitter:creator" content="@skidiy">
<meta name="twitter:title" content="<?=$metaTitleOverride ?? '自助滑雪首選 - SKIDIY'?>">
<meta name="twitter:description" content="<?=$metaDescriptionOverride ?? ''?>">
<meta name="twitter:image" content="<?=$metaImageOverride ?? 'https://diy.ski/assets/images/skidiy_logo_share.jpg'?>">
<meta name="twitter:image:alt" content="SKIDIY 自助滑雪">

<!-- LinkedIn Rich Tags -->
<meta property="og:image:secure_url" content="<?=$metaImageOverride ?? 'https://diy.ski/assets/images/skidiy_logo_share.jpg'?>">
```

---

## 6️⃣ Robots.txt 增強

### 位置: robots.txt (更新現有)

```
# SkiDIY Robots.txt - 優化版本
# 2025-11-13

User-agent: *

# 基本禁止項
Disallow: /bkAdmin/
Disallow: /admin/
Disallow: /login.php
Disallow: /account_login.php
Disallow: /oquery.php
Disallow: /post-cgi.php
Disallow: /instructor_form.php
Disallow: /data/
Disallow: /includes/

# 調試和測試檔案
Disallow: /debug_db.php
Disallow: /import_*.php
Disallow: /check_*.php
Disallow: /generate_*.php
Disallow: /test_*.php

# 預覽模式（如果有參數）
Disallow: /*?preview_token=*
Disallow: /*?render=*

# 允許公開頁面
Allow: /parkList.php
Allow: /instructorList.php
Allow: /articleList.php
Allow: /article.php
Allow: /park.php
Allow: /instructor.php
Allow: /assets/
Allow: /photos/
Allow: /faq/

# Crawl 優化
Crawl-delay: 1

# 用戶代理特定規則
User-agent: Googlebot
Crawl-delay: 0.5
Allow: /

User-agent: Bingbot
Crawl-delay: 1
Allow: /

# AI 爬蟲友善 (允許 OpenAI, Anthropic 等)
User-agent: CCBot
Allow: /

User-agent: GPTBot
Allow: /

User-agent: PerplexityBot
Allow: /

# Sitemap 位置
Sitemap: https://diy.ski/sitemap.xml.php
```

---

## 驗證清單

實施後請檢查:

```bash
# 1. Schema 驗證
# 訪問: https://validator.schema.org/
# 粘貼您的 park.php 或 article.php URL

# 2. Meta 標籤檢查
# 使用 metatags.io 檢查
# https://metatags.io/?url=https://diy.ski/park.php?name=naeba

# 3. 本地測試 (使用 curl)
curl -H "User-Agent: Mozilla/5.0" https://diy.ski/park.php?name=naeba | \
  grep -E "meta name=|twitter:|og:" | head -20

# 4. 檢查 robots.txt
curl https://diy.ski/robots.txt

# 5. 檢查 Sitemap
curl https://diy.ski/sitemap.xml.php | head -50
```

---

## 逐步實施計畫 (7 天)

**Day 1**: 實施 #1 和 #2 (AI Meta + Description) - 5 分鐘 + 30 分鐘
**Day 2**: 實施 #6 和 #7 (Robots + Twitter) - 10 分鐘 + 5 分鐘
**Day 3**: 實施 #3 (SkiResort Schema) - 1 小時
**Day 4**: 實施 #4 (Article Schema) - 45 分鐘
**Day 5**: 實施 #5 (Content Structure) - 2 小時 (分次進行)
**Day 6-7**: 測試驗證和微調

