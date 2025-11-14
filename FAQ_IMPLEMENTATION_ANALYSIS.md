# FAQ 系統實作現況分析

**分析日期**: 2025-11-13  
**分析對象**: /Users/jameschen/Downloads/diyski/crm/03_FAQ與知識庫/zeabur/skidiyog/  
**當前分支**: feature/schema-refactor

---

## 1. FAQ 資料層架構

### 1.1 FAQ 定義方式

#### 現有實作位置
- **主要檔案**: `/includes/faq_helpers.php`
- **行數**: 39 行（簡單的硬編碼陣列）
- **實現方式**: PHP 函式 `getParkFAQs($parkName)` - 針對各雪場返回不同的 FAQ

```php
// 示例: /includes/faq_helpers.php
function getParkFAQs($parkName){
    $key = strtolower($parkName);
    $faqs = array(
        'naeba' => array(
            array(
                'q' => '苗場適合初學者嗎？',
                'a' => '<p>苗場擁有專門的 beginner zone...</p>'
            ),
            // ... 更多 FAQ
        ),
        'karuizawa' => array( ... )
    );
    if(isset($faqs[$key])){
        return $faqs[$key];
    }
    // 默認 FAQ
    return array( ... );
}
```

**問題**: 
- 硬編碼在 PHP 中，不易維護和擴展
- 每個雪場只有 2-3 個 FAQ
- 沒有元數據（分類、標籤、關鍵字）
- 無多語言支援

---

### 1.2 FAQ 代理系統 (Proxy Pattern)

#### 外部 FAQ 來源
- **目標**: `https://faq.diy.ski`
- **檔案位置**: `/includes/faq_proxy.php` (337 行)
- **實現方式**: 從外部 FAQ 網站代理 HTML 內容

**核心功能**:
```php
// 從 faq.diy.ski 抓取靜態 FAQ 頁面
$url = "https://faq.diy.ski/faq/{$faqId}-{$lang}.html";

// 使用 curl 抓取並快取 (1 小時)
// 正則表達式解析 HTML 結構
// 提取問題 (h1)、答案 (class="faq-content")、分類 badge
// 提取 Schema.org 結構化資料

// 支援分析追蹤：
fetch('https://faq.diy.ski/api/v1/analytics/track-faq-view', {
    faq_id, clicked, language, timestamp, source
});
```

**實作的 FAQ ID 列表** (在代理函式中):
```
faq.general.009  - 幾歲可以開始學滑雪？
faq.general.010  - (未知)
faq.general.011  - (未知)
faq.general.012  - (未知)
faq.general.013  - (未知)
faq.grouping.007 - (未知)
faq.grouping.008 - (未知)
faq.course.005   - (未知)
faq.course.006   - (未知)
```

**支援的分類映射**:
```php
$faqMapping = [
    'general'  => [faq.general.009-013],
    'kids'     => [faq.general.009, grouping.007-008],
    'gear'     => [faq.general.011],
    'booking'  => [faq.general.012-013],
    'instructor' => [faq.course.005-006]
];
```

**快取機制**:
- 使用 APCu (如果可用)
- 快取時間: 1 小時
- 失敗時靜默降級

---

### 1.3 FAQ 本地 HTML 檔案

#### 檔案位置
- **目錄**: `/faq/`
- **命名格式**: `faq.{category}.{id}-{lang}.html`
- **已存在的檔案**:
  ```
  /faq/faq.general.009-zh.html  (幾歲可以開始學滑雪？)
  /faq/faq.general.010-zh.html
  /faq/faq.general.011-zh.html
  ```

