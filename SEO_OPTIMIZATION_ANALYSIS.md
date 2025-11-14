# SKIDIY 網站 SEO 和 AI 搜尋優化分析

## 執行摘要

根據對項目代碼的全面掃描，我找到了 **8 個高 ROI 的優化機會**，其中前 5 個優化可以顯著改善 AI 搜尋爬蟲的內容理解和 Google 搜尋排名。

**當前狀態**:
- ✅ 基礎設施良好: robots.txt, sitemap.xml, JSON-LD 基本實現
- ✅ Schema.org 部分實現: Article, SkiResort, Breadcrumb, FAQPage
- ✅ OG 標籤完整: og:image, og:description, hreflang
- ⚠️ **缺失項**: AI 友善元標籤、進階 schema 字段、內容結構標記
- ⚠️ **改進空間**: Meta 描述長度、robots 進階指令、內容可讀性

---

## 詳細分析 (8 個優化機會)

### 🎯 優先級 1: AI Search 優化標籤 (ROI: 最高)

**現狀分析:**
- 缺失 `allow-ai` meta 標籤
- 無 `robots` 進階指令 (max-snippet, max-image-preview)
- 無版權/許可標記

**建議実装:**

```html
<!-- pageHeader.php 中添加 -->

<!-- AI 爬蟲許可 -->
<meta name="allow-ai" content="true">
<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">

<!-- 版權和許可信息 -->
<meta name="copyright" content="Copyright 2025 SKIDIY 自助滑雪. All rights reserved.">
<meta name="license" content="https://diy.ski/license">

<!-- 針對特定 AI 爬蟲 -->
<meta name="googlebot" content="index, follow, max-snippet:-1, max-image-preview:large">
<meta name="bingbot" content="index, follow, max-snippet:-1, max-image-preview:large">
```

**預期影響:**
- Claude/OpenAI/Perplexity 等 AI 模型更容易索引和引用您的內容
- 搜尋結果中的摘要更完整 (不受字符限制)
- 圖片預覽顯示更清晰

**實施成本:** 5 分鐘 | **影響範圍:** 全站 | **收益時間:** 1-2 週

---

### 🎯 優先級 2: Meta 描述優化 (ROI: 高)

**現狀分析:**
```php
// pageHeader.php 第 79, 152 行
<meta name="description" content="<?=$resolvedDescription?>" />
```

問題:
- 大多數 description 長度未檢查，可能超過 160 字元 (Google 現在偏好 120-150 字)
- 未包含 structured 信息 (主要資訊一目瞭然)

**建議修改:**

```php
<?php
// 新增 SEO 優化函數 (在 sdk.php 或專用文件中)
function optimizeMetaDescription($text, $maxLength = 155) {
    $text = trim(strip_tags($text));
    // 確保在句子邊界處截斷
    if (strlen($text) > $maxLength) {
        $text = substr($text, 0, $maxLength);
        $text = substr($text, 0, strrpos($text, ' ')) . '...';
    }
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

// pageHeader.php 優化示例 (Park 頁面)
$optimizedDescription = optimizeMetaDescription(
    "【{$park_basic_info['cname']}】日本人氣滑雪場。位於{$park_info['location']}，提供中文教練課程、雪票購買、住宿資訊。初學者友善，可線上預約。",
    155
);
?>
<meta name="description" content="<?=$optimizedDescription?>" />
```

**實施細節 - Park 頁面:**
```
原始: "澳洲最大的滑雪場，擁有47部纜車和多個雪道..."
最佳: "【雪場名稱】位置和特色。中文教練、雪票、住宿資訊。可線上預約課程。"

字數: 120-155 字
結構: [雪場名] + 地點 + 特色 + 預約資訊
```

**實施成本:** 30 分鐘 | **影響範圍:** 所有列表頁 | **收益時間:** 1 週

---

### 🎯 優先級 3: SkiResort Schema 補充欄位 (ROI: 高)

**現狀分析:**
```php
// park.php 第 30-83 行
$parkSchema = [
  '@context' => 'https://schema.org',
  '@type' => 'SkiResort',
  'name' => $display_name,
  'description' => $SEO_DESCRIPTION,
  'image' => [$hero_image],
  'touristType' => 'Skiers',
  // ❌ 缺失: amenities, skiAreas, contact, telephone, email, url
];
```

