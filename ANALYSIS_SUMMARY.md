# FAQ 系統分析執行摘要

## 核心發現

### 1. FAQ 資料層 - 三層架構
你的系統使用了巧妙的**三層架構**來管理 FAQ：

#### 層級 1: 硬編碼本地 FAQ (`faq_helpers.php`)
- **用途**: 雪場特定的快速 FAQ
- **規模**: 每個雪場 2-3 個 FAQ
- **維護**: 直接在 PHP 中修改

#### 層級 2: 代理外部 FAQ (`faq_proxy.php`)
- **來源**: `https://faq.diy.ski`
- **功能**: 完整的 FAQ 知識庫，支援多語言
- **快取**: APCu (1 小時)
- **分析**: 追蹤 FAQ 點擊

#### 層級 3: 靜態 FAQ HTML (`/faq/*.html`)
- **格式**: 完整的獨立頁面
- **內容**: Schema.org FAQPage + hreflang
- **維護**: 手動或自動化生成

---

### 2. Schema.org 實作評分

#### 優秀 (5/5 ⭐)
- SkiResort Schema (Park 頁面)
- Article Schema (文章頁面)
- FAQPage Schema (FAQ 靜態檔案)
- FAQ 代理系統 Schema 導入

#### 良好 (4/5 ⭐)
- Open Graph 標籤 (og:*)
- Meta Description (動態生成)
- hreflang (FAQ 頁面中)

#### 缺失 (0/5)
- Canonical 標籤 (所有頁面)
- hreflang (Park/Article 頁面)
- og:image 尺寸 (大部分頁面)
- Breadcrumb Schema
- Person Schema (Instructor)
- Organization Schema

---

## 檔案對應表

| 功能 | 檔案位置 | 行數 | 優先級 |
|------|--------|------|--------|
| 雪場 FAQ 定義 | `/includes/faq_helpers.php` | 39 | 改進 |
| FAQ 代理邏輯 | `/includes/faq_proxy.php` | 337 | 保持 |
| FAQ 組件渲染 | `/includes/components/faq.php` | 64 | 改進 |
| Meta Tags | `/pageHeader.php` | 158 | 修復 |
| Park Schema | `/park.php` | 84 | 保持 |
| Article Schema | `/article.php` | 73 | 保持 |
| 內容倉庫 | `/includes/content_repository.php` | 217 | 參考 |

---

## 改進優先順序

### Phase 1: 立即修復 (SEO 關鍵) - 1-2 小時
1. **添加 Canonical 標籤** (pageHeader.php)
   ```html
   <link rel="canonical" href="https://diy.ski/park.php?name={name}" />
   ```

2. **完善 og:image 尺寸** (pageHeader.php)
   ```html
   <meta property="og:image:width" content="1200">
   <meta property="og:image:height" content="630">
   ```

### Phase 2: 重要改進 (SEO 加強) - 2-3 小時
3. **添加 hreflang 支援** (pageHeader.php)
   ```html
   <link rel="alternate" hreflang="zh-Hant" href="https://diy.ski/park.php?name={name}&lang=zh" />
   <link rel="alternate" hreflang="en" href="https://diy.ski/park.php?name={name}&lang=en" />
   ```

4. **改進 FAQ Schema 細節** (faq.php)
   - 保留 HTML 而非 `strip_tags()`
   - 添加 `url` 和 `author` 欄位

### Phase 3: 增強功能 (額外優化) - 3-4 小時
5. **添加 Breadcrumb Schema**
6. **添加 Person Schema (Instructor)**
7. **遷移 FAQ 至 JSON** (維護優化)

---

## 檔案結構概覽

### FAQ 相關
```
/includes/
├── faq_helpers.php            ← 雪場 FAQ (改進)
├── faq_proxy.php              ← 外部代理 (保持)
└── components/faq.php         ← 組件 (改進)

/faq/
└── faq.{category}.{id}-{lang}.html
```

