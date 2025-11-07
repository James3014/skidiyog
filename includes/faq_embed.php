<?php
/**
 * FAQ Embed Component - 嵌入 https://faq.diy.ski/ 的 FAQ 區塊
 *
 * 從 FAQ 系統載入靜態頁面內容，保留 Schema.org 結構化資料以利 SEO
 *
 * Usage:
 * require_once 'includes/faq_embed.php';
 * renderFAQEmbed(['faq.general.009', 'faq.general.010'], 'zh');
 *
 * @param array $faqIds FAQ ID 陣列 (e.g., ['faq.general.009', 'faq.general.010'])
 * @param string $lang 語言代碼 (zh, en, th)
 * @param string $displayMode 顯示模式: 'iframe', 'fetch', 'widget'
 */

function renderFAQEmbed($faqIds, $lang = 'zh', $displayMode = 'widget') {
    if (empty($faqIds)) {
        return;
    }

    $faqBaseUrl = 'https://faq.diy.ski/faq';

    ?>
    <style>
        .faq-embed-container {
            margin: 40px 0;
            background: #f8f9fa;
            border-radius: 8px;
            padding: 30px;
        }
        .faq-embed-header {
            color: #2c3e50;
            font-size: 28px;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 3px solid #3498db;
        }
        .faq-embed-item {
            margin-bottom: 20px;
            background: white;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: box-shadow 0.3s ease;
        }
        .faq-embed-item:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        .faq-embed-question {
            padding: 20px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 500;
            font-size: 18px;
            color: #2c3e50;
            background: white;
            transition: background-color 0.3s ease;
        }
        .faq-embed-question:hover {
            background: #ecf0f1;
        }
        .faq-embed-question::before {
            content: "Q";
            display: inline-block;
            width: 30px;
            height: 30px;
            line-height: 30px;
            text-align: center;
            background: #3498db;
            color: white;
            border-radius: 50%;
            margin-right: 15px;
            font-weight: bold;
            flex-shrink: 0;
        }
        .faq-embed-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease, padding 0.3s ease;
            padding: 0 20px;
        }
        .faq-embed-item.active .faq-embed-answer {
            max-height: 2000px;
            padding: 0 20px 20px 65px;
        }
        .faq-embed-answer p {
            line-height: 1.8;
            color: #555;
            margin: 10px 0;
        }
        .faq-embed-badge {
            display: inline-block;
            background: #e3f2fd;
            color: #1565c0;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .faq-embed-link {
            display: inline-block;
            margin-top: 10px;
            color: #3498db;
            text-decoration: none;
            font-size: 14px;
        }
        .faq-embed-link:hover {
            text-decoration: underline;
        }
        .faq-embed-icon {
            font-size: 24px;
            color: #3498db;
            transition: transform 0.3s ease;
            flex-shrink: 0;
        }
        .faq-embed-item.active .faq-embed-icon {
            transform: rotate(180deg);
        }
        .faq-embed-loading {
            text-align: center;
            padding: 40px;
            color: #999;
        }
    </style>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <div class="faq-embed-container">
        <h3 class="faq-embed-header">常見問題</h3>
        <div id="faq-embed-content" class="faq-embed-loading">
            載入中...
        </div>
    </div>

    <script>
    (async function() {
        const faqIds = <?= json_encode($faqIds) ?>;
        const lang = <?= json_encode($lang) ?>;
        const faqBaseUrl = <?= json_encode($faqBaseUrl) ?>;
        const displayMode = <?= json_encode($displayMode) ?>;

        const container = document.getElementById('faq-embed-content');

        try {
            const faqs = await Promise.all(
                faqIds.map(id => fetchFAQData(id, lang))
            );

            renderFAQs(faqs);
            attachEventListeners();
        } catch (error) {
            console.error('Failed to load FAQs:', error);
            container.innerHTML = '<p style="color: #e74c3c;">載入 FAQ 失敗，請稍後再試。</p>';
        }

        async function fetchFAQData(faqId, lang) {
            const url = `${faqBaseUrl}/${faqId}-${lang}.html`;
            const response = await fetch(url);

            if (!response.ok) {
                throw new Error(`Failed to fetch ${faqId}`);
            }

            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            // 提取關鍵資訊
            const question = doc.querySelector('h1')?.textContent || '';
            const badge = doc.querySelector('.badge')?.textContent || '';
            const mainAnswer = doc.querySelector('.faq-content p')?.innerHTML || '';
            const faqUrl = `${faqBaseUrl}/${faqId}?lang=${lang}`;

            // 提取 Schema.org 結構化資料
            const ldJson = doc.querySelector('script[type="application/ld+json"]');
            let schemaData = null;
            if (ldJson) {
                try {
                    schemaData = JSON.parse(ldJson.textContent);
                } catch (e) {
                    console.warn('Failed to parse ld+json for', faqId);
                }
            }

            return {
                id: faqId,
                question,
                answer: mainAnswer,
                badge,
                url: faqUrl,
                schemaData
            };
        }

        function renderFAQs(faqs) {
            const html = faqs.map((faq, index) => `
                <div class="faq-embed-item" data-faq-index="${index}">
                    ${faq.badge ? `<div class="faq-embed-badge">${escapeHtml(faq.badge)}</div>` : ''}
                    <div class="faq-embed-question">
                        <span>${escapeHtml(faq.question)}</span>
                        <i class="material-icons faq-embed-icon">expand_more</i>
                    </div>
                    <div class="faq-embed-answer">
                        <div>${faq.answer}</div>
                        <a href="${faq.url}" class="faq-embed-link" target="_blank" rel="noopener">
                            查看完整說明 →
                        </a>
                    </div>
                </div>
            `).join('');

            container.innerHTML = html;

            // 插入 Schema.org 結構化資料
            injectSchemaData(faqs);
        }

        function attachEventListeners() {
            const faqItems = container.querySelectorAll('.faq-embed-item');
            faqItems.forEach(item => {
                const question = item.querySelector('.faq-embed-question');
                question.addEventListener('click', function() {
                    item.classList.toggle('active');
                });
            });
        }

        function injectSchemaData(faqs) {
            const validSchemas = faqs
                .map(faq => faq.schemaData)
                .filter(Boolean);

            if (validSchemas.length === 0) return;

            // 合併多個 FAQ 的 Schema.org 資料
            const combinedSchema = {
                "@context": "https://schema.org",
                "@type": "FAQPage",
                "mainEntity": validSchemas.flatMap(schema =>
                    schema.mainEntity || []
                )
            };

            const script = document.createElement('script');
            script.type = 'application/ld+json';
            script.textContent = JSON.stringify(combinedSchema);
            document.head.appendChild(script);

            console.log('✅ Injected Schema.org FAQPage data:', combinedSchema.mainEntity.length, 'questions');
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    })();
    </script>
    <?php
}

