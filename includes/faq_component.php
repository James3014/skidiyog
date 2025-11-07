<?php
/**
 * FAQ Component
 *
 * Reusable FAQ section for park and article pages
 *
 * Usage:
 * require_once 'includes/faq_component.php';
 * renderFAQSection($faqs, $title);
 *
 * @param array $faqs Array of FAQ items [['q' => '問題', 'a' => '答案'], ...]
 * @param string $title Section title (default: "常見問題")
 */

function renderFAQSection($faqs, $title = "常見問題") {
    if (empty($faqs)) {
        return;
    }

    ?>
    <style>
        .faq-section {
            margin: 40px 0;
            padding: 30px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .faq-section h3 {
            color: #2c3e50;
            font-size: 28px;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 3px solid #3498db;
        }
        .faq-item {
            margin-bottom: 20px;
            background: white;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: box-shadow 0.3s ease;
        }
        .faq-item:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        .faq-question {
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
        .faq-question:hover {
            background: #ecf0f1;
        }
        .faq-question::before {
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
        .faq-icon {
            font-size: 24px;
            color: #3498db;
            transition: transform 0.3s ease;
            flex-shrink: 0;
        }
        .faq-item.active .faq-icon {
            transform: rotate(180deg);
        }
        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease, padding 0.3s ease;
            padding: 0 20px;
        }
        .faq-item.active .faq-answer {
            max-height: 1000px;
            padding: 0 20px 20px 65px;
        }
        .faq-answer p {
            line-height: 1.8;
            color: #555;
            margin: 10px 0;
        }
        .faq-answer ul {
            margin-left: 20px;
            line-height: 1.8;
        }
        .faq-answer a {
            color: #3498db;
            text-decoration: none;
        }
        .faq-answer a:hover {
            text-decoration: underline;
        }

        /* Schema.org markup (hidden but helps SEO) */
        .faq-schema {
            display: none;
        }
    </style>

    <div class="faq-section">
        <h3><?= htmlspecialchars($title) ?></h3>

        <?php foreach ($faqs as $index => $faq): ?>
        <div class="faq-item" data-faq-index="<?= $index ?>">
            <div class="faq-question">
                <span><?= htmlspecialchars($faq['q']) ?></span>
                <i class="material-icons faq-icon">expand_more</i>
            </div>
            <div class="faq-answer">
                <?= $faq['a'] ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Schema.org FAQPage structured data for SEO -->
    <script type="application/ld+json" class="faq-schema">
    {
        "@context": "https://schema.org",
        "@type": "FAQPage",
        "mainEntity": [
            <?php foreach ($faqs as $index => $faq): ?>
            {
                "@type": "Question",
                "name": "<?= addslashes(strip_tags($faq['q'])) ?>",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "<?= addslashes(strip_tags($faq['a'])) ?>"
                }
            }<?= ($index < count($faqs) - 1) ? ',' : '' ?>
            <?php endforeach; ?>
        ]
    }
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const faqItems = document.querySelectorAll('.faq-item');

        faqItems.forEach(item => {
            const question = item.querySelector('.faq-question');

            question.addEventListener('click', function() {
                // Toggle current item
                item.classList.toggle('active');

                // Optional: Close other items (accordion behavior)
                // faqItems.forEach(otherItem => {
                //     if (otherItem !== item) {
                //         otherItem.classList.remove('active');
                //     }
                // });
            });
        });
    });
    </script>
    <?php
}

/**
 * Get FAQ data for specific park
 *
 * @param string $parkName Park identifier (e.g., 'naeba', 'hakuba')
 * @return array FAQ items
 */
