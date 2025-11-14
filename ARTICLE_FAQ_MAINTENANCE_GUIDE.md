# 文章-FAQ 整合維護指南

**版本**: 1.0
**最後更新**: 2025-11-14
**系統**: SKIDIY 文章系統 + FAQ API

---

## 概述

本指南說明如何維護文章與 FAQ 之間的自動關聯，使得每篇文章都能自動顯示相關的常見問題。

### 技術架構

```
文章系統 (PHP)          FAQ 系統 (Node.js API)
  skidiyog.zeabur.app  ← 呼叫  → faq-api-v1.zeabur.app

articles 表 (tags)      ↓           /api/v1/faq/all
  #行程規劃             過濾          {crm_tags}
  #課程諮詢        ────────→    找出交集
  #雪具選購        (content_repo)  回傳 FAQ

顯示層
  article.php ←─── 展示相關 FAQ

```

---

## 1. 文章標籤系統

### 1.1 標籤格式

文章的 `tags` 欄位採用 **逗號分隔** 的格式，帶 `#` 符號：

```sql
-- 範例
tags = '#行程規劃,#旺季預約,#雪場資訊,#季節性'
tags = '#教練選擇,#課程諮詢,#滑雪課程,#初學指南'
tags = '#雪具選購,#單板裝備,#初學裝備,#租借建議'
```

### 1.2 標籤命名規則

**必須遵守以下規則**，以確保與 FAQ 系統正確匹配：

#### 核心教學標籤
```
#課程選擇       - 課程類型選擇、課程內容
#初學指南       - 初學者入門、第一次滑雪
#進階課程       - 進階技巧、中級以上
#進度分級       - 滑雪等級、能力評估
#教練選擇       - 選擇教練、教練配對
#教練安排       - 教練預約、教練分配
#課程安排       - 上課時間、課程安排
#課程諮詢       - 課程相關問題、建議
#教練服務       - 教練資訊、服務內容
```

#### 群組課程標籤
```
#同堂安排       - 混班上課、人數配置
#團體預約       - 團體課程、集體報名
#親子同堂       - 家庭課程、成人與兒童混班
#程度差異       - 不同程度同班
#課程費用       - 團體課程費用
```

#### 兒童與安全標籤
```
#兒童課程       - 小孩上課、兒童教學
#兒童滑雪       - 年齡限制、小孩滑雪
#年齡限制       - 最小/最大年齡
#親子滑雪       - 家庭課程、親子同行
#家庭課程       - 家庭課程、全家人上課
#安全保障       - 運動傷害、保護措施
#特殊需求       - 身障、輔具、客製課程
#輔具諮詢       - 適應性滑雪、輔具建議
```

#### 雪具標籤
```
#雪具選購       - 板子、雪杖、服裝選擇
#單板裝備       - Snowboard 相關裝備
#雙板裝備       - Ski 相關裝備
#初學裝備       - 初學者裝備建議
#租借建議       - 租借雪具、租借攻略
#防護用品       - 護具、頭盔、護膝
#裝備建議       - 裝備選擇、購買建議
```

#### 行程與規劃標籤
```
#行程規劃       - 旅遊計畫、行程安排
#旺季預約       - 旺季預約、熱門時段
#旺季訂房       - 住宿預約、訂房困難
#住宿問題       - 住宿推薦、訂房攻略
#訂房攻略       - 如何訂房、訂房策略
#雪場資訊       - 度假村資訊、雪場資訊
#季節性         - 春雪、冬季、四季滑雪
```

#### 費用與保險標籤
```
#保險須知       - 保險類型、保險選擇
#費用說明       - 課程費用、價格說明
#旅平險         - 旅遊平安險
#滑雪險         - 滑雪保險
#風險管理       - 保險、風險、保障
```

