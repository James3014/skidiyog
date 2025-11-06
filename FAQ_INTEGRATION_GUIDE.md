# FAQ 卡片整合指南 (FAQ Card Integration Guide)

**目的**: 在舊系統 (skidiyog) 的度假村介紹頁面整合新 FAQ 系統卡片
**狀態**: 待部署驗證後執行
**預期時間**: 2-3 小時開發 + 1 小時測試

---

## 1. 整合架構 (Integration Architecture)

```
┌─────────────────────────────────────────────┐
│      新系統 FAQ (faq.diy.ski)              │
│  Zeabur + Node.js + SQLite                  │
│  • 多語言搜尋 (ZH/EN/TH)                    │
│  • Intent 偵測與 Slot 擷取                  │
│  • API 端點: /api/v1/faq/search             │
└────────────────┬────────────────────────────┘
                 │ HTTP API 呼叫
                 ▼
┌─────────────────────────────────────────────┐
│      舊系統 (diy.ski - skidiyog)           │
│  PHP + MySQL                                │
│  • park.php: 度假村頁面 + FAQ 卡片渲染     │
│  • bkAdmin/parks.php: 後台 FAQ 插入按鈕    │
│  • faq-integration.js: 前端 API 呼叫       │
└─────────────────────────────────────────────┘
```

---

## 2. 前端整合: park.php 修改

### 2.1 添加 FAQ 卡片渲染函數

在 `park.php` 頂部添加 (line 5-20):

```php
<?php
// ... 現有代碼 ...

/**
 * 根據度假村名稱和章節取得相關 FAQ
 * @param string $resort_name 度假村名稱 (naeba, appi, nozawa 等)
 * @param string $section 章節名稱 (access, ticket, lesson 等)
 * @return string HTML FAQ 卡片集合
 */
function getFAQCards($resort_name, $section) {
    // FAQ API 端點 (需在 config 中配置)
    $faq_api_url = 'https://faq.diy.ski/api/v1/faq/search';

    // 將章節名稱映射到 FAQ 類別
    $section_to_faq_intent = [
        'access' => 'COURSE',      // 交通與課程相關
        'ticket' => 'PAYMENT',     // 票價與支付
        'lesson' => 'INSTRUCTOR',  // 課程與教練
        'kids' => 'KIDS_SAFETY',   // 兒童安全
        'gear' => 'GEAR',          // 裝備準備
        'rental' => 'GEAR',        // 租賃
        'remind' => 'SERVICE',     // 提醒與服務
        'default' => 'GENERAL'     // 一般問題
    ];

    $intent = $section_to_faq_intent[$section] ?? 'GENERAL';
    $query = "{$resort_name} {$intent}";

    // 呼叫 FAQ API
    $faq_json = @file_get_contents(
        $faq_api_url . '?query=' . urlencode($query) . '&limit=5'
    );

    if (!$faq_json) {
        return '';  // API 不可用時不顯示
    }

    $faq_data = json_decode($faq_json, true);
    if (empty($faq_data['data'])) {
        return '';  // 無相關 FAQ
    }

    return renderFAQCardsHTML($faq_data['data'], $resort_name);
}

/**
 * 渲染 FAQ 卡片 HTML
 */
function renderFAQCardsHTML($faqs, $resort_name) {
    if (empty($faqs)) return '';

    $html = '<div class="faq-cards-container" style="margin-top: 2rem;">';
    $html .= '<h3>相關常見問題</h3>';
    $html .= '<div class="faq-cards">';

    foreach (array_slice($faqs, 0, 3) as $faq) {
        $question = htmlspecialchars($faq['item']['canonical_question'] ?? '');
        $answer = htmlspecialchars(substr($faq['item']['answer'] ?? '', 0, 150)) . '...';
        $confidence = $faq['score'] ?? 75;
        $confidence_class = $confidence >= 80 ? 'high' : ($confidence >= 60 ? 'medium' : 'low');

        $html .= '<div class="faq-card" style="' . getFAQCardStyles() . '">';
        $html .= '<div class="faq-header">';
        $html .= '<h4 style="margin: 0;">' . $question . '</h4>';
        $html .= '<span class="confidence-badge ' . $confidence_class . '">' . $confidence . '%</span>';
        $html .= '</div>';
        $html .= '<div class="faq-answer">' . $answer . '</div>';
        $html .= '<a href="#" class="faq-link" style="color: #0066cc; text-decoration: none;">查看完整答案 →</a>';
        $html .= '</div>';
    }

    $html .= '</div>';
    $html .= '</div>';

    return $html;
}

/**
 * FAQ 卡片默認樣式
 */
function getFAQCardStyles() {
    return 'border: 1px solid #ddd; padding: 1rem; margin: 0.5rem 0; border-radius: 4px; background: #f9f9f9;';
}

// ... 現有代碼繼續 ...
?>
```