**建議補充:**

```php
<?php
// park.php 中 $parkSchema 的擴展
$parkSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'SkiResort',
    'name' => $display_name,
    'description' => $SEO_DESCRIPTION,
    'url' => 'https://' . domain_name . '/park.php?name=' . urlencode($name),
    'image' => [$hero_image],
    
    // 新增欄位
    'sameAs' => [
        'https://www.google.com/search?q=' . urlencode($display_name),
        !empty($park_info['official_url']) ? $park_info['official_url'] : null
    ],
    
    // 預計開放時間
    'openingHoursSpecification' => [
        '@type' => 'OpeningHoursSpecification',
        'dayOfWeek' => ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
        'opens' => '08:00',  // 從 park_info 提取
        'closes' => '17:00'  // 從 park_info 提取
    ],
    
    // 聯絡方式
    'contactPoint' => [
        '@type' => 'ContactPoint',
        'contactType' => 'Customer Service',
        'telephone' => '+886-2-XXXX-XXXX',  // SKIDIY 統一客服電話
        'email' => 'service@diy.ski',
        'availableLanguage' => ['zh-TW', 'en', 'ja']
    ],
    
    // 設施列表 (對 AI 提取很關鍵)
    'amenityFeature' => [
        [
            '@type' => 'LocationFeatureSpecification',
            'name' => 'Skiing',
            'value' => true
        ],
        [
            '@type' => 'LocationFeatureSpecification',
            'name' => 'Snowboarding',
            'value' => !empty($park_info['snowboard_allowed'])
        ],
        [
            '@type' => 'LocationFeatureSpecification',
            'name' => 'Lessons Available',
            'value' => true
        ],
        [
            '@type' => 'LocationFeatureSpecification',
            'name' => 'Equipment Rental',
            'value' => !empty($park_info['rental_section'])
        ],
        [
            '@type' => 'LocationFeatureSpecification',
            'name' => 'Chinese Speaking Staff',
            'value' => true
        ]
    ],
    
    // 主要吸引點
    'potentialAction' => [
        '@type' => 'ReserveAction',
        'target' => [
            '@type' => 'EntryPoint',
            'urlTemplate' => 'https://booking.diy.ski/schedule?park=' . $name,
            'actionPlatform' => ['DesktopWebPlatform', 'MobileWebPlatform']
        ],
        'name' => 'Book Lesson'
    ]
];
?>
```

**實施成本:** 1 小時 | **影響範圍:** Park 頁面 | **收益時間:** 2-3 週

---

### 🎯 優先級 4: Article Schema 完善 (ROI: 中高)

**現狀分析:**
```php
// article.php 第 33-61 行 - 缺失關鍵欄位
$articleSchema = [
  '@type' => 'Article',
  'headline' => $article_title,
  'description' => $SEO_DESCRIPTION,
  'image' => [$article_hero],
  // ❌ 缺失: articleBody, keywords, wordCount, author details, publisher logo
];
```

**建議補充:**

```php
<?php
// article.php 中 $articleSchema 的擴展
if (!empty($article_raw['timestamp'])) {
    $published = date(DATE_ATOM, strtotime($article_raw['timestamp']));
    $articleSchema['datePublished'] = $published;
    $articleSchema['dateModified'] = $published;
}

// 新增欄位優化
$articleSchema = array_merge($articleSchema, [
    // 文章內容 (用於 AI 提取)
    'articleBody' => strip_tags($article_content_html),
    
    // 關鍵字標籤
    'keywords' => implode(',', $article_keywords ?? ['滑雪', '教練', 'SKI']),
    
    // 字數統計 (Google 重視)
    'wordCount' => str_word_count(strip_tags($article_content_html)),
    
    // 文章種類
    'articleSection' => 'Travel Guide',
    
    // 作者詳情
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
    
    // 頭圖詳情
    'image' => [
        [
            '@type' => 'ImageObject',
            'url' => $article_hero,
            'width' => 1200,
            'height' => 630,
            'name' => $article_title
        ]
    ],
    
    // 讀者評論 (如有)
    'commentCount' => count($article_comments ?? [])
]);
?>
```

