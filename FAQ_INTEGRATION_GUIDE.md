# FAQ 卡片整合指南

## 概述

FAQ Proxy 系統允許您在任何頁面中嵌入來自 `faq.diy.ski` 的 FAQ 內容，並自動追蹤點擊數據。

## 快速開始

### 1. 基本用法

```php
<?php
// 在任何 PHP 頁面中引入 FAQ Proxy
require_once __DIR__ . '/includes/faq_proxy.php';

// 方法 A: 手動指定 FAQ ID
$faqIds = ['faq.general.009', 'faq.general.010', 'faq.general.011'];
renderFAQProxy($faqIds, 'zh');

// 方法 B: 根據分類自動推薦
renderRecommendedFAQsProxy('kids', 5, 'zh');

// 方法 C: 雪場專屬 FAQ
renderParkFAQsProxy('野澤', 'zh');
?>
```

## 三種整合方式詳解

### 方法 A: 手動選擇 FAQ（最精確）

**適用場景**: 您明確知道要顯示哪些 FAQ

```php
<?php
require_once __DIR__ . '/includes/faq_proxy.php';

// 指定要顯示的 FAQ ID
$faqIds = [
    'faq.general.009',  // 3 歲小孩可以上雪板課嗎？
    'faq.general.010',  // 不同年齡可以同班嗎？
    'faq.general.011',  // 小朋友上課有哪些安全機制？
];

// 渲染 FAQ 卡片（zh = 中文, en = 英文, th = 泰文）
renderFAQProxy($faqIds, 'zh');
?>
```

**可用的 FAQ ID**:
- `faq.general.009` - 3 歲小孩可以上雪板課嗎？
- `faq.general.010` - 不同年齡可以同班嗎？
- `faq.general.011` - 小朋友上課有哪些安全機制？
- `faq.general.012` - 裝備租借相關
- `faq.general.013` - 預約流程相關
- `faq.grouping.007` - 團體課程相關
- `faq.grouping.008` - 私人課程相關
- `faq.course.005` - 課程安排相關
- `faq.course.006` - 教練資格相關

### 方法 B: 分類推薦（最智能）

**適用場景**: 根據頁面主題自動推薦相關 FAQ

```php
<?php
require_once __DIR__ . '/includes/faq_proxy.php';

// 根據文章分類顯示相關 FAQ
$category = 'kids';  // 可選: general, kids, gear, booking, instructor
$limit = 5;          // 最多顯示 5 個 FAQ
$lang = 'zh';        // 語言

renderRecommendedFAQsProxy($category, $limit, $lang);
?>
```

**可用分類**:
| 分類 | 說明 | 推薦的 FAQ |
|------|------|-----------|
| `general` | 一般資訊 | faq.general.009, 010, 011, 012, 013 |
| `kids` | 兒童滑雪 | faq.general.009, faq.grouping.007, 008 |
| `gear` | 裝備租借 | faq.general.011 |
| `booking` | 預約流程 | faq.general.012, 013 |
| `instructor` | 教練相關 | faq.course.005, 006 |

### 方法 C: 雪場專屬 FAQ

**適用場景**: 雪場介紹頁面

```php
<?php
require_once __DIR__ . '/includes/faq_proxy.php';

// 顯示特定雪場的 FAQ
renderParkFAQsProxy('野澤', 'zh');
?>
```

## 實際應用範例

### 範例 1: 文章頁面 (article.php)

```php
<?php
// article.php (已更新)

// ... 文章內容 ...

// 根據文章分類動態顯示 FAQ
require_once __DIR__ . '/includes/faq_proxy.php';

$category = 'general'; // 預設分類
if (isset($article_data['category'])) {
    $category = $article_data['category'];
}
renderRecommendedFAQsProxy($category, 5, 'zh');
?>
```

### 範例 2: 雪場頁面 (resort.php)

```php
<?php
// resort.php

// ... 雪場介紹內容 ...

require_once __DIR__ . '/includes/faq_proxy.php';

// 顯示雪場專屬 FAQ
renderParkFAQsProxy($resort_name, 'zh');
?>
```

### 範例 3: 課程介紹頁面

```php
<?php
// course.php

// ... 課程介紹內容 ...

require_once __DIR__ . '/includes/faq_proxy.php';

// 根據課程類型顯示不同 FAQ
if ($course_type == 'kids') {
    renderRecommendedFAQsProxy('kids', 5, 'zh');
} elseif ($course_type == 'private') {
    $faqIds = ['faq.grouping.008', 'faq.course.005', 'faq.course.006'];
    renderFAQProxy($faqIds, 'zh');
} else {
    renderRecommendedFAQsProxy('general', 5, 'zh');
}
?>
```

### 範例 4: 多語言支援

```php
<?php
require_once __DIR__ . '/includes/faq_proxy.php';

// 根據使用者語言偏好顯示
$lang = isset($_GET['lang']) ? $_GET['lang'] : 'zh';

// 支援的語言: zh (中文), en (英文), th (泰文)
renderRecommendedFAQsProxy('kids', 5, $lang);
?>
```

