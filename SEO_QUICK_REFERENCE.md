# SKIDIY SEO 優化 - 快速參考指南

## 遷移前 48 小時檢查清單

### Priority 1: 關鍵實施 (必做)

- [ ] **AI Search Meta** (5 分鐘)
  ```html
  <meta name="allow-ai" content="true">
  <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
  ```
  位置: pageHeader.php, 第 35-36 行之後

- [ ] **Meta Description 檢查** (15 分鐘)
  - 驗證所有 description 在 120-155 字元內
  - 使用 Lighthouse 檢查: `lighthouse https://diy.ski`
  - 或在線檢查: https://metatags.io/

- [ ] **Robots.txt 更新** (10 分鐘)
  - 添加 AI 爬蟲允許規則
  - 確保 Sitemap 在底部
  - 位置: /robots.txt

### Priority 2: Schema 增強 (提升 SERP)

- [ ] **SkiResort Schema** (1 小時)
  - 添加 amenityFeature, contactPoint, openingHours
  - 位置: park.php, 第 30-84 行

- [ ] **Article Schema** (45 分鐘)
  - 添加 wordCount, articleBody, keywords
  - 位置: article.php, 第 33-61 行

### Priority 3: 其他改進

- [ ] Twitter Meta Tags (5 分鐘)
- [ ] Content Structure (2-3 小時, 可選)
- [ ] 內部連接 (1 小時, 可選)

---

## 快速代碼片段

### 1. AI Meta 標籤 (複製粘貼)

```html
<!-- 在 pageHeader.php <meta charset> 後方添加 -->
<meta name="allow-ai" content="true">
<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
<meta name="googlebot" content="index, follow, max-snippet:-1, max-image-preview:large">
<meta name="bingbot" content="index, follow, max-snippet:-1, max-image-preview:large">
<meta name="copyright" content="Copyright 2025 SKIDIY 自助滑雪">
<meta name="license" content="https://diy.ski/license">
```

### 2. Robots.txt 更新 (完整版本)

複製整個檔案內容替換現有的 robots.txt:

詳見: SEO_IMPLEMENTATION_CODE.md 的 "6️⃣ Robots.txt 增強" 章節

### 3. Park Schema 快速補充 (copy-paste ready)

在 park.php 第 42 行後添加:

```php
// 簡易版本 - 快速添加最關鍵的欄位
'sameAs' => !empty($park_info['official_url']) 
    ? [$park_info['official_url']] 
    : [],

'contactPoint' => [
    '@type' => 'ContactPoint',
    'contactType' => 'Customer Service',
    'telephone' => '+886-2-2881-0000',
    'email' => 'service@diy.ski',
    'availableLanguage' => ['zh-TW', 'en', 'ja']
],

'amenityFeature' => [
    [
        '@type' => 'LocationFeatureSpecification',
        'name' => 'Lessons Available',
        'value' => true
    ],
    [
        '@type' => 'LocationFeatureSpecification',
        'name' => 'Chinese Language Support',
        'value' => true
    ]
]
```

---

## 驗證工具 (立即檢查)

| 工具 | 用途 | 結果位置 |
|------|------|--------|
| https://validator.schema.org/ | Schema 驗證 | JSON-LD 是否有效 |
| https://metatags.io/ | Meta 預覽 | 搜尋結果預覽 |
| https://search.google.com/test/rich-results | Rich Results | 特殊結果資格 |
| https://search.google.com/test/mobile-friendly | 行動友善 | 響應式問題 |
| https://pagespeed.web.dev/ | Core Web Vitals | 頁面速度 |

### 快速測試命令

```bash
# 檢查 Meta 標籤
curl -s https://diy.ski/park.php?name=naeba | \
  grep -E '<meta|<title' | head -20

# 檢查 Schema
curl -s https://diy.ski/park.php?name=naeba | \
  grep -o '<script type="application/ld+json">[^<]*</script>' | \
  head -1 | python3 -m json.tool

# 檢查 Robots.txt
curl -s https://diy.ski/robots.txt

# 檢查 Sitemap
curl -s https://diy.ski/sitemap.xml.php | head -30
```

---

## 文件位置速查

| 需要修改的檔案 | 優先級 | 修改項目 |
|--------------|------|--------|
| pageHeader.php | P1 | 添加 AI Meta 標籤 |
| park.php | P2 | 增強 SkiResort Schema |
| article.php | P2 | 增強 Article Schema |
| robots.txt | P1 | 添加 max-snippet 等指令 |
| includes/sdk.php | P1 | 添加 Meta Description 優化函數 |

---

## 遷移時 DNS 注意事項

1. **舊站點**: 保持 robots.txt 和 sitemap.xml 可訪問 (301 重定向)
2. **新站點**: 所有 meta 標籤已更新 (pre-migration)
3. **過渡期**: 在 Google Search Console 中設置地址變更

---

## 預期效果時間表

| 時間 | 效果 |
|------|------|
| 1 週 | AI 爬蟲開始索引新的 meta 標籤 |
| 2 週 | Google 搜尋摘要更新 |
| 4 週 | Schema 增強開始顯示豐富結果 |
| 8 週 | SERP 位置改善可見 |

---

## 已驗證項目 (✅ 目前已有)

- ✅ JSON-LD Schema 基礎 (Article, SkiResort, Breadcrumb, FAQPage)
- ✅ Open Graph 標籤 (og:image, og:title, og:description)
- ✅ hreflang 多語言標籤
- ✅ Sitemap.xml 生成
- ✅ Robots.txt 基本規則
- ✅ Canonical URL

---

## 缺失項目 (⚠️ 需要添加)

- ⚠️ AI 友善 meta 標籤 (allow-ai, max-snippet)
- ⚠️ SkiResort Schema 的 amenityFeature
- ⚠️ Article Schema 的 wordCount, articleBody
- ⚠️ Twitter Card 標籤
- ⚠️ 內容結構語義標記

---

## 常見問題

**Q: 需要停機嗎?**
A: 否。所有更改可在生產環境即時實施，無需停機。

**Q: 多久會看到效果?**
A: 
- AI 爬蟲: 1-2 週
- Google SERP: 2-4 週
- 排名改善: 4-8 週

**Q: 會影響現有排名嗎?**
A: 不會。這些都是加強項，不涉及 URL 或內容結構改變。

**Q: 是否需要重新提交 sitemap?**
A: 是。遷移後在 GSC 中提交新 sitemap。

**Q: 如何驗證實施是否成功?**
A: 
1. 使用 https://validator.schema.org/ 檢查 Schema
2. 使用 https://metatags.io/ 預覽 Meta 標籤
3. 在 Google Search Console 中檢查索引狀態

---

## 支援文檔

- 完整分析: `SEO_OPTIMIZATION_ANALYSIS.md`
- 實施代碼: `SEO_IMPLEMENTATION_CODE.md`
- 項目規範: `CLAUDE.md`

---

## 最後檢查清單 (遷移前)

- [ ] 所有優先級 1 項目已實施
- [ ] Meta 標籤在瀏覽器開發工具中可見
- [ ] Schema 驗證通過 (無錯誤警告)
- [ ] Robots.txt 包含 AI 爬蟲規則
- [ ] Sitemap 包含所有公開頁面
- [ ] 所有內部連接有效
- [ ] Mobile 頁面正確渲染 Meta 標籤

**準備就緒？開始實施吧！** 預計只需 **1.5-2 小時** 完成所有優先級項目。