### 2.2 在內容後添加 FAQ 卡片

修改 line 91-100 (內容渲染部分):

```php
<?php
// ... 現有代碼 ...

if(!empty($section) && $section != 'all'){
  echo '<h1 id="intro">'.$SECTION_HEADER[$section].'</h1>';
  if($name=='appi' || $name=='naeba' || $name=='karuizawa'){
    echo $section_content[$section].'<hr>';
  }else{
    echo '<pre>'.$section_content[$section].'</pre><hr>';
  }

  // ↓ 新增: 添加 FAQ 卡片 ↓
  echo getFAQCards($name, $section);
  // ↑ 新增: FAQ 卡片結束 ↑
}

// ... 現有代碼繼續 ...
?>
```

### 2.3 添加前端 JavaScript (異步加載)

在 `<head>` 中添加或創建 `faq-integration.js`:

**文件**: `assets/js/faq-integration.js`

```javascript
/**
 * FAQ 卡片異步加載與交互
 */

(function() {
    'use strict';

    // API 配置
    const FAQ_API = {
        url: 'https://faq.diy.ski/api/v1/faq/search',
        timeout: 3000  // 3 秒超時
    };

    // 章節映射
    const SECTION_TO_INTENT = {
        'access': 'COURSE',
        'ticket': 'PAYMENT',
        'lesson': 'INSTRUCTOR',
        'kids': 'KIDS_SAFETY',
        'gear': 'GEAR',
        'rental': 'GEAR',
        'remind': 'SERVICE'
    };

    /**
     * 取得 FAQ 卡片
     */
    async function fetchFAQCards(resortName, section) {
        const intent = SECTION_TO_INTENT[section] || 'GENERAL';
        const query = `${resortName} ${intent}`;

        try {
            const response = await Promise.race([
                fetch(`${FAQ_API.url}?query=${encodeURIComponent(query)}&limit=5`),
                new Promise((_, reject) =>
                    setTimeout(() => reject(new Error('Timeout')), FAQ_API.timeout)
                )
            ]);

            if (!response.ok) throw new Error('API Error');
            return await response.json();
        } catch (error) {
            console.error('[FAQ] 加載失敗:', error);
            return null;
        }
    }

    /**
     * 渲染 FAQ 卡片 HTML
     */
    function renderFAQCards(faqs) {
        if (!faqs || !faqs.data || faqs.data.length === 0) {
            return '';
        }

        let html = '<div class="faq-cards-container" style="margin-top: 2rem;">';
        html += '<h3>相關常見問題</h3>';
        html += '<div class="faq-cards">';

        faqs.data.slice(0, 3).forEach(faq => {
            const question = escapeHtml(faq.item.canonical_question || '');
            const answer = escapeHtml((faq.item.answer || '').substring(0, 150)) + '...';
            const confidence = Math.round(faq.score || 75);
            const confClass = confidence >= 80 ? 'high' : (confidence >= 60 ? 'medium' : 'low');

            html += `
                <div class="faq-card" style="${getFAQCardCSS()}">
                    <div class="faq-header">
                        <h4 style="margin: 0;">${question}</h4>
                        <span class="confidence-badge confidence-${confClass}" style="font-size: 0.85em; padding: 0.25rem 0.5rem; border-radius: 3px;">
                            ${confidence}%
                        </span>
                    </div>
                    <div class="faq-answer" style="color: #666; margin: 0.5rem 0;">${answer}</div>
                    <a href="https://faq.diy.ski?q=${encodeURIComponent(faq.item.canonical_question)}"
                       target="_blank"
                       class="faq-link"
                       style="color: #0066cc; text-decoration: none;">
                        查看完整答案 →
                    </a>
                </div>
            `;
        });

        html += '</div></div>';
        return html;
    }

    /**
     * FAQ 卡片默認 CSS
     */
    function getFAQCardCSS() {
        return 'border: 1px solid #ddd; padding: 1rem; margin: 0.5rem 0; border-radius: 4px; background: #f9f9f9;';
    }

    /**
     * 轉義 HTML 字符
     */
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * 初始化 FAQ 卡片
     */
    async function initializeFAQCards() {
        // 取得度假村名稱與章節 (從 URL 參數)
        const params = new URLSearchParams(window.location.search);
        const resortName = params.get('name') || document.body.dataset.resort;
        const section = params.get('section') || document.body.dataset.section;

        if (!resortName || !section) return;

        // 尋找插入點
        const insertPoint = document.querySelector('.faq-cards-container');
        if (insertPoint) return;  // 已存在 (PHP 版本)

        // 異步加載 FAQ
        const faqs = await fetchFAQCards(resortName, section);
        if (faqs && faqs.data) {
            const container = document.querySelector('hr:last-of-type');
            if (container) {
                const html = renderFAQCards(faqs);
                container.insertAdjacentHTML('afterend', html);
            }
        }
    }

    // 頁面加載時初始化
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeFAQCards);
    } else {
        initializeFAQCards();
    }
})();
```