#### 檔案結構範例
```html
<!doctype html>
<html lang="zh">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>幾歲可以開始學滑雪？ | SkiDIY FAQ</title>
  
  <!-- hreflang 多語言標籤 ✓ -->
  <link rel="canonical" href="https://faq.diy.ski/faq/faq.general.009" />
  <link rel="alternate" hreflang="zh-Hant" href="..." />
  <link rel="alternate" hreflang="en" href="..." />
  <link rel="alternate" hreflang="th" href="..." />
  
  <!-- Meta Tags ✓ -->
  <meta name="description" content="建議從3歲以上開始..." />
  <meta property="og:type" content="article" />
  <meta property="og:title" content="幾歲可以開始學滑雪？" />
  <meta property="og:description" content="..." />
  <meta property="og:url" content="https://faq.diy.ski/faq/faq.general.009" />
  
  <!-- Schema.org FAQPage ✓ -->
  <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "FAQPage",
      "inLanguage": "zh",
      "mainEntity": [{
        "@type": "Question",
        "name": "幾歲可以開始學滑雪？",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "建議從3歲以上開始..."
        }
      }]
    }
  </script>
</head>
<body>
  <header>
    <p class="badge">👶 小朋友滑雪與安全保障</p>
    <h1>幾歲可以開始學滑雪？</h1>
    <p class="meta">ID: faq.general.009</p>
  </header>
  
  <section class="card">
    <h2>主要回答</h2>
    <div class="faq-content"><p>建議從3歲以上開始...</p></div>
  </section>
  
  <section class="card"><h2>補充資訊</h2>...</section>
  <section class="card"><h2>小提醒</h2>...</section>
  <section class="card"><h2>附註</h2>...</section>
  
  <section class="card">
    <h2>FAQ 資訊</h2>
    <div class="meta-grid">
      <div><strong>分類</strong><br/>👶 小朋友滑雪與安全保障</div>
      <div><strong>最後更新</strong><br/>2025-11-05</div>
      <div><strong>CRM 標籤</strong><br/>
        <span class="tag">#兒童滑雪</span>
        <span class="tag">#年齡限制</span>
        <span class="tag">#教學安排</span>
        <span class="tag">#安全考量</span>
      </div>
      <div><strong>關鍵字</strong><br/>
        <span class="keyword">學滑雪年齡</span>
        <span class="keyword">兒童滑雪</span>
        ...
      </div>
    </div>
  </section>
  
  <section class="card">
    <h2>使用者提問語句</h2>
    <ul>
      <li>幾歲可以開始學滑雪</li>
      <li>幾歲能不能開始學滑雪</li>
      ...
    </ul>
  </section>
</body>
</html>
```

**優點**:
- 完整的 Schema.org FAQPage 實作 ✓
- hreflang 多語言標籤 ✓
- Open Graph tags ✓
- 結構化元數據（分類、標籤、關鍵字）✓
- 使用者提問語句作為同義詞 ✓

---

## 2. Meta Tags 和結構化資料

### 2.1 Page Header Meta Tags (`pageHeader.php`)

**位置**: `/pageHeader.php` (158 行)

**實現的 Meta Tags**:

| Tag | 實現 | 範例 |
|-----|------|------|
| Charset | ✓ | `<meta charset="utf-8">` |
| Viewport | ✓ | `<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover"/>` |
| Title | ✓ | 動態根據頁面設定 |
| Description | ✓ | `<meta name="description" content="...">` |
| **og:url** | ✓ | 頁面型態驅動 (park/article/instructor) |
| **og:title** | ✓ | `$metaTitleOverride` 優先 |
| **og:description** | ✓ | `$metaDescriptionOverride` 優先 |
| **og:image** | ✓ | `$metaImageOverride` 優先 |
| **og:type** | ✓ | `website` 或 `article` |
| **og:image:width** | ⚠️ | 僅在 instructor 頁面 (300px) |
| **og:image:height** | ⚠️ | 僅在 instructor 頁面 (300px) |
| **fb:app_id** | ✓ | `1475301989434574` (硬編碼) |
| **Robots** | ✓ | Preview mode 時 `noindex, nofollow` |
| **Canonical** | ✗ | **缺失** |
| **hreflang** | ✗ | **缺失** (多語言支援) |

**頁面型態驅動的邏輯**:
```php
// pageHeader.php 第 52-140 行
if($target=='park') { ... }       // Park 頁面特定邏輯
else if($target=='instructor') { ... }
else if($target=='index') { ... }
else if($target=='article') { ... }
else if($target=='schedule') { ... }
// 等等
```

---

### 2.2 Schema.org 結構化資料

#### Park 頁面 (`park.php`)

**實現方式**: 
```php
// park.php 第 30-75 行
$parkSchema = [
  '@context' => 'https://schema.org',
  '@type' => 'SkiResort',
  'name' => $display_name,
  'description' => $SEO_DESCRIPTION,
  'url' => 'https://...',
  'image' => [$hero_image],
  'touristType' => 'Skiers',
  'provider' => [...],
  'areaServed' => '...',  // 可選
  'address' => [           // 可選
    '@type' => 'PostalAddress',
    'streetAddress' => '...',
    'addressCountry' => 'JP'
  ],
  'openingHoursSpecification' => [...],  // 可選
  'priceRange' => '...',               // 可選
  'hasMap' => '...'                     // 可選
];
```