#### 工具與資源標籤
```
#工具使用       - APP、軟體、工具使用
#app推薦        - 應用程式推薦
#優惠推廣       - 優惠活動、折扣
#雪票優惠       - 雪票優惠、套票
#品牌合作       - 品牌合作、廠商推薦
#品牌文化       - 公司文化、品牌故事
#招聘資訊       - 工作機會、招聘
```

#### 特殊標籤
```
#技術細節       - 技術教學、細節說明
#運動傷害       - 傷害預防、醫學篇
#防護建議       - 如何避免受傷
#社群互動       - 雪友聚會、社群活動
#雪友聚會       - 聚會活動、交流
#職業發展       - 職涯規劃、教練職業
#國際資格       - 國際認證、CASI
#活動推廣       - 活動推廣、活動介紹
#行程安排       - 旅遊行程安排
#課程客製       - 客製課程、特殊需求
#國外課程       - 海外教學、加拿大等
```

### 1.3 如何選擇標籤

**原則**：
1. **最多 4 個標籤** - 避免過多，保持清晰
2. **只選擇最相關的** - 不要標籤濫用
3. **優先級順序** - 最相關的放前面

**範例**：

```
文章: "新手日本自助滑雪攻略"
選擇: #初學指南, #行程規劃, #課程選擇, #新手須知
原因: 這篇文章主要是新手入門指南 → #初學指南 優先級最高

文章: "親子及家族滑雪需注意的事項"
選擇: #親子同堂, #兒童課程, #安全保障, #家庭課程
原因: 專門針對家庭和親子上課的注意事項

文章: "步驟123如何挑選自己的滑雪裝備[Snowboard 雪板篇]"
選擇: #雪具選購, #單板裝備, #初學裝備, #租借建議
原因: 裝備選擇是核心，特別針對 Snowboard
```

---

## 2. 實施步驟

### 2.1 第一次部署（已完成）

#### 步驟 1️⃣：執行 SQL 更新

登入資料庫，執行以下 SQL 語句（查看文末的完整 SQL）：

```bash
# 使用 SQLite 命令行
sqlite3 /path/to/skidiyog.db < /tmp/article_tags_update.sql

# 或逐行執行 UPDATE 語句
sqlite3 /path/to/skidiyog.db
sqlite> UPDATE articles SET tags = '#行程規劃,#旺季預約,#雪場資訊' WHERE idx = 1;
```

#### 步驟 2️⃣：部署更新的 PHP 代碼

將修改後的 `content_repository.php` 部署到 Zeabur：

```bash
# 上傳到 skidiyog.zeabur.app
scp includes/content_repository.php user@server:/path/to/includes/
```

#### 步驟 3️⃣：測試驗證

訪問任一篇文章，確認頁面下方顯示相關 FAQ：

```
測試 URL: https://skidiyog.zeabur.app/article.php?idx=1
預期結果: 文章內容下方顯示 "相關常見問題" 區塊
```

### 2.2 新增文章的流程（未來）

當需要新增文章時：

**1️⃣ 寫文章內容**
- 標題、正文、圖片等

**2️⃣ 填寫 tags（最關鍵）**
- 參考本文第 1.2 節的標籤列表
- 選擇 2-4 個最相關的標籤
- 格式：`#tag1,#tag2,#tag3`

**3️⃣ 插入資料庫**

```sql
INSERT INTO articles
  (idx, title, tags, article, keyword, timestamp)
VALUES
  (
    39,                                      -- idx (遞增)
    '新文章標題',                             -- title
    '#初學指南,#課程選擇,#行程規劃',         -- tags (重要!)
    '<p>文章內容...</p>',                    -- article
    '關鍵字1 關鍵字2',                        -- keyword
    NOW()                                    -- timestamp
  );
```

**4️⃣ 驗證**
- 訪問 `article.php?idx=39`
- 確認頁面顯示相關 FAQ

---

## 3. FAQ 標籤對應參考

當編輯不確定如何選擇文章標籤時，可以參考這個表格：

