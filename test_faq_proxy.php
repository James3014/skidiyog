<?php
/**
 * FAQ Proxy 測試頁面
 *
 * 測試從 faq.diy.ski 透過伺服器代理載入 FAQ
 */

require_once 'includes/faq_proxy.php';
require_once 'includes/booking_cta.php';

?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ Proxy 測試 - 伺服器代理方案</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Microsoft JhengHei", sans-serif;
            line-height: 1.6; color: #333; background: #f5f5f5;
        }
        .test-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white; padding: 50px 20px; text-align: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        }
        .test-header h1 { font-size: 42px; margin-bottom: 15px; font-weight: 700; }
        .test-header p { font-size: 20px; opacity: 0.95; }
        .test-header .badge {
            display: inline-block; background: rgba(255,255,255,0.2);
            padding: 8px 20px; border-radius: 20px; margin-top: 15px;
            font-size: 16px;
        }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        .test-section {
            background: white; margin: 40px auto; padding: 40px;
            border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .test-section-title {
            font-size: 32px; color: #667eea; margin-bottom: 25px;
            padding-bottom: 15px; border-bottom: 4px solid #667eea;
            display: flex; align-items: center; gap: 15px;
        }
        .status-indicator {
            display: inline-flex; align-items: center; gap: 8px;
            background: #d4edda; color: #155724; padding: 8px 16px;
            border-radius: 20px; font-size: 14px; font-weight: 500;
        }
        .status-indicator.loading {
            background: #fff3cd; color: #856404;
        }
        .status-indicator.error {
            background: #f8d7da; color: #721c24;
        }
        .highlight-box {
            background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
            border-left: 4px solid #667eea; padding: 20px; margin: 25px 0;
            border-radius: 8px;
        }
        .highlight-box strong { color: #667eea; }
        .highlight-box ul { margin: 15px 0 0 20px; line-height: 2; }
        .info-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px; margin: 30px 0;
        }
        .info-card {
            background: #f8f9fa; padding: 20px; border-radius: 8px;
            border-left: 4px solid #667eea;
        }
        .info-card h4 {
            color: #667eea; margin-bottom: 10px; font-size: 18px;
        }
        .info-card p { color: #666; line-height: 1.8; }
        .code-block {
            background: #2d2d2d; color: #f8f8f2; padding: 20px;
            border-radius: 8px; overflow-x: auto; margin: 20px 0;
            font-family: 'Courier New', monospace; font-size: 14px;
        }
        .divider {
            height: 3px; background: linear-gradient(to right, #667eea, #764ba2);
            margin: 60px 0; border-radius: 2px;
        }
        .footer {
            text-align: center; padding: 50px 20px;
            background: #2d3748; color: white; margin-top: 60px;
        }
    </style>
</head>
<body>
    <div class="test-header">
        <h1>🚀 FAQ Proxy 伺服器代理測試</h1>
        <p>從 https://faq.diy.ski/ 透過 PHP cURL 載入 FAQ 區塊</p>
        <div class="badge">✅ 解決 CORS 問題 | 🔍 保留 SEO 優化 | 🔄 內容自動同步</div>
    </div>

    <div class="container">
        <div class="test-section">
            <h2 class="test-section-title">
                <i class="material-icons" style="font-size: 36px;">science</i>
                測試一：載入 3 個通用 FAQ
                <span class="status-indicator loading">
                    <i class="material-icons" style="font-size: 16px;">hourglass_empty</i>
                    載入中...
                </span>
            </h2>

            <div class="highlight-box">
                <strong>📋 測試項目：</strong>
                <ul>
                    <li><code>faq.general.009</code> - 幾歲可以開始學滑雪？</li>
                    <li><code>faq.general.010</code> - 第一次滑雪需要上課嗎？</li>
                    <li><code>faq.general.011</code> - 需要自備裝備嗎？</li>
                </ul>
            </div>

            <?php
            $startTime = microtime(true);

            $faqIds = [
                'faq.general.009',
                'faq.general.010',
                'faq.general.011',
            ];

            renderFAQProxy($faqIds, 'zh');

            $loadTime = round((microtime(true) - $startTime) * 1000, 2);
            ?>

            <div class="info-grid">
                <div class="info-card">
                    <h4>⏱️ 載入時間</h4>
                    <p style="font-size: 24px; font-weight: bold; color: #667eea;">
                        <?= $loadTime ?> ms
                    </p>
                </div>
                <div class="info-card">
                    <h4>🔢 FAQ 數量</h4>
                    <p style="font-size: 24px; font-weight: bold; color: #667eea;">
                        <?= count($faqIds) ?> 個
                    </p>
                </div>
                <div class="info-card">
                    <h4>🌐 語言</h4>
                    <p style="font-size: 24px; font-weight: bold; color: #667eea;">
                        繁體中文
                    </p>
                </div>
                <div class="info-card">
                    <h4>📊 Schema.org</h4>
                    <p style="font-size: 24px; font-weight: bold; color: #667eea;">
                        已注入 ✅
                    </p>
                </div>
            </div>
        </div>

        <div class="divider"></div>

        <div class="test-section">
            <h2 class="test-section-title">
                <i class="material-icons" style="font-size: 36px;">family_restroom</i>
                測試二：兒童滑雪相關 FAQ（智慧推薦）
            </h2>

            <div class="highlight-box">
                <strong>🎯 推薦邏輯：</strong> 系統自動選擇與「兒童滑雪」相關的 FAQ
            </div>

            <?php
            renderRecommendedFAQsProxy('kids', 3, 'zh');
            ?>
        </div>

        <div class="divider"></div>

        <div class="test-section">
            <h2 class="test-section-title">
                <i class="material-icons" style="font-size: 36px;">code</i>
                技術實作細節
            </h2>

            <div class="info-card" style="margin-bottom: 30px;">
                <h4>🔧 工作原理</h4>
                <ol style="margin-left: 20px; line-height: 2;">
                    <li><strong>PHP cURL 請求</strong> - 伺服器端請求 faq.diy.ski，繞過瀏覽器 CORS 限制</li>
                    <li><strong>DOM 解析</strong> - 使用 DOMDocument 和 DOMXPath 提取 HTML 內容</li>
                    <li><strong>資料提取</strong> - 提取問題、答案、分類標籤、Schema.org JSON-LD</li>
                    <li><strong>Schema 注入</strong> - 將 FAQPage 結構化資料注入當前頁面 &lt;head&gt;</li>
                    <li><strong>動態渲染</strong> - 在伺服器端生成 FAQ HTML 區塊</li>
                    <li><strong>快取機制</strong> - 使用 APCu 快取 FAQ 內容 1 小時（如果可用）</li>
                </ol>
            </div>

            <h3 style="color: #667eea; margin-bottom: 15px;">PHP 使用範例：</h3>
            <div class="code-block">&lt;?php
require_once 'includes/faq_proxy.php';

// 方法 1: 指定 FAQ ID 列表
$faqIds = ['faq.general.009', 'faq.general.010'];
renderFAQProxy($faqIds, 'zh');

// 方法 2: 根據分類推薦
renderRecommendedFAQsProxy('kids', 5, 'zh');
renderRecommendedFAQsProxy('gear', 3, 'zh');
renderRecommendedFAQsProxy('booking', 5, 'zh');

// 方法 3: 雪場專屬
renderParkFAQsProxy('naeba', 'zh');
?&gt;</div>

            <h3 style="color: #667eea; margin: 30px 0 15px 0;">支援的分類：</h3>
            <div class="info-grid">
                <div class="info-card">
                    <h4>general</h4>
                    <p>通用滑雪問題</p>
                </div>
                <div class="info-card">
                    <h4>kids</h4>
                    <p>兒童滑雪與安全</p>
                </div>
                <div class="info-card">
                    <h4>gear</h4>
                    <p>裝備準備</p>
                </div>
                <div class="info-card">
                    <h4>booking</h4>
                    <p>預約與費用</p>
                </div>
                <div class="info-card">
                    <h4>instructor</h4>
                    <p>教練資訊</p>
                </div>
            </div>
        </div>

        <div class="divider"></div>

        <div class="test-section">
            <h2 class="test-section-title">
                <i class="material-icons" style="font-size: 36px;">search</i>
                SEO 驗證
            </h2>

            <div class="highlight-box">
                <strong>✅ Schema.org FAQPage 已注入到 &lt;head&gt;</strong>
                <p style="margin-top: 10px;">打開瀏覽器開發者工具（F12），在 Console 執行：</p>
            </div>

            <div class="code-block">// 檢查 Schema.org 資料
document.querySelectorAll('script[type="application/ld+json"]').forEach(script => {
  console.log(JSON.parse(script.textContent));
});

// 預期輸出
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
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
}</div>

            <div class="info-card" style="margin-top: 30px;">
                <h4>🔍 Google Rich Results Test</h4>
                <p>
                    1. 訪問：<a href="https://search.google.com/test/rich-results" target="_blank" style="color: #667eea;">https://search.google.com/test/rich-results</a><br>
                    2. 貼上此頁面 URL<br>
                    3. 檢查是否偵測到 FAQPage 結構化資料<br>
                    4. 預覽 Google 搜尋結果的 FAQ 摺疊區塊
                </p>
            </div>
        </div>
    </div>

    <?php
    // 顯示 Booking CTA
    renderBookingCTA('general');
    ?>

    <div class="footer">
        <h3 style="margin-bottom: 20px;">© 2025 SKIDIY - FAQ Proxy Component</h3>
        <p style="opacity: 0.8; line-height: 1.8;">
            技術棧：PHP 8+ | cURL | DOMDocument | DOMXPath | Schema.org | APCu Cache<br>
            FAQ 來源：<a href="https://faq.diy.ski/" target="_blank" style="color: #93c5fd;">https://faq.diy.ski/</a><br>
            載入時間：<?= $loadTime ?> ms
        </p>
    </div>

    <script>
        // 更新載入狀態指示器
        document.addEventListener('DOMContentLoaded', function() {
            const statusIndicators = document.querySelectorAll('.status-indicator.loading');

            setTimeout(() => {
                statusIndicators.forEach(indicator => {
                    indicator.classList.remove('loading');
                    indicator.style.background = '#d4edda';
                    indicator.style.color = '#155724';
                    indicator.innerHTML = '<i class="material-icons" style="font-size: 16px;">check_circle</i>載入完成';
                });
            }, 500);

            // 檢查 Schema.org 資料
            setTimeout(() => {
                const schemas = document.querySelectorAll('script[type="application/ld+json"]');
                console.log('📊 Schema.org scripts found:', schemas.length);

                schemas.forEach((script, index) => {
                    try {
                        const data = JSON.parse(script.textContent);
                        console.log(`✅ Schema ${index + 1}:`, data);

                        if (data['@type'] === 'FAQPage') {
                            console.log(`   📋 FAQ count: ${data.mainEntity?.length || 0}`);
                        }
                    } catch (e) {
                        console.warn(`❌ Failed to parse schema ${index + 1}`);
                    }
                });
            }, 1000);
        });
    </script>
</body>
</html>