在 `park.php` 的 `</body>` 前添加:

```html
<script src="assets/js/faq-integration.js"></script>
```

---

## 3. 後台整合: bkAdmin/parks.php 修改

### 3.1 添加 FAQ 插入按鈕

在 line 130 (textarea 下方) 添加:

```html
<!-- 原代碼: -->
<textarea name="<?=$key?>" id="<?=$key?>" class="materialize-textarea"><?=$s_content?></textarea>

<!-- ↓ 新增: FAQ 插入按鈕 ↓ -->
<div style="margin-top: 1rem;">
    <button type="button"
            class="btn btn-info"
            onclick="showFAQInsertModal('<?=$key?>')"
            style="background: #0066cc; color: white; padding: 0.5rem 1rem; border: none; cursor: pointer;">
        [+] 插入 FAQ 卡片
    </button>
</div>
<!-- ↑ 新增: FAQ 按鈕結束 ↑ -->
```

### 3.2 添加 FAQ 搜尋與插入功能

在 `bkAdmin/parks.php` 底部 `</script>` 前添加:

```javascript
<script>
/**
 * FAQ 插入模態窗口管理
 */

// FAQ 搜尋函數
async function searchFAQ(query) {
    const input = document.getElementById('faq-search-input');
    if (!input.value.trim()) return;

    try {
        const response = await fetch(
            'https://faq.diy.ski/api/v1/faq/search?' +
            new URLSearchParams({
                query: input.value,
                limit: 10
            })
        );

        if (!response.ok) throw new Error('API Error');

        const data = await response.json();
        displayFAQResults(data.data || []);
    } catch (error) {
        console.error('FAQ 搜尋失敗:', error);
        alert('無法搜尋 FAQ，請稍後再試');
    }
}

// 顯示搜尋結果
function displayFAQResults(results) {
    const resultsDiv = document.getElementById('faq-results');
    resultsDiv.innerHTML = '';

    if (results.length === 0) {
        resultsDiv.innerHTML = '<p style="color: #999;">未找到相關 FAQ</p>';
        return;
    }

    results.forEach((result, index) => {
        const question = result.item.canonical_question || '';
        const answer = result.item.answer || '';
        const confidence = Math.round(result.score || 75);

        const html = `
            <div style="border: 1px solid #ddd; padding: 1rem; margin: 0.5rem 0; cursor: pointer; background: #f9f9f9;"
                 onmouseover="this.style.background='#efefef'"
                 onmouseout="this.style.background='#f9f9f9'"
                 onclick="insertFAQCard(${index})">
                <strong>${question}</strong><br>
                <small style="color: #999;">信心度: ${confidence}%</small>
            </div>
        `;
        resultsDiv.innerHTML += html;
    });
}

// 顯示 FAQ 插入模態
function showFAQInsertModal(fieldName) {
    const modal = `
        <div id="faq-modal" style="
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 9999;">
            <div style="background: white; padding: 2rem; border-radius: 8px; width: 90%; max-width: 600px;">
                <h2>搜尋並插入 FAQ 卡片</h2>
                <div style="margin: 1rem 0;">
                    <input type="text"
                           id="faq-search-input"
                           placeholder="搜尋 FAQ (例: 教練、價格、裝備)"
                           style="width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;"
                           onkeypress="if(event.key==='Enter') searchFAQ()">
                    <button onclick="searchFAQ()"
                            style="margin-top: 0.5rem; background: #0066cc; color: white; padding: 0.5rem 1rem; border: none; border-radius: 4px; cursor: pointer;">
                        搜尋
                    </button>
                </div>
                <div id="faq-results" style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 0.5rem; margin: 1rem 0;">
                </div>
                <div style="text-align: right;">
                    <button onclick="closeFAQModal()"
                            style="background: #ccc; color: black; padding: 0.5rem 1rem; border: none; border-radius: 4px; cursor: pointer;">
                        關閉
                    </button>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modal);
    document.getElementById('faq-search-input').focus();
}

