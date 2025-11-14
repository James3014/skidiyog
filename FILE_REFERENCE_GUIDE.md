# FAQ 系統檔案速查指南

## 絕對路徑對應

### FAQ 定義和管理
| 功能 | 絕對路徑 | 重要性 | 狀態 |
|------|--------|--------|------|
| 雪場 FAQ 定義 | `/Users/jameschen/Downloads/diyski/crm/03_FAQ與知識庫/zeabur/skidiyog/includes/faq_helpers.php` | 中 | ⚠️ 需改進 |
| FAQ 代理邏輯 | `/Users/jameschen/Downloads/diyski/crm/03_FAQ與知識庫/zeabur/skidiyog/includes/faq_proxy.php` | 高 | ✓ 優秀 |
| FAQ 嵌入邏輯 | `/Users/jameschen/Downloads/diyski/crm/03_FAQ與知識庫/zeabur/skidiyog/includes/faq_embed.php` | 中 | ? 未查看 |
| FAQ 組件 | `/Users/jameschen/Downloads/diyski/crm/03_FAQ與知識庫/zeabur/skidiyog/includes/components/faq.php` | 高 | ⚠️ 需改進 |

### Meta Tags 和 Schema
| 功能 | 絕對路徑 | 重要性 | 狀態 |
|------|--------|--------|------|
| Page Header | `/Users/jameschen/Downloads/diyski/crm/03_FAQ與知識庫/zeabur/skidiyog/pageHeader.php` | 極高 | ⚠️ 需修復 |
| Park 頁面 | `/Users/jameschen/Downloads/diyski/crm/03_FAQ與知識庫/zeabur/skidiyog/park.php` | 高 | ✓ 優秀 |
| Article 頁面 | `/Users/jameschen/Downloads/diyski/crm/03_FAQ與知識庫/zeabur/skidiyog/article.php` | 高 | ✓ 優秀 |
| Instructor 頁面 | `/Users/jameschen/Downloads/diyski/crm/03_FAQ與知識庫/zeabur/skidiyog/instructor.php` | 中 | ✗ 缺失 Person Schema |

### 資料層
| 功能 | 絕對路徑 | 重要性 | 狀態 |
|------|--------|--------|------|
| 資料庫類 | `/Users/jameschen/Downloads/diyski/crm/03_FAQ與知識庫/zeabur/skidiyog/includes/db.class.php` | 高 | ✓ 穩定 |
| 業務類 | `/Users/jameschen/Downloads/diyski/crm/03_FAQ與知識庫/zeabur/skidiyog/includes/mj.class.php` | 高 | ✓ 穩定 |
| 內容倉庫 | `/Users/jameschen/Downloads/diyski/crm/03_FAQ與知識庫/zeabur/skidiyog/includes/content_repository.php` | 高 | ✓ 優秀 |
| SDK 入口 | `/Users/jameschen/Downloads/diyski/crm/03_FAQ與知識庫/zeabur/skidiyog/includes/sdk.php` | 高 | ✓ 穩定 |

### 組件庫
| 功能 | 絕對路徑 | 重要性 | 狀態 |
|------|--------|--------|------|
| Hero 組件 | `/Users/jameschen/Downloads/diyski/crm/03_FAQ與知識庫/zeabur/skidiyog/includes/components/hero.php` | 中 | ✓ 穩定 |
| Nav 組件 | `/Users/jameschen/Downloads/diyski/crm/03_FAQ與知識庫/zeabur/skidiyog/includes/components/nav.php` | 中 | ✓ 穩定 |
| LeftNav 組件 | `/Users/jameschen/Downloads/diyski/crm/03_FAQ與知識庫/zeabur/skidiyog/includes/components/leftnav.php` | 中 | ✓ 穩定 |
| 分區組件 | `/Users/jameschen/Downloads/diyski/crm/03_FAQ與知識庫/zeabur/skidiyog/includes/components/page_sections.php` | 中 | ✓ 穩定 |

### FAQ 靜態檔案
| 檔案名稱 | 絕對路徑 | 內容 | 多語言 |
|--------|--------|------|--------|
| FAQ 009 | `/Users/jameschen/Downloads/diyski/crm/03_FAQ與知識庫/zeabur/skidiyog/faq/faq.general.009-zh.html` | 幾歲可開始學滑雪 | ✓ (zh/en/th) |
| FAQ 010 | `/Users/jameschen/Downloads/diyski/crm/03_FAQ與知識庫/zeabur/skidiyog/faq/faq.general.010-zh.html` | ? | ? |
| FAQ 011 | `/Users/jameschen/Downloads/diyski/crm/03_FAQ與知識庫/zeabur/skidiyog/faq/faq.general.011-zh.html` | ? | ? |

### 資料檔
| 資料 | 絕對路徑 | 格式 | 用途 |
|------|--------|------|------|
| 雪場資料 | `/Users/jameschen/Downloads/diyski/crm/03_FAQ與知識庫/zeabur/skidiyog/database/parks.json` | JSON | 雪場基本資訊 |
| 文章資料 | `/Users/jameschen/Downloads/diyski/crm/03_FAQ與知識庫/zeabur/skidiyog/database/articles.json` | JSON | 文章列表 |
| 教練資料 | `/Users/jameschen/Downloads/diyski/crm/03_FAQ與知識庫/zeabur/skidiyog/database/instructors.json` | JSON | 教練資訊 |