### Meta/Schema
```
/pageHeader.php                ← Meta Tags (修復 + 完善)
/park.php                      ← Park Schema (保持)
/article.php                   ← Article Schema (保持)
/includes/content_repository.php ← SEO 倉庫 (參考)
```

### 資料層
```
/includes/
├── db.class.php              ← SQLite 連線
├── mj.class.php              ← PARKS/ARTICLE 類
└── config.php                ← 配置

/database/
├── parks.json                ← 雪場資料
├── articles.json             ← 文章資料
└── instructors.json          ← 教練資料
```

---

## 快速檢查清單

### 當前狀況
- [x] SkiResort Schema 實作
- [x] Article Schema 實作
- [x] FAQPage Schema 實作
- [x] FAQ 代理系統
- [x] Open Graph 標籤
- [ ] Canonical 標籤
- [ ] hreflang 完整支援
- [ ] og:image 完整尺寸
- [ ] Breadcrumb Schema
- [ ] Person Schema

### 建議修改
1. **pageHeader.php** - 添加 canonical + hreflang + og:image 尺寸
2. **faq.php** - 改進 FAQ Schema，保留 HTML
3. **faq_helpers.php** - (可選) 遷移至 JSON 儲存

---

## 關鍵代碼片段

### 添加 Canonical (pageHeader.php)
```php
<?php if($target=='park'){ ?>
  <link rel="canonical" href="https://diy.ski/park.php?name=<?=$name?>" />
<?php }else if($target=='article'){ ?>
  <link rel="canonical" href="https://diy.ski/article.php?idx=<?=$article_id?>" />
<?php } ?>
```

### 改進 FAQ Schema (faq.php)
```php
// 當前: strip_tags($faq['a'])
// 改為: $faq['a'] (保留 HTML)
// 添加:
"url": "https://faq.diy.ski/faq/<?=$faqId?>",
"author": {
  "@type": "Organization",
  "name": "SKIDIY 自助滑雪"
}
```

### 添加 og:image 尺寸 (pageHeader.php)
```php
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:type" content="image/jpeg">
```

---

## 部署建議

1. **在 feature/schema-refactor 分支上進行修改**
   - 已建立，無需新建

2. **修改順序**
   - pageHeader.php (最影響範圍)
   - faq.php (FAQ 相關)
   - 其他頁面 (可選)

3. **測試方法**
   - Google Rich Results Test: https://search.google.com/test/rich-results
   - Facebook Debugger: https://developers.facebook.com/tools/debug/
   - Schema.org Validator: https://validator.schema.org/

4. **驗證檢查**
   - Park 頁面: Canonical + hreflang + og:image 尺寸 + SkiResort Schema
   - Article 頁面: 同上 (使用 Article Schema)
   - FAQ 頁面: FAQPage Schema + 完整欄位

---

## 資源連結

### SEO 工具
- Google Rich Results: https://search.google.com/test/rich-results
- Facebook Debugger: https://developers.facebook.com/tools/debug/
- Schema.org Validator: https://validator.schema.org/

### Schema.org 文檔
- FAQPage: https://schema.org/FAQPage
- SkiResort: https://schema.org/SkiResort
- Article: https://schema.org/Article
- Person: https://schema.org/Person
- Breadcrumb: https://schema.org/BreadcrumbList

### Meta Tags
- Open Graph: https://ogp.me/
- hreflang 指南: https://developers.google.com/search/docs/beginner/hreflang

---

## 下一步行動

1. 閱讀完整分析報告: `FAQ_IMPLEMENTATION_ANALYSIS.md`
2. 在 feature/schema-refactor 上提交 PR
3. Phase 1 修改 (立即): Canonical + og:image 尺寸
4. Phase 2 修改 (重要): hreflang + FAQ Schema
5. Phase 3 修改 (可選): 其他 Schema

---

**分析完成日期**: 2025-11-13  
**分析工程師**: Claude Code  
**報告位置**: `/FAQ_IMPLEMENTATION_ANALYSIS.md` (完整版)