| 文章主題 | 推薦 tags | FAQ intent | 理由 |
|---------|---------|-----------|------|
| 新手入門 | #初學指南, #課程選擇 | COURSE, GENERAL | 新手需要了解課程類型和基礎知識 |
| 教練選擇 | #教練選擇, #課程諮詢 | COURSE, BOOKING | 涉及教練配對和課程安排 |
| 親子課程 | #親子同堂, #兒童課程 | GROUPING, GENERAL | 特定針對家庭和小孩 |
| 雪具購買 | #雪具選購, #租借建議 | GEAR | 裝備選擇和租借 |
| 旅遊規劃 | #行程規劃, #旺季預約 | ITINERARY | 行程和預約相關 |
| 保險說明 | #保險須知, #費用說明 | PAYMENT | 費用和保障相關 |
| 進階技巧 | #進階課程, #進度分級 | COURSE | 進階滑雪者 |
| 雪場介紹 | #雪場資訊, #行程規劃 | SERVICE, ITINERARY | 度假村和位置資訊 |
| 安全防護 | #安全保障, #運動傷害 | GENERAL | 安全和健康 |
| 優惠活動 | #優惠推廣, #品牌合作 | BOOKING | 推廣和合作 |

---

## 4. 系統運作原理

### 4.1 流程圖

```
用戶訪問 article.php?idx=1
    ↓
ContentRepository::getArticleData(1)
    ↓
取得文章資料: {title, content, tags: "#行程規劃,#旺季預約"}
    ↓
檢查 tags 是否存在?
    ├─ 是 → getRelatedFAQsByTags("#行程規劃,#旺季預約")
    └─ 否 → 返回 related_faqs = []
    ↓
parseTags() 解析標籤
    ↓
fetchFAQData() 從 API 取得 FAQ
    ├─ 檢查快取 (5 分鐘內重用)
    └─ 呼叫 https://faq-api-v1.zeabur.app/api/v1/faq/all?lang=zh
    ↓
比對標籤：找出 FAQ.crm_tags 與 tags 的交集
    ↓
最多回傳 5 個相關 FAQ
    ↓
article.php 顯示相關 FAQ 區塊
    ↓
用戶看到 "相關常見問題" 並點擊查看完整答案
```

### 4.2 標籤匹配邏輯

```php
// 文章 tags
article.tags = "#行程規劃,#旺季預約,#雪場資訊"

// FAQ 的 crm_tags
faq.metadata.crm_tags = ["#行程規劃", "#優先順序", "#教練預約"]

// 匹配結果
交集 = {"#行程規劃"}  ✓ 有交集 → 包含此 FAQ

---

// 另一個例子
article.tags = "#雪具選購,#單板裝備"
faq.metadata.crm_tags = ["#親子同堂", "#兒童課程"]

// 匹配結果
交集 = {}  ✗ 無交集 → 不包含此 FAQ
```

---

## 5. 常見問題與排查

### Q1: 為什麼文章不顯示相關 FAQ？

**檢查清單**：
1. ✅ 文章的 `tags` 欄位是否有值？
   ```sql
   SELECT idx, title, tags FROM articles WHERE idx = 1;
   ```

2. ✅ tags 格式是否正確（逗號分隔）？
   ```
   正確: "#初學指南,#課程選擇"
   錯誤: "#初學指南 #課程選擇"  (空格分隔)
   錯誤: "初學指南,課程選擇"     (缺少 #)
   ```

3. ✅ FAQ API 是否可以訪問？
   ```bash
   curl https://faq-api-v1.zeabur.app/api/v1/faq/all?lang=zh
   ```

4. ✅ PHP 的 `error_log` 中是否有錯誤？
   ```bash
   tail -f /var/log/php-errors.log
   ```

### Q2: 文章 tags 和 FAQ crm_tags 不匹配怎麼辦？

**解決方案**：
- 檢查 FAQ 的實際 crm_tags 值
  ```bash
  curl https://faq-api-v1.zeabur.app/api/v1/faq/all?lang=zh | jq '.data.items[] | {id, crm_tags: .metadata.crm_tags}'
  ```