function getParkFAQs($parkName) {
    $faq_data = [
        'naeba' => [
            ['q' => '苗場雪場適合初學者嗎？', 'a' => '<p>非常適合！苗場有專門的初學者區域，坡度平緩，設施完善。我們的教練會根據您的程度安排適合的練習場地。</p>'],
            ['q' => '苗場的雪票價格多少？', 'a' => '<p>成人一日券約 6,200 日圓，多日券有優惠。建議提前在官網或透過 SKIDIY 購買，可享折扣價格。</p>'],
            ['q' => '從東京到苗場怎麼去最方便？', 'a' => '<p>搭乘新幹線到越後湯澤站，再轉乘接駁巴士約 40 分鐘。也可選擇直達巴士，從新宿出發約 3.5 小時。</p>'],
            ['q' => '苗場有哪些住宿選擇？', 'a' => '<p>苗場王子大飯店是最熱門的選擇，滑雪進出很方便。另外周邊也有溫泉旅館和民宿可選擇。</p>'],
        ],
        'hakuba' => [
            ['q' => '白馬有幾個雪場？', 'a' => '<p>白馬地區有 10 個雪場，可使用白馬谷纜車通券（Hakuba Valley Ticket）通玩所有雪場。</p>'],
            ['q' => '白馬適合什麼程度的滑雪者？', 'a' => '<p>從初學者到專家都適合。不同雪場有不同特色，初學者推薦岩岳、栂池，進階玩家推薦八方尾根。</p>'],
            ['q' => '白馬的雪季什麼時候？', 'a' => '<p>通常從 12 月中旬到 4 月初。1-2 月是雪量最豐富的時期，3 月則是春雪季節，天氣較暖和。</p>'],
        ],
        // Default FAQs for all parks
        'default' => [
            ['q' => '第一次滑雪需要上課嗎？', 'a' => '<p>強烈建議！專業教練可以幫助您：</p><ul><li>學習正確姿勢，避免運動傷害</li><li>快速掌握基本技巧</li><li>建立信心，享受滑雪樂趣</li></ul>'],
            ['q' => '需要自備裝備嗎？', 'a' => '<p>初學者建議租借即可。雪場都有完整的租借服務，包含雪板/雪鞋、雪杖、安全帽等。<br>建議自備：雪衣褲、手套、護目鏡、保暖衣物。</p>'],
            ['q' => '一對一教學和團體課的差別？', 'a' => '<p><strong>一對一教學：</strong></p><ul><li>✓ 100% 專注於您的需求</li><li>✓ 進度快速，客製化教學</li><li>✓ 適合想快速進步或有特定需求者</li></ul><p><strong>團體課（2-3人）：</strong></p><ul><li>✓ 價格較優惠</li><li>✓ 可與朋友一起學習</li><li>✓ 適合程度相近的學員</li></ul>'],
            ['q' => '如何預約滑雪課程？', 'a' => '<p>透過 SKIDIY 網站預約：</p><ol><li>選擇雪場和日期</li><li>選擇教練和課程時數</li><li>填寫學員資訊</li><li>完成付款即可</li></ol><p>建議提前 1-2 週預約，旺季（12月底-2月）建議更早。</p>'],
        ]
    ];

    // Return park-specific FAQs + default FAQs
    $park_faqs = isset($faq_data[$parkName]) ? $faq_data[$parkName] : [];
    return array_merge($park_faqs, $faq_data['default']);
}

/**
 * Get FAQ data for articles
 *
 * @param int $articleIdx Article index
 * @return array FAQ items
 */
function getArticleFAQs($articleIdx) {
    // General skiing FAQs for articles
    return [
        ['q' => '第一次滑雪該選擇 Ski 還是 Snowboard？', 'a' => '<p><strong>Ski（雙板）：</strong></p><ul><li>上手較快，第一天就能滑</li><li>適合喜歡速度感的人</li><li>行動較靈活</li></ul><p><strong>Snowboard（單板）：</strong></p><ul><li>學習曲線較陡，但掌握後很有成就感</li><li>適合喜歡帥氣動作的人</li><li>摔倒時較不易受傷（但會比較累）</li></ul>'],
        ['q' => '滑雪保險有必要嗎？', 'a' => '<p>非常建議！滑雪運動具有一定風險，保險可涵蓋：</p><ul><li>醫療費用（日本醫療費昂貴）</li><li>救援費用</li><li>第三方責任險</li><li>裝備損壞賠償</li></ul><p>SKIDIY 有與保險公司合作，提供優惠的滑雪保險方案。</p>'],
        ['q' => '一般需要上幾堂課才能獨立滑行？', 'a' => '<p><strong>Ski：</strong> 通常 1-2 天（6-12 小時）課程即可在綠線獨立滑行<br><strong>Snowboard：</strong> 通常需要 2-3 天（12-18 小時）課程</p><p>實際進度因個人體能、平衡感、練習時間而異。建議初學者至少安排 2 天課程。</p>'],
        ['q' => 'SKIDIY 教練的資格認證？', 'a' => '<p>我們的教練都具備以下資格之一：</p><ul><li>日本 SAJ 滑雪指導員</li><li>加拿大 CASI/CSIA 認證</li><li>紐西蘭 SBINZ/NZSIA 認證</li><li>其他國際認可的滑雪教練執照</li></ul><p>所有教練都經過 SKIDIY 嚴格面試和試教，確保教學品質。</p>'],
    ];
}