**Schema 類型**: SkiResort (特定於滑雪場)

---

#### Article 頁面 (`article.php`)

**實現方式**:
```php
// article.php 第 33-61 行
$articleSchema = [
  '@context' => 'https://schema.org',
  '@type' => 'Article',
  'headline' => $article_title,
  'description' => $SEO_DESCRIPTION,
  'inLanguage' => 'zh-TW',
  'image' => [$article_hero],
  'mainEntityOfPage' => [
    '@type' => 'WebPage',
    '@id' => '...'
  ],
  'author' => [
    '@type' => 'Organization',
    'name' => 'SKIDIY 自助滑雪'
  ],
  'publisher' => [
    '@type' => 'Organization',
    'name' => 'SKIDIY 自助滑雪',
    'logo' => [...]
  ],
  'datePublished' => '...',  // 若有時間戳
  'dateModified' => '...'
];
```

**Schema 類型**: Article (通用文章)

---

#### FAQ 頁面 (組件)

**實現位置**: `/includes/components/faq.php` (64 行)

**實現方式**:
```php
// faq.php 第 26-43 行
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
        {
            "@type": "Question",
            "name": "<?= addslashes(strip_tags($faq['q'])) ?>",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "<?= addslashes(strip_tags($faq['a'])) ?>"
            }
        },
        // 更多 FAQ...
    ]
}
</script>
```

**使用時機**: 頁面底部用 `renderFAQSection()` 函式顯示 FAQ

**問題**:
- `strip_tags()` 移除了答案中的重要 HTML (連結、列表等)
- 缺少 Schema.org 推薦的 meta 欄位 (URL、keywords 等)

---

#### FAQ 代理 Schema (進階)

**實現位置**: `/includes/faq_proxy.php` (第 124-143 行)

```php
function injectFAQSchema($faqs) {
    $mainEntity = [];
    foreach ($faqs as $faq) {
        if (isset($faq['schemaData']['mainEntity'])) {
            $mainEntity = array_merge($mainEntity, $faq['schemaData']['mainEntity']);
        }
    }
    
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => $mainEntity
    ];
    
    echo '<script type="application/ld+json">'
        . json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        . '</script>';
}
```

**優點**: 從外部 FAQ 網站匯入完整的 Schema.org 資料，無損失

---

## 3. 現有檔案位置清單

### 3.1 FAQ 相關

```
/includes/
  ├── faq_helpers.php           (39 行) - 硬編碼的雪場 FAQ
  ├── faq_proxy.php             (337 行) - 代理外部 faq.diy.ski
  ├── faq_embed.php             (未查看)
  └── components/
      └── faq.php               (64 行) - FAQSection 組件與 Schema

/faq/
  ├── faq.general.009-zh.html   - 幾歲可開始學滑雪 (多語言支援)
  ├── faq.general.010-zh.html
  └── faq.general.011-zh.html

/includes/content_repository.php (217 行)
  - ContentRepository::getParkData()
  - ContentRepository::getArticleData()
  - 包含 FAQ 關鍵字建議 (line 117)
```

### 3.2 Meta Tags 相關

```
/pageHeader.php                  (158 行)
  - Open Graph tags (og:*)
  - Meta description
  - Robots directive
  - Page type detection

/park.php                        (前 84 行)
  - SkiResort Schema.org
  - SEO 資訊設定

/article.php                     (前 73 行)
  - Article Schema.org
  - SEO 資訊設定

/includes/content_repository.php
  - SEO snippet 建構
  - 圖片解析邏輯
```

### 3.3 數據層

```
/includes/db.class.php           - SQLite PDO wrapper
  - CREATE TABLE parks (idx, name, cname, description, ...)
  - CREATE TABLE articles (...)
  - CREATE TABLE instructorInfo (...)

/includes/mj.class.php           (284 行)
  - class PARKS { getParkInfo(), getParkInfo_by_Name(), ... }
  - class INSTRUCTORS { ... }
  - class ARTICLE { readByIdx(), listing(), ... }

/database/
  ├── parks.json                 - JSON 格式的雪場資料
  ├── articles.json              - 文章資料
  └── instructors.json           - 教練資料
```

### 3.4 組件相關

```
/includes/components/
  ├── hero.php                   - 頁首英雄圖片
  ├── nav.php                    - 導航列
  ├── leftnav.php                - 左側導航
  ├── page_sections.php          - 內容分區
  └── faq.php                    - FAQ 區塊 (上述)
```

---

## 4. 當前實作的優點和缺陷