- 調整文章 tags 以匹配 FAQ
- 參考第 1.2 節的標籤列表

### Q3: 如何更新現有文章的 tags？

```sql
-- 更新單篇文章
UPDATE articles
SET tags = '#新標籤1,#新標籤2,#新標籤3'
WHERE idx = 5;

-- 批量更新（謹慎使用）
UPDATE articles
SET tags = '#初學指南'
WHERE title LIKE '%新手%';
```

### Q4: 如何調試標籤匹配？

在 PHP 代碼中加入 debug 代碼：

```php
// 在 content_repository.php 的 getRelatedFAQsByTags 函數
error_log('[DEBUG] Article Tags: ' . json_encode($articleTags));
error_log('[DEBUG] Related FAQs found: ' . count($relatedFAQs));
foreach ($relatedFAQs as $faq) {
    error_log('[DEBUG] FAQ: ' . $faq['question']);
}
```

---

## 6. 性能優化

### 6.1 快取策略

系統已內建快取機制，5 分鐘內重複請求不會重新呼叫 FAQ API：

```php
// 快取時間：5 分鐘
if ((time() - $GLOBALS[$cacheKey . '_time']) < 300) {
    return $GLOBALS[$cacheKey];
}
```

### 6.2 超時設定

FAQ API 請求超時設定為 5 秒，避免頁面卡住：

```php
'timeout' => 5,  // 5 秒超時
```

如果 FAQ API 無法訪問，文章仍可正常顯示，只是不會有相關 FAQ。

---

## 7. 前端顯示

### 7.1 HTML 結構（article.php）

```html
<?php if (!empty($articleData['related_faqs'])): ?>
  <section class="related-faqs-section">
    <h2>相關常見問題</h2>
    <div class="faq-items">
      <?php foreach ($articleData['related_faqs'] as $faq): ?>
        <article class="faq-item">
          <h3><?php echo htmlspecialchars($faq['question']); ?></h3>
          <p><?php echo htmlspecialchars($faq['answer_preview']); ?>...</p>
          <a href="https://faq.diy.ski/?q=<?php echo urlencode($faq['question']); ?>"
             class="faq-link" target="_blank">
            查看完整答案 →
          </a>
        </article>
      <?php endforeach; ?>
    </div>
  </section>
<?php endif; ?>
```

### 7.2 CSS 樣式建議

```css
.related-faqs-section {
  margin-top: 40px;
  padding: 20px;
  background: #f5f5f5;
  border-radius: 8px;
}

.related-faqs-section h2 {
  font-size: 1.5rem;
  margin-bottom: 20px;
  color: #333;
}

.faq-items {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 15px;
}

.faq-item {
  background: white;
  padding: 15px;
  border-radius: 5px;
  border-left: 4px solid #0066cc;
}

.faq-item h3 {
  font-size: 1rem;
  margin-bottom: 10px;
  color: #0066cc;
}

.faq-item p {
  font-size: 0.9rem;
  color: #666;
  line-height: 1.5;
  margin-bottom: 10px;
}

.faq-link {
  color: #0066cc;
  text-decoration: none;
  font-weight: bold;
}

.faq-link:hover {
  text-decoration: underline;
}
```

---

## 8. 未來改進方向

### 8.1 可能的增強

1. **多語言支援** - 支援 `lang=en, lang=th` 參數
2. **動態排序** - 根據相關性分數排序 FAQ
3. **使用者反饋** - 記錄用戶是否點擊相關 FAQ
4. **AI 自動標籤** - 未來可能使用 LLM 自動分類

### 8.2 監控指標

- FAQ API 呼叫次數/秒
- 平均響應時間
- 快取命中率
- 標籤匹配成功率

---

## 9. 附錄：完整 SQL 更新語句

所有現有文章的 tags 更新已在第 2.1 節執行。