// 關閉模態
function closeFAQModal() {
    const modal = document.getElementById('faq-modal');
    if (modal) modal.remove();
}

// 當前正在編輯的字段名稱和結果
let currentEditField = '';
let currentFAQResults = [];

// 插入 FAQ 卡片到文本區域
function insertFAQCard(index) {
    // 此功能可根據需要擴展
    alert('FAQ 卡片已準備插入。實際實現應修改 textarea 內容');
    closeFAQModal();
}

// ESC 鍵關閉模態
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeFAQModal();
});
</script>
```

---

## 4. 配置 (Configuration)

### 4.1 更新 `includes/config.example.php`

添加 FAQ API 配置:

```php
<?php
// ... 現有配置 ...

// FAQ 系統集成
define('FAQ_API_URL', getenv('FAQ_API_URL') ?: 'https://faq.diy.ski/api/v1');
define('FAQ_ENABLED', true);
define('FAQ_AUTO_INSERT', false);  // 自動插入 FAQ 卡片 (true=自動, false=後台手動)
define('FAQ_TIMEOUT', 3);  // 秒
?>
```

### 4.2 環境變量 (Zeabur)

添加到 Zeabur 環境變量:

```
FAQ_API_URL = https://faq.diy.ski/api/v1
FAQ_ENABLED = true
FAQ_AUTO_INSERT = false
```

---

## 5. 測試計劃 (Testing Plan)

### 5.1 本地測試

```bash
# 1. 啟動本地 PHP 伺服器
cd /path/to/skidiyog
php -S localhost:8000

# 2. 訪問度假村頁面
http://localhost:8000/park.php?name=naeba&section=access

# 3. 檢查瀏覽器控制台是否有錯誤
# 4. 驗證 FAQ 卡片是否正確顯示
```

### 5.2 Zeabur 測試

部署後在 https://your-zeabur-domain/park.php?name=naeba&section=access 測試:

- [ ] FAQ 卡片正確加載
- [ ] 沒有 CORS 錯誤
- [ ] 卡片樣式正確
- [ ] 鏈接指向正確的 FAQ 系統

### 5.3 後台測試

訪問 https://your-zeabur-domain/bkAdmin/parks.php:

- [ ] "[+] 插入 FAQ 卡片" 按鈕可見
- [ ] 點擊按鈕打開搜尋模態
- [ ] 搜尋功能正常運作
- [ ] 結果正確顯示

---

## 6. 故障排查 (Troubleshooting)

### 問題 1: FAQ 卡片未顯示
**原因**: FAQ API 不可用或超時
**解決**:
- 檢查 FAQ API 是否在線: `curl https://faq.diy.ski/api/v1/faq/search?query=test`
- 檢查瀏覽器控制台錯誤
- 增加超時時間

### 問題 2: CORS 錯誤
**原因**: FAQ 系統未配置 CORS
**解決**:
- 聯繫新系統開發者啟用 CORS
- 或添加代理層 (PHP 後端代理)

### 問題 3: 樣式不匹配
**原因**: 默認樣式不適應頁面設計
**解決**:
- 修改 `getFAQCardCSS()` 函數的樣式
- 根據頁面主題調整顏色、間距等

---

## 7. 集成檢查表 (Integration Checklist)

**開發階段**:
- [ ] 修改 `park.php` 添加 FAQ 函數
- [ ] 測試 FAQ 卡片渲染
- [ ] 添加 `faq-integration.js` 前端代碼
- [ ] 修改 `bkAdmin/parks.php` 添加按鈕
- [ ] 添加後台 FAQ 搜尋功能

**部署階段**:
- [ ] 提交代碼至 GitHub
- [ ] Zeabur 自動構建並部署
- [ ] Zeabur 環境變量已配置

**測試階段**:
- [ ] 本地測試全部功能
- [ ] Zeabur 測試無誤
- [ ] 性能測試 (加載時間 < 2s)
- [ ] 後台測試完成

**上線階段**:
- [ ] 文檔已更新
- [ ] FAQ 卡片集成完成
- [ ] 用戶通知已發送
- [ ] 監測告警已配置

---

## 8. 下一步

完成部署驗證後執行本指南。

**預期時間表**:
- 日期 1: 部署 Zeabur 環境 (見 DEPLOYMENT_GUIDE.md)
- 日期 2: 驗證環境配置 (見 DEPLOYMENT_CHECKLIST.md)
- 日期 3-4: FAQ 卡片集成 (本指南)
- 日期 5: 最終測試與上線

---

**文檔版本**: 1.0
**最後更新**: 2025-11-06
**相關文檔**: DEPLOYMENT_GUIDE.md, DEPLOYMENT_CHECKLIST.md