## 功能特色

### 1. ✅ SEO 優化
- 自動注入 Schema.org FAQPage 結構化資料
- Google Rich Results 支援
- 從 faq.diy.ski 同步內容，保持一致性

### 2. ✅ 點擊追蹤
- 自動追蹤 FAQ 展開/收合事件
- 數據發送到 `https://faq.diy.ski/api/v1/analytics/track-faq-view`
- 追蹤資料包含:
  - `faq_id` - FAQ 唯一識別碼
  - `clicked` - 是否點擊展開
  - `language` - 語言版本
  - `timestamp` - 點擊時間
  - `source` - 來源 (skidiyog)

### 3. ✅ 快取機制
- APCu 快取 1 小時
- 減少對 faq.diy.ski 的請求
- 提升載入速度

### 4. ✅ 響應式設計
- 自動適應桌面/平板/手機
- Material Icons 圖示
- 平滑的展開/收合動畫

## 技術細節

### 資料流程

```
使用者瀏覽文章
    ↓
article.php 呼叫 renderFAQProxy()
    ↓
檢查 APCu 快取
    ↓ (未快取)
cURL 請求 faq.diy.ski/faq/{id}-{lang}.html
    ↓
Regex 解析 HTML 提取:
  - 問題 (h1)
  - 答案 (div.faq-content)
  - 分類標籤 (p.badge)
  - Schema.org 資料 (script[type="application/ld+json"])
    ↓
注入 Schema.org 到 <head>
    ↓
渲染 FAQ 卡片
    ↓
使用者點擊展開
    ↓
JavaScript 發送追蹤事件到 faq.diy.ski API
```

### 效能指標

- **快取命中**: <5ms
- **快取未命中**: <300ms (含 cURL 請求)
- **頁面載入**: 不阻塞主要內容
- **追蹤請求**: 非同步，不影響使用者體驗

## 疑難排解

### 問題 1: FAQ 內容為空

**症狀**: 顯示空白的 FAQ 卡片

**解決方法**:
1. 檢查 FAQ ID 是否正確
2. 確認 `faq.diy.ski` 可訪問
3. 查看 PHP 錯誤日誌

```bash
# 檢查靜態檔案是否存在
curl -I https://faq.diy.ski/faq/faq.general.009-zh.html

# 應返回 HTTP 200
```

### 問題 2: 展開/收合不工作

**症狀**: 點擊問題後無法展開答案

**解決方法**:
1. 檢查 Material Icons CSS 是否載入
2. 確認沒有 JavaScript 錯誤
3. 檢查瀏覽器控制台

```javascript
// 在瀏覽器控制台執行
console.log(document.querySelectorAll('.faq-proxy-item').length);
// 應顯示 FAQ 卡片數量
```

### 問題 3: 追蹤數據未記錄

**症狀**: FAQ 點擊未出現在分析後台

**可能原因**:
1. CORS 設定問題
2. Analytics API 未部署
3. 網路問題

**檢查方法**:
```javascript
// 在瀏覽器控制台查看
// 點擊 FAQ 後應看到:
// ✅ Tracked FAQ click: faq.general.009
```

## 最佳實踐

### 1. 選擇合適的整合方式

- **精準控制**: 使用方法 A (手動選擇)
- **自動化**: 使用方法 B (分類推薦)
- **雪場頁面**: 使用方法 C (雪場專屬)

### 2. 限制 FAQ 數量

```php
// ❌ 避免: 顯示過多 FAQ
renderRecommendedFAQsProxy('general', 20, 'zh'); // 太多了

// ✅ 推薦: 3-5 個最相關的 FAQ
renderRecommendedFAQsProxy('general', 5, 'zh'); // 剛好
```

### 3. 根據頁面內容選擇 FAQ

```php
// 根據文章關鍵字決定分類
if (strpos($article_data['content'], '小朋友') !== false ||
    strpos($article_data['content'], '兒童') !== false) {
    $category = 'kids';
} elseif (strpos($article_data['content'], '裝備') !== false) {
    $category = 'gear';
} else {
    $category = 'general';
}

renderRecommendedFAQsProxy($category, 5, 'zh');
```

### 4. 多語言最佳實踐

```php
// 偵測使用者語言偏好
$supportedLangs = ['zh', 'en', 'th'];
$userLang = $_GET['lang'] ?? $_COOKIE['lang'] ?? 'zh';
$lang = in_array($userLang, $supportedLangs) ? $userLang : 'zh';

renderRecommendedFAQsProxy('kids', 5, $lang);
```

## 更新日誌

- **2025-11-08**: 初版發布
  - 支援三種整合方式
  - 自動點擊追蹤
  - Schema.org SEO 優化
  - APCu 快取機制

## 技術支援

如有問題,請檢查:
1. PHP 錯誤日誌: `/var/log/php-error.log`
2. 瀏覽器控制台: F12 → Console
3. 網路請求: F12 → Network → XHR

或參考測試頁面: `test_faq_proxy.php`
