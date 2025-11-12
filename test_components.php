<?php
/**
 * Component Testing Page
 *
 * Test page for FAQ Component and Booking CTA
 * URL: /test_components.php
 */

// Include the components
require_once 'includes/sdk.php';
require_once 'includes/booking_cta.php';

?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>元件測試 - FAQ & Booking CTA</title>

    <!-- Material Icons for UI elements -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Microsoft JhengHei", sans-serif;
            line-height: 1.6;
            color: #333;
            background: #f5f5f5;
        }

        .test-header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 40px 20px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .test-header h1 {
            font-size: 36px;
            margin-bottom: 10px;
        }

        .test-header p {
            font-size: 18px;
            opacity: 0.9;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .test-section {
            background: white;
            margin: 40px auto;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .test-section-title {
            font-size: 28px;
            color: #1e3c72;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #3498db;
        }

        .test-description {
            background: #e3f2fd;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            border-left: 4px solid #2196f3;
        }

        .test-description h3 {
            color: #1565c0;
            margin-bottom: 10px;
        }

        .test-description ul {
            margin-left: 20px;
            line-height: 1.8;
        }

        .test-description code {
            background: #fff;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            color: #c7254e;
        }

        .divider {
            height: 2px;
            background: linear-gradient(to right, #3498db, #9b59b6);
            margin: 60px 0;
        }

        .footer {
            text-align: center;
            padding: 40px 20px;
            color: #666;
            font-size: 14px;
        }

        /* Optimize for screenshots */
        @media print {
            .test-header, .footer {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="test-header">
        <h1>🧪 元件測試頁面</h1>
        <p>FAQ Component & Booking CTA - 完整功能展示</p>
    </div>

    <div class="container">
        <!-- FAQ Component Test Section -->
        <div class="test-section">
            <h2 class="test-section-title">📚 FAQ Component 測試</h2>

            <div class="test-description">
                <h3>功能特點：</h3>
                <ul>
                    <li>✅ <strong>可重複使用的元件</strong> - 透過 <code>renderFAQSection()</code> 函數快速整合</li>
                    <li>✅ <strong>手風琴折疊設計</strong> - 點擊問題展開/收合答案</li>
                    <li>✅ <strong>美觀的視覺效果</strong> - 漸變 hover 效果 + 圓形 Q 標記</li>
                    <li>✅ <strong>Schema.org 結構化資料</strong> - 有利於 Google Rich Snippets SEO</li>
                    <li>✅ <strong>雪場專屬 FAQ</strong> - 支援苗場、白馬等雪場客製化問題</li>
                    <li>✅ <strong>響應式設計</strong> - 手機/平板/桌面完美顯示</li>
                </ul>
            </div>

            <?php
            // Test 1: 苗場雪場專屬 FAQ
            $naeba_faqs = getParkFAQs('naeba');
            renderFAQSection($naeba_faqs, "苗場雪場常見問題");
            ?>
        </div>

        <div class="divider"></div>

        <!-- Booking CTA Test Section -->
        <div class="test-section">
            <h2 class="test-section-title">🎯 Booking CTA 測試</h2>

            <div class="test-description">
                <h3>功能特點：</h3>
                <ul>
                    <li>✅ <strong>情境感知 CTA</strong> - 根據頁面類型（雪場/教練/文章）動態調整文案</li>
                    <li>✅ <strong>漸層紫色背景</strong> - 吸引眼球的視覺設計</li>
                    <li>✅ <strong>Hover 動畫效果</strong> - 按鈕上浮 + 陰影加深</li>
                    <li>✅ <strong>深度連結</strong> - 自動帶入雪場/教練參數到預約頁面</li>
                    <li>✅ <strong>四大特色亮點</strong> - 使用 Material Icons 圖示（體積小）</li>
                    <li>✅ <strong>響應式布局</strong> - 移動裝置自動調整字體和排版</li>
                </ul>
            </div>
        </div>
    </div>

    <?php
    // Test 2: Park-specific CTA (苗場)
    renderBookingCTA('park', [
        'park_name' => 'naeba',
        'park_cname' => '苗場'
    ]);
    ?>

    <div class="divider" style="margin-top: 0;"></div>

    <div class="container">
        <div class="test-section">
            <h2 class="test-section-title">🧑‍🏫 教練專屬 CTA 測試</h2>
        </div>
    </div>

    <?php
    // Test 3: Instructor-specific CTA
    renderBookingCTA('instructor', [
        'instructor_name' => '陳小明'
    ]);
    ?>

    <div class="divider" style="margin-top: 0;"></div>

    <div class="container">
        <div class="test-section">
            <h2 class="test-section-title">📝 文章專屬 CTA 測試</h2>
        </div>
    </div>

    <?php
    // Test 4: Article CTA
    renderBookingCTA('article');
    ?>

    <div class="container">
        <div class="test-section">
            <h2 class="test-section-title">🎨 設計說明</h2>

            <div class="test-description">
                <h3>為什麼使用 Material Icons？</h3>
                <p><strong>解決 MCP 工具圖片過大的問題：</strong></p>
                <ul>
                    <li>✅ <strong>向量圖示</strong> - Material Icons 是字體圖示，檔案極小（約 40KB for all icons）</li>
                    <li>✅ <strong>CDN 載入</strong> - 使用 Google CDN，不增加專案體積</li>
                    <li>✅ <strong>無限縮放</strong> - 向量圖示在任何解析度都清晰</li>
                    <li>✅ <strong>易於客製化</strong> - CSS 可直接修改顏色、大小、動畫</li>
                    <li>✅ <strong>豐富的圖示庫</strong> - 超過 2000+ 圖示可選擇</li>
                </ul>
                <br>
                <p><strong>使用的圖示：</strong></p>
                <ul>
                    <li><code>verified_user</code> - 專業認證（盾牌打勾）</li>
                    <li><code>language</code> - 中文教學（地球圖示）</li>
                    <li><code>schedule</code> - 彈性時間（時鐘圖示）</li>
                    <li><code>thumb_up</code> - 高滿意度（讚圖示）</li>
                    <li><code>expand_more</code> - FAQ 展開箭頭</li>
                    <li><code>arrow_forward</code> - CTA 按鈕箭頭</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>© 2025 SKIDIY - Component Testing Page</p>
        <p style="margin-top: 10px; color: #999;">
            技術棧：PHP + Material Icons + CSS3 Animations + Schema.org Structured Data
        </p>
    </div>

    <script>
        // Add some interaction feedback
        console.log('✅ FAQ Component loaded');
        console.log('✅ Booking CTA loaded');
        console.log('📊 Total FAQ items:', document.querySelectorAll('.faq-item').length);
        console.log('🎯 Total CTA sections:', document.querySelectorAll('.booking-cta').length);

        // Add smooth scroll to CTA buttons
        document.querySelectorAll('.booking-cta-button').forEach(btn => {
            btn.addEventListener('click', function(e) {
                console.log('CTA clicked:', this.href);
            });
        });
    </script>
</body>
</html>
