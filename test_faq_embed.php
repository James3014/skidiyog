<?php
/**
 * FAQ Embed Component Testing Page
 *
 * 測試從 https://faq.diy.ski/ 嵌入 FAQ 區塊
 * 展示如何整合 Schema.org 結構化資料以利 SEO
 */

require_once 'includes/faq_embed.php';
require_once 'includes/booking_cta.php';

?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ 嵌入測試 - 從 faq.diy.ski 載入</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Microsoft JhengHei", sans-serif;
            line-height: 1.6; color: #333; background: #f5f5f5;
        }
        .test-header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white; padding: 40px 20px; text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .test-header h1 { font-size: 36px; margin-bottom: 10px; }
        .test-header p { font-size: 18px; opacity: 0.9; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        .test-section {
            background: white; margin: 40px auto; padding: 40px;
            border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .test-section-title {
            font-size: 28px; color: #1e3c72; margin-bottom: 20px;
            padding-bottom: 15px; border-bottom: 3px solid #3498db;
        }
        .test-description {
            background: #e3f2fd; padding: 20px; border-radius: 8px;
            margin-bottom: 30px; border-left: 4px solid #2196f3;
        }
        .test-description h3 { color: #1565c0; margin-bottom: 10px; }
        .test-description ul { margin-left: 20px; line-height: 1.8; }
        .test-description code {
            background: #fff; padding: 2px 6px; border-radius: 3px;
            font-family: 'Courier New', monospace; color: #c7254e;
        }
        .highlight-box {
            background: #fff3cd; border-left: 4px solid #ffc107;
            padding: 15px; margin: 20px 0; border-radius: 4px;
        }
        .highlight-box strong { color: #856404; }
        .divider {
            height: 2px; background: linear-gradient(to right, #3498db, #9b59b6);
            margin: 60px 0;
        }
        .footer { text-align: center; padding: 40px 20px; color: #666; font-size: 14px; }
    </style>
</head>
<body>
    <div class="test-header">
        <h1>🔗 FAQ 嵌入元件測試</h1>
        <p>從 https://faq.diy.ski/ 載入 FAQ 區塊 - 保留 SEO 優化與結構化資料</p>
    </div>

    <div class="container">
        <div class="test-section">
            <h2 class="test-section-title">📚 方案一：嵌入靜態 FAQ 頁面（推薦）</h2>

            <div class="test-description">
                <h3>✅ 功能特點：</h3>
                <ul>
                    <li><strong>從 faq.diy.ski 載入內容</strong> - 使用 Fetch API 動態抓取 FAQ 靜態頁面</li>
                    <li><strong>保留 Schema.org 結構化資料</strong> - 自動提取並注入 FAQPage schema，有利 Google Rich Snippets</li>
                    <li><strong>多語言支援</strong> - 支援中文（zh）、英文（en）、泰文（th）</li>
                    <li><strong>手風琴互動效果</strong> - 點擊展開/收合，使用 Material Icons</li>
                    <li><strong>連結到完整頁面</strong> - 每個 FAQ 可點擊查看完整說明</li>
                    <li><strong>與 FAQ 系統連動</strong> - 內容更新時自動同步</li>
                </ul>
            </div>

            <div class="highlight-box">
                <strong>⚠️ 重要：</strong> 此方案會從 <code>https://faq.diy.ski/faq/</code> 抓取靜態 HTML 頁面，
                並提取其中的 Schema.org 結構化資料注入到當前頁面，確保 SEO 效果。
            </div>

            <?php
            // 測試：嵌入 3 個通用 FAQ
            $faqIds = [
                'faq.general.009', // 幾歲可以開始學滑雪？
                'faq.general.010', // 第一次滑雪需要上課嗎？
                'faq.general.011', // 需要自備裝備嗎？
            ];
            renderFAQEmbed($faqIds, 'zh', 'widget');
            ?>
        </div>

        <div class="divider"></div>

        <div class="test-section">
            <h2 class="test-section-title">🎯 方案二：根據分類推薦 FAQ</h2>

            <div class="test-description">
                <h3>✅ 使用場景：</h3>
                <ul>
                    <li><strong>雪場頁面</strong> - 顯示該雪場的常見問題</li>
                    <li><strong>教練頁面</strong> - 顯示教學相關 FAQ</li>
                    <li><strong>課程頁面</strong> - 顯示課程費用、預約相關 FAQ</li>
                    <li><strong>文章頁面</strong> - 根據文章主題推薦相關 FAQ</li>
                </ul>
            </div>

            <div class="highlight-box">
                <strong>💡 智慧推薦：</strong> 系統會根據頁面類型自動選擇最相關的 FAQ，
                例如兒童滑雪頁面會優先顯示年齡限制、安全保障相關問題。
            </div>

            <?php
            // 測試：推薦兒童相關 FAQ
            renderRecommendedFAQs('kids', 3, 'zh');
            ?>
        </div>

        <div class="divider"></div>

        <div class="test-section">
            <h2 class="test-section-title">⚙️ PHP 使用方式</h2>

            <div class="test-description">
                <h3>基本用法：</h3>
                <pre style="background: #2d2d2d; color: #f8f8f2; padding: 20px; border-radius: 8px; overflow-x: auto;"><code>&lt;?php
require_once 'includes/faq_embed.php';

// 方法 1: 指定 FAQ ID 列表
$faqIds = [
    'faq.general.009',  // 幾歲可以開始學滑雪？
    'faq.general.010',  // 第一次滑雪需要上課嗎？
];
renderFAQEmbed($faqIds, 'zh', 'widget');

// 方法 2: 根據分類推薦（自動選擇相關 FAQ）
renderRecommendedFAQs('kids', 5, 'zh');  // 兒童滑雪相關
renderRecommendedFAQs('gear', 3, 'zh');  // 裝備相關
renderRecommendedFAQs('booking', 5, 'zh'); // 預約相關

// 方法 3: 雪場專屬 FAQ
renderParkFAQs('naeba', 'zh');  // 苗場雪場
renderParkFAQs('hakuba', 'zh'); // 白馬雪場
?&gt;</code></pre>
            </div>

            <div class="test-description">
                <h3>支援的分類：</h3>
                <ul>
                    <li><code>general</code> - 通用滑雪問題</li>
                    <li><code>kids</code> - 兒童滑雪與安全</li>
                    <li><code>gear</code> - 裝備準備</li>
                    <li><code>booking</code> - 預約與費用</li>
                    <li><code>instructor</code> - 教練資訊</li>
                </ul>
            </div>

            <div class="test-description">
                <h3>語言支援：</h3>
                <ul>
                    <li><code>'zh'</code> - 繁體中文（預設）</li>
                    <li><code>'en'</code> - English</li>
                    <li><code>'th'</code> - ภาษาไทย</li>
                </ul>
            </div>
        </div>

        <div class="divider"></div>

        <div class="test-section">
            <h2 class="test-section-title">🔍 SEO 優化說明</h2>

            <div class="test-description">
                <h3>Schema.org 結構化資料注入：</h3>
                <p>元件會自動從 faq.diy.ski 的靜態頁面提取 <code>application/ld+json</code> 資料，並注入到當前頁面的 <code>&lt;head&gt;</code> 中。</p>
                <br>
                <p><strong>注入的資料範例：</strong></p>
                <pre style="background: #2d2d2d; color: #f8f8f2; padding: 20px; border-radius: 8px; overflow-x: auto;"><code>{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {
      "@type": "Question",
      "name": "幾歲可以開始學滑雪？",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "建議從3歲以上開始，5歲以下的兒童建議安排一對一教學..."
      }
    },
    {
      "@type": "Question",
      "name": "第一次滑雪需要上課嗎？",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "強烈建議！專業教練可以幫助您學習正確姿勢..."
      }
    }
  ]
}</code></pre>
            </div>

            <div class="highlight-box">
                <strong>🎉 Google Rich Snippets 效果：</strong>
                <ul style="margin-top: 10px;">
                    <li>✅ 搜尋結果直接顯示 FAQ 摺疊區塊</li>
                    <li>✅ 提高點擊率（CTR）</li>
                    <li>✅ 增加搜尋曝光度</li>
                    <li>✅ 提升頁面權威性</li>
                </ul>
            </div>
        </div>

        <div class="divider"></div>

        <div class="test-section">
            <h2 class="test-section-title">🛠️ 技術實作細節</h2>

            <div class="test-description">
                <h3>運作流程：</h3>
                <ol style="line-height: 2;">
                    <li><strong>Fetch API 請求</strong> - 從 <code>https://faq.diy.ski/faq/{faqId}-{lang}.html</code> 抓取 HTML</li>
                    <li><strong>DOM 解析</strong> - 使用 <code>DOMParser</code> 解析 HTML 內容</li>
                    <li><strong>資料提取</strong> - 提取問題、答案、分類標籤、Schema.org 資料</li>
                    <li><strong>動態渲染</strong> - 在當前頁面生成 FAQ 區塊</li>
                    <li><strong>Schema 注入</strong> - 將結構化資料注入 <code>&lt;head&gt;</code></li>
                    <li><strong>事件綁定</strong> - 綁定點擊事件實現手風琴效果</li>
                </ol>
            </div>

            <div class="test-description">
                <h3>關鍵優勢：</h3>
                <ul>
                    <li>✅ <strong>內容同步</strong> - FAQ 更新時自動同步，無需手動維護</li>
                    <li>✅ <strong>SEO 友善</strong> - 保留完整的 Schema.org 結構化資料</li>
                    <li>✅ <strong>效能優化</strong> - 使用 Promise.all 並行載入多個 FAQ</li>
                    <li>✅ <strong>錯誤處理</strong> - 載入失敗時顯示友善錯誤訊息</li>
                    <li>✅ <strong>可擴展性</strong> - 支援未來新增更多 FAQ 分類</li>
                </ul>
            </div>
        </div>
    </div>

    <?php
    // 顯示 Booking CTA
    renderBookingCTA('general');
    ?>

    <div class="footer">
        <p>© 2025 SKIDIY - FAQ Embed Component Testing</p>
        <p style="margin-top: 10px; color: #999;">
            技術棧：PHP + Fetch API + DOMParser + Schema.org + Material Icons
        </p>
        <p style="margin-top: 5px; color: #999;">
            FAQ 來源：<a href="https://faq.diy.ski/" target="_blank" style="color: #3498db;">https://faq.diy.ski/</a>
        </p>
    </div>

    <script>
        // 檢查 Schema.org 資料是否成功注入
        setTimeout(() => {
            const schemas = document.querySelectorAll('script[type="application/ld+json"]');
            console.log('📊 Schema.org scripts found:', schemas.length);
            schemas.forEach((script, index) => {
                try {
                    const data = JSON.parse(script.textContent);
                    console.log(`Schema ${index + 1}:`, data);
                } catch (e) {
                    console.warn(`Failed to parse schema ${index + 1}`);
                }
            });
        }, 2000);
    </script>
</body>
</html>