### 優點

| 項目 | 實現狀況 | 評分 |
|------|--------|------|
| SkiResort Schema | ✓ 完整實作 | ⭐⭐⭐⭐⭐ |
| Article Schema | ✓ 完整實作 | ⭐⭐⭐⭐⭐ |
| FAQPage Schema (靜態) | ✓ 完整實作 | ⭐⭐⭐⭐⭐ |
| Open Graph Tags | ✓ 大部分 | ⭐⭐⭐⭐ |
| Meta Description | ✓ 動態生成 | ⭐⭐⭐⭐ |
| hreflang (FAQ) | ✓ FAQ 檔案中 | ⭐⭐⭐⭐ |
| FAQ 代理系統 | ✓ 完整實作 | ⭐⭐⭐⭐⭐ |
| 快取機制 | ✓ APCu 支持 | ⭐⭐⭐⭐ |
| 分析追蹤 | ✓ 集成 | ⭐⭐⭐⭐ |

### 缺陷

| 項目 | 現況 | 優先級 |
|------|------|--------|
| 缺少 Canonical 標籤 | Park/Article 頁面缺少 `<link rel="canonical">` | 🔴 高 |
| hreflang 不完整 | 僅 FAQ 頁面有，Park/Article 缺失 | 🔴 高 |
| og:image:width/height | 僅 Instructor 頁面有，其他缺失 | 🟡 中 |
| FAQ Schema 遺漏詳情 | `strip_tags()` 移除 HTML，失去結構 | 🟡 中 |
| 硬編碼的 FAQ | faq_helpers.php 不易維護 | 🟡 中 |
| 無 Breadcrumb Schema | Park/Article 缺少麵包屑導航結構 | 🟡 中 |
| 無 Person Schema | Instructor 頁面缺少人物 Schema | 🟡 中 |
| 無 Organization Schema | 主頁面缺少組織 Schema | 🟡 中 |
| 無 LocalBusiness Schema | 可用於聯絡信息 | 🟢 低 |

---

## 5. FAQ 資料結構規範

### 5.1 當前資料格式

#### 硬編碼 FAQ (faq_helpers.php)
```php
[
  'q' => '問題文本',
  'a' => '<p>HTML 格式的答案</p>'
]
```

#### 靜態 FAQ HTML (faq.general.009-zh.html)
```
- badge: 分類標籤 (如 👶 小朋友滑雪與安全保障)
- question: 問題 (h1)
- sections:
  - 主要回答 (h2)
  - 補充資訊 (h2)
  - 小提醒 (h2)
  - 附註 (h2)
- meta:
  - 分類 (badge)
  - 最後更新 (ISO 8601)
  - CRM 標籤 (多個)
  - 關鍵字 (多個)
- 使用者提問語句 (同義詞列表)
- 相關連結
```

#### 代理系統使用的格式 (faq_proxy.php)
```php
[
  'id' => 'faq.general.009',
  'question' => '幾歲可以開始學滑雪？',
  'answer' => '<p>建議從3歲以上開始...</p>',
  'badge' => '👶 小朋友滑雪與安全保障',
  'url' => 'https://faq.diy.ski/faq/faq.general.009?lang=zh',
  'schemaData' => [ /* FAQPage JSON-LD */ ]
]
```

### 5.2 JSON Schema 參考

**當前 FAQPage Schema** (簡化版):
```json
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "inLanguage": "zh",
  "mainEntity": [
    {
      "@type": "Question",
      "name": "幾歲可以開始學滑雪？",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "建議從3歲以上開始..."
      }
    }
  ]
}
```

**改進後應包含的欄位**:
```json
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "inLanguage": "zh",
  "url": "https://diy.ski/park.php?name=naeba",
  "name": "常見問題",
  "mainEntity": [
    {
      "@type": "Question",
      "name": "幾歲可以開始學滑雪？",
      "url": "https://faq.diy.ski/faq/faq.general.009",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "建議從3歲以上開始...",
        "author": {
          "@type": "Organization",
          "name": "SKIDIY 自助滑雪"
        }
      }
    }
  ]
}
```

---

## 6. 改進建議

### 6.1 必須修復 (SEO 關鍵)

1. **添加 Canonical 標籤**
   - Park 頁面: `<link rel="canonical" href="https://diy.ski/park.php?name={name}" />`
   - Article 頁面: `<link rel="canonical" href="https://diy.ski/article.php?idx={idx}" />`
   - **位置**: `pageHeader.php`