如需重新執行或修改，使用以下語句：

```sql
-- [已執行] 2025-11-14
UPDATE articles SET tags = '#行程規劃,#旺季預約,#雪場資訊,#季節性' WHERE idx = 1;
UPDATE articles SET tags = '#教練選擇,#課程諮詢,#滑雪課程,#初學指南' WHERE idx = 8;
UPDATE articles SET tags = '#社群互動,#雪友聚會' WHERE idx = 9;
UPDATE articles SET tags = '#教練職業,#國際資格,#職涯發展' WHERE idx = 10;
UPDATE articles SET tags = '#同堂安排,#團體預約,#課程安排,#教練服務' WHERE idx = 11;
UPDATE articles SET tags = '#進階課程,#進度分級,#課程設計' WHERE idx = 12;
UPDATE articles SET tags = '#活動推廣,#品牌合作' WHERE idx = 13;
UPDATE articles SET tags = '#進階課程,#單板教學,#技術細節' WHERE idx = 14;
UPDATE articles SET tags = '#親子同堂,#兒童課程,#安全保障,#家庭課程' WHERE idx = 15;
UPDATE articles SET tags = '#保險須知,#費用說明,#旅平險,#風險管理' WHERE idx = 16;
UPDATE articles SET tags = '#初學指南,#行程規劃,#課程選擇,#新手須知' WHERE idx = 18;
UPDATE articles SET tags = '#進階課程,#單板教學,#技術細節' WHERE idx = 19;
UPDATE articles SET tags = '#進階課程,#單板教學,#技術細節' WHERE idx = 20;
UPDATE articles SET tags = '#安全保障,#運動傷害,#防護建議' WHERE idx = 21;
UPDATE articles SET tags = '#工具使用,#app推薦,#行程規劃' WHERE idx = 22;
UPDATE articles SET tags = '#雪具選購,#單板裝備,#初學裝備,#租借建議' WHERE idx = 23;
UPDATE articles SET tags = '#活動推廣,#行程安排' WHERE idx = 24;
UPDATE articles SET tags = '#品牌合作,#季節性活動' WHERE idx = 25;
UPDATE articles SET tags = '#同堂安排,#團體預約,#課程費用,#課程安排' WHERE idx = 26;
UPDATE articles SET tags = '#優惠推廣,#住宿推薦,#雪場資訊' WHERE idx = 27;
UPDATE articles SET tags = '#雪場資訊,#課程安排,#注意事項' WHERE idx = 28;
UPDATE articles SET tags = '#雪具選購,#防護用品,#裝備建議' WHERE idx = 29;
UPDATE articles SET tags = '#進度分級,#進階課程,#同堂安排' WHERE idx = 30;
UPDATE articles SET tags = '#雪具選購,#雙板裝備,#裝備建議,#租借建議' WHERE idx = 31;
UPDATE articles SET tags = '#教練選擇,#課程諮詢,#教練安排,#國外課程' WHERE idx = 32;
UPDATE articles SET tags = '#特殊需求,#輔具諮詢,#安全保障,#課程客製' WHERE idx = 33;
UPDATE articles SET tags = '#優惠推廣,#雪票優惠,#雪場資訊' WHERE idx = 34;
UPDATE articles SET tags = '#同堂安排,#進階課程,#團體預約,#課程安排' WHERE idx = 35;
UPDATE articles SET tags = '#品牌合作,#產品推廣' WHERE idx = 36;
UPDATE articles SET tags = '#招聘資訊,#品牌文化,#職涯發展' WHERE idx = 38;
```

---

## 聯絡與支援

如有問題或需要調整標籤系統，請聯絡開發團隊。

**快速檢查清單**：
- [ ] FAQ API 是否可訪問？
- [ ] 文章 tags 是否已更新？
- [ ] PHP 代碼已部署？
- [ ] 至少 3 篇文章已驗證？
- [ ] 新文章的 tags 填寫流程已培訓？