---

## 主要類和函式

### PARKS 類 (mj.class.php)
```php
$parks = new PARKS();
$parks->getParkInfo()                    // 取所有雪場
$parks->getParkInfo_by_Name($name)       // 取特定雪場
$parks->CheckParkContent_by_Name($name)  // 檢查雪場是否存在
$parks->update($name, $section, $data)   // 更新雪場資訊
```

### ARTICLE 類 (mj.class.php)
```php
$article = new ARTICLE();
$article->listing()                      // 列出所有文章
$article->readByIdx($idx)                // 取特定文章
$article->add($data)                     // 新增文章
$article->update($idx, $data)            // 更新文章
$article->delete($idx)                   // 刪除文章
```

### ContentRepository 類 (content_repository.php)
```php
ContentRepository::getParkData($name)              // 取雪場完整資料 (含 SEO)
ContentRepository::getArticleData($idx)            // 取文章完整資料 (含 SEO)
ContentRepository::getParkSectionsDefinition()     // 取雪場分區標籤
ContentRepository::getParkRedirect($name)          // 取雪場重導向
ContentRepository::shouldHideArticle($idx)         // 檢查是否隱藏文章
```

### FAQ 相關函式
```php
getParkFAQs($parkName)                  // 取雪場 FAQ (faq_helpers.php)
renderFAQSection($faqs, $title)         // 渲染 FAQ 區塊 (faq.php)
renderFAQProxy($faqIds, $lang)          // 代理 FAQ 渲染 (faq_proxy.php)
fetchFAQContent($faqId, $lang)          // 從 faq.diy.ski 抓取 (faq_proxy.php)
injectFAQSchema($faqs)                  // 注入 FAQ Schema (faq_proxy.php)
renderRecommendedFAQsProxy($category)   // 推薦相關 FAQ (faq_proxy.php)
renderParkFAQsProxy($parkName)          // 雪場專屬 FAQ (faq_proxy.php)
```

### 頁面型態檢測
```php
// pageHeader.php 中使用的 $target 變數決定 Meta Tags:
$target = 'park'        // Park 頁面
$target = 'article'     // Article 頁面
$target = 'instructor'  // Instructor 頁面
$target = 'index'       // 首頁
$target = 'schedule'    // 課表頁面
$target = 'booking'     // 訂購頁面
$target = 'instructors' // 教練列表
```

---

## 代碼修改指南

### 1. 添加 Canonical 標籤

**位置**: pageHeader.php (第 52-140 行之間)

**程式碼**:
```php
<?php if($target=='park'){
    $park = new PARKS();
    $desc = $park->info($name, 'desc');
    ...
    ?>
    <!-- 添加以下行 -->
    <link rel="canonical" href="https://diy.ski/park.php?name=<?=htmlspecialchars($name)?>" />
    <meta property="og:url" content="https://diy.ski/park.php?name=<?=htmlspecialchars($name)?>" />
    ...
```

**類似修改**: 為 article, instructor 等頁面型態添加 canonical

---

### 2. 改進 FAQ Schema

**位置**: includes/components/faq.php (第 26-43 行)

**修改前**:
```php
"text": "<?= addslashes(strip_tags($faq['a'])) ?>"
```

**修改後**:
```php
"text": "<?= addslashes(html_entity_decode($faq['a'])) ?>"
```

**添加欄位**:
```php
"url": "https://faq.diy.ski/faq/<?= $faqId ?>",
"author": {
  "@type": "Organization",
  "name": "SKIDIY 自助滑雪"
}
```

---

### 3. 完善 og:image 尺寸

**位置**: pageHeader.php (各個 og:image 標籤後)

**添加**:
```php
<meta property="og:image" content="..." />
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:type" content="image/jpeg">
```

---

## 快速除錯技巧

### 檢查 Park FAQ
```bash
grep -n "getParkFAQs" /Users/jameschen/Downloads/diyski/crm/03_FAQ與知識庫/zeabur/skidiyog/*.php
```

### 尋找 Schema 實作
```bash
grep -n "schema\|FAQPage\|SkiResort" /Users/jameschen/Downloads/diyski/crm/03_FAQ與知識庫/zeabur/skidiyog/*.php
```

### 查看 Meta Tags
```bash
grep -n "og:\|meta.*name\|canonical" /Users/jameschen/Downloads/diyski/crm/03_FAQ與知識庫/zeabur/skidiyog/pageHeader.php
```

### 測試 FAQ 代理
```bash
curl -s https://faq.diy.ski/faq/faq.general.009-zh.html | head -20
```

---

## 相關資源

### Schema.org 文檔
- FAQPage: https://schema.org/FAQPage
- SkiResort: https://schema.org/SkiResort
- Article: https://schema.org/Article
- Breadcrumb: https://schema.org/BreadcrumbList
- Person: https://schema.org/Person

### Meta Tags 規範
- Open Graph: https://ogp.me/
- Twitter Card: https://developer.twitter.com/en/docs/twitter-for-websites/cards
- Canonical: https://en.wikipedia.org/wiki/Canonical_link_element

### SEO 測試工具
- Google Rich Results Test: https://search.google.com/test/rich-results
- Facebook Debugger: https://developers.facebook.com/tools/debug/
- Bing Webmaster Tools: https://www.bing.com/webmaster/tools

---

**最後更新**: 2025-11-13  
**所有路徑**: macOS 絕對路徑格式