/**
 * 根據分類或標籤自動推薦相關 FAQ
 *
 * @param string $category FAQ 分類 (e.g., 'general', 'kids', 'gear')
 * @param int $limit 最多顯示幾個 FAQ
 * @param string $lang 語言代碼
 */
function renderRecommendedFAQs($category, $limit = 5, $lang = 'zh') {
    // FAQ ID 映射表
    $faqMapping = [
        'general' => [
            'faq.general.009', // 幾歲可以開始學滑雪？
            'faq.general.010', // 第一次滑雪需要上課嗎？
            'faq.general.011', // 需要自備裝備嗎？
            'faq.general.012', // 課程費用包含哪些？
            'faq.general.013', // 如何預約課程？
        ],
        'kids' => [
            'faq.general.009', // 幾歲可以開始學滑雪？
            'faq.grouping.007', // 小朋友可以一起上課嗎？
            'faq.grouping.008', // 不同程度能同堂上課嗎？
        ],
        'gear' => [
            'faq.general.011', // 需要自備裝備嗎？
        ],
        'booking' => [
            'faq.general.012', // 課程費用包含哪些？
            'faq.general.013', // 如何預約課程？
        ],
        'instructor' => [
            'faq.course.005', // 一對一教學和團體課的差別？
            'faq.course.006', // 教練的資格認證？
        ]
    ];

    $faqIds = isset($faqMapping[$category])
        ? array_slice($faqMapping[$category], 0, $limit)
        : [];

    if (empty($faqIds)) {
        return;
    }

    renderFAQEmbed($faqIds, $lang, 'widget');
}

/**
 * 為雪場頁面推薦專屬 FAQ
 *
 * @param string $parkName 雪場名稱 (e.g., 'naeba', 'hakuba')
 * @param string $lang 語言代碼
 */
function renderParkFAQs($parkName, $lang = 'zh') {
    // 雪場專屬 FAQ (未來可從 API 動態載入)
    $generalFaqs = [
        'faq.general.009', // 幾歲可以開始學滑雪？
        'faq.general.010', // 第一次滑雪需要上課嗎？
        'faq.general.011', // 需要自備裝備嗎？
    ];

    renderFAQEmbed($generalFaqs, $lang, 'widget');
}
?>