2. **添加 hreflang 支援**
   - Park: 中文 (zh-Hant) / 英文 (en) / 泰文 (th)
   - Article: 同上
   - **位置**: `pageHeader.php` + 語言路由邏輯

3. **完善 og:image 尺寸**
   - 添加 `og:image:width` 和 `og:image:height`
   - 標準: 1200x630px (Facebook推薦) 或 1200x1200px (通用)
   - **位置**: `pageHeader.php`

### 6.2 應該改進

4. **改進 FAQ Schema 細節**
   - 保留 HTML 標籤而非 `strip_tags()`
   - 添加 `url` 欄位指向完整 FAQ 頁面
   - 添加 `author` 和 `updatedAt` 資訊
   - **位置**: `/includes/components/faq.php`

5. **添加 Person Schema (Instructor)**
   - 名稱、照片、簡介、資格
   - **位置**: `instructor.php`

6. **添加 Breadcrumb Schema**
   - Park: Home > 滑雪場 > {Park Name}
   - Article: Home > 文章 > {Article Title}
   - **位置**: `pageHeader.php` 或各自頁面

### 6.3 可選改進

7. **添加 Organization Schema**
   - 公司名稱、Logo、聯絡方式、社交媒體
   - 放在每頁 `<head>` 中或 JSON-LD 檔案
   - **位置**: `pageHeader.php` (主頁) 或新增 `organization-schema.php`

8. **改進 FAQ 本地儲存**
   - 從 JSON 檔案讀取而非硬編碼
   - 統一 FAQ 資料結構
   - **位置**: 新增 `/data/faq_kb.json`

9. **實作 LocalBusiness Schema**
   - 如有實體地點或營業時間
   - **位置**: 視業務需求

---

## 7. 檔案修改清單

### 影響最大的檔案 (優先修改)

| 檔案 | 修改建議 | 影響範圍 |
|------|--------|--------|
| `pageHeader.php` | 1. 添加 canonical 2. 添加 hreflang 3. 完善 og:image 尺寸 | Park/Article/Instructor |
| `includes/components/faq.php` | 改進 FAQ Schema 細節，保留 HTML | 所有使用 FAQ 的頁面 |
| `park.php` | (可選) 添加 Breadcrumb Schema | Park 頁面 |
| `article.php` | (可選) 添加 Breadcrumb Schema | Article 頁面 |
| `instructor.php` | 添加 Person Schema | Instructor 頁面 |
| `includes/faq_helpers.php` | (優化) 移至 JSON 儲存 | FAQ 管理 |

---

## 8. 配置和依賴

### 外部服務依賴
- `https://faq.diy.ski` - FAQ 代理來源（需要 CURL 和網路連線）
- `https://faq.diy.ski/api/v1/analytics/track-faq-view` - 分析追蹤端點

### PHP 擴展需求
- `curl` - 用於 FAQ 代理
- `json` - Schema.org JSON-LD 編碼
- `spl` (標準) - 異常處理

### 快取系統
- APCu (可選) - FAQ 1 小時快取，提升效能
- 無時自動降級至無快取

---

## 9. 技術架構圖

```
用戶訪問 Park/Article/FAQ 頁面
    ↓
pageHeader.php
├─ Meta Tags (og:*, description, robots)
├─ Canonical (缺失)
├─ hreflang (缺失)
└─ Title
    ↓
[park.php | article.php]
├─ ContentRepository::getParkData()
├─ SkiResort / Article Schema.org
└─ FAQ Section (可選)
    ↓
includes/faq_proxy.php (或 faq_helpers.php)
├─ 代理 https://faq.diy.ski (優先)
├─ APCu 快取 (1 小時)
├─ 提取 HTML + Schema
└─ 分析追蹤 (異步)
    ↓
includes/components/faq.php
├─ 渲染 HTML (accordion)
├─ 注入 FAQPage Schema
└─ 互動事件

最終 HTML 輸出
└─ All Meta Tags + Schemas
```

---

## 總結

**當前實作評分**: 7.5/10

**強項**:
- Schema.org 結構化資料 (SkiResort, Article, FAQPage)
- FAQ 代理系統完善，支援快取和分析
- Open Graph tags 大部分實現

**待改善**:
- 缺少 Canonical 和 hreflang (SEO 關鍵)
- FAQ Schema 細節不完整
- 缺少其他 Schema 類型 (Person, Breadcrumb, Organization)

**建議優先順序**: Canonical → hreflang → FAQ Schema → 其他 Schema