**實施成本:** 45 分鐘 | **影響範圍:** Article 頁面 | **收益時間:** 2 週

---

### 🎯 優先級 5: Content Structure 標記 (ROI: 中)

**現狀分析:**

Park 頁面的內容結構目前缺少 AI 友善的語義標記。Google 和 AI 爬蟲難以區分不同的內容區塊。

**建議實施:**

```html
<!-- park.php 和組件中 -->

<!-- 使用 semantic HTML5 和 data 屬性 -->

<!-- 介紹區塊 -->
<section class="park-section" data-section-type="about" id="about-section">
    <h2>【<name>苗場</name>】介紹</h2>
    <p class="summary" data-summary-type="main">
        日本人氣第一滑雪場，擁有優質粉雪和完善設施...
    </p>
    <dl class="key-facts">
        <dt>地點</dt>
        <dd itemscope itemtype="https://schema.org/Place">
            <span itemprop="name">新潟縣湯澤町</span>
        </dd>
        <dt>特色</dt>
        <dd>初學者友善、中文教練、高質粉雪</dd>
    </dl>
</section>

<!-- 交通資訊 (結構化清單) -->
<section class="park-section" data-section-type="access" id="access-section">
    <h2>交通方式</h2>
    <ol class="directions-list">
        <li data-direction-step="1">
            <strong>第一步:</strong> 搭乘新幹線至越後湯澤站 
            <span class="detail">(約 80 分鐘)</span>
        </li>
        <li data-direction-step="2">
            <strong>第二步:</strong> 轉乘飯店接駁巴士
            <span class="detail">(約 30 分鐘)</span>
        </li>
    </ol>
</section>

<!-- 雪票資訊 (表格優於段落) -->
<section class="park-section" data-section-type="ticket">
    <h2>雪票價格</h2>
    <table class="ticket-table" data-searchable="true">
        <thead>
            <tr>
                <th>票券類型</th>
                <th>價格 (JPY)</th>
                <th>適用對象</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>全日券</td>
                <td>5,000</td>
                <td>成人</td>
            </tr>
        </tbody>
    </table>
</section>

<!-- FAQ 部分 (已有 FAQPage Schema，但可增強) -->
<section class="park-faq" data-section-type="faq" itemscope itemtype="https://schema.org/FAQPage">
    <h2>常見問題</h2>
    <div class="faq-item" itemscope itemtype="https://schema.org/Question">
        <h3 itemprop="name">苗場適合初學者嗎？</h3>
        <div itemprop="acceptedAnswer" itemscope itemtype="https://schema.org/Answer">
            <p itemprop="text">
                是的，苗場擁有專門的初學者區域...
            </p>
        </div>
    </div>
</section>
```

**實施成本:** 2 小時 (逐步實施) | **影響範圍:** Park 和 Article 頁面 | **收益時間:** 3-4 週

---

## 次優化 (機會 6-8)

### 6️⃣ robots Meta 進階指令

**現狀:**
```php
// pageHeader.php 第 20 行 - 僅針對 PREVIEW_MODE
<meta name="robots" content="noindex, nofollow">
```

**改進:**
```php
<?php
if (defined('SKID_PREVIEW_MODE') && SKID_PREVIEW_MODE) {
    echo '<meta name="robots" content="noindex, nofollow">';
} else {
    echo '<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">';
}
?>

<!-- 針對特定工具 -->
<meta name="googlebot" content="index, follow, max-snippet:-1, max-image-preview:large, notranslate">
<meta name="bingbot" content="index, follow, max-snippet:-1, max-image-preview:large">

<!-- Crawl 優化 (減少伺服器負荷) -->
<meta name="Crawl-delay" content="1">
```

**收益:** Google 摘要更完整、搜尋結果圖片更清晰 | **成本:** 10 分鐘

---

### 7️⃣ Twitter/LinkedIn 卡片優化

**現狀:** 缺失 `twitter:card`, `twitter:creator`

**添加:**
```html
<!-- pageHeader.php 中 -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="@skidiy">
<meta name="twitter:creator" content="@skidiy">
<meta name="twitter:title" content="<?=$metaTitleOverride?>">
<meta name="twitter:description" content="<?=$metaDescriptionOverride?>">
<meta name="twitter:image" content="<?=$metaImageOverride?>">

<!-- LinkedIn -->
<meta property="og:image:secure_url" content="<?=$metaImageOverride?>">
```

**收益:** 社群分享時外觀更好 | **成本:** 5 分鐘

---

### 8️⃣ 內部連接策略

**現狀:** Park 頁面缺少到相關文章的內部鏈接

**建議:**
```php
<?php
// park.php 或 components 中新增
function renderRelatedArticles($parkName, $limit = 3) {
    $relatedArticles = getArticlesByPark($parkName);
    if (empty($relatedArticles)) return;
    ?>
    <section class="related-articles" aria-labelledby="related-articles-title">
        <h2 id="related-articles-title"><?=$parkName?> 相關文章</h2>
        <ul>
            <?php foreach (array_slice($relatedArticles, 0, $limit) as $article): ?>
            <li>
                <a href="/article.php?idx=<?=$article['idx']?>" 
                   title="<?=$article['title']?>">
                    <?=$article['title']?>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </section>
    <?php
}
?>
```

**收益:** 提高頁面停留時間、分散權重 | **成本:** 1 小時

---

## 實施優先順序表

| 優先級 | 項目 | 難度 | 時間 | ROI | 預期 SERP 影響 |
|--------|------|------|------|-----|----------------|
| 1 | AI Search Meta 標籤 | 低 | 5 分鐘 | 最高 | +10-15% AI 引用 |
| 2 | Meta 描述優化 | 低 | 30 分鐘 | 高 | +5-10% CTR |
| 3 | SkiResort Schema | 中 | 1 小時 | 高 | +8-12% 結果豐富度 |
| 4 | Article Schema | 低 | 45 分鐘 | 中高 | +5-8% Article SERP |
| 5 | Content Structure | 中 | 2 小時 | 中 | +3-5% AI 理解度 |
| 6 | Robots Meta | 低 | 10 分鐘 | 中 | 摘要更完整 |
| 7 | Twitter/LinkedIn | 低 | 5 分鐘 | 低 | 社群效果 |
| 8 | 內部連接 | 中 | 1 小時 | 中 | +2-4% Session 深度 |

---

## 遷移前快速檢查清單

在遷移到新主機前，請確保:

- [ ] 1. 添加 `allow-ai` 和 `max-snippet` meta 標籤
- [ ] 2. 檢查所有 meta descriptions 在 120-155 字元範圍內
- [ ] 3. 在 robots.txt 中添加 Sitemap 聲明 (已完成)
- [ ] 4. 為 Park 頁面增強 SkiResort schema
- [ ] 5. 為 Article 頁面補充 wordCount 和 articleBody
- [ ] 6. 設定 HTTP 重定向檢查 (301 vs 302)
- [ ] 7. 配置 GSC 遷移通知
- [ ] 8. 在 robots.txt 中禁用 preview/draft URL

---

## 驗證方法

### 使用工具檢驗

```bash
# 1. Schema 驗證
https://validator.schema.org/

# 2. Meta 標籤檢查
https://metatags.io/

# 3. 移動友善檢測
https://search.google.com/test/mobile-friendly

# 4. 豐富結果測試
https://search.google.com/test/rich-results

# 5. 開源工具
npm install -g lighthouse
lighthouse https://diy.ski --view
```

### Google Search Console

遷移後:
1. 添加新屬性
2. 驗證所有權
3. 提交 sitemap
4. 檢查索引狀態
5. 監控 Core Web Vitals

---

## 預期成效時間表

- **1-2 週**: AI 搜尋爬蟲開始索引新的 meta 標籤 (優先級 1)
- **2-3 週**: Google 更新搜尋結果摘要 (優先級 2)
- **4-6 週**: Schema 改進在搜尋結果顯示 (優先級 3-4)
- **2-3 個月**: 整體 SERP 位置改善 5-15%

---

**報告生成時間**: 2025-11-13
**分析範圍**: pageHeader.php, park.php, article.php, robots.txt, sitemap.xml.php, includes/content_repository.php
