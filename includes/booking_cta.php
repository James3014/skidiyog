<?php
/**
 * Booking CTA Component
 *
 * Reusable call-to-action section for booking
 *
 * Usage:
 * require_once 'includes/booking_cta.php';
 * renderBookingCTA($type, $params);
 *
 * @param string $type Type of CTA: 'park', 'instructor', 'article', 'general'
 * @param array $params Additional parameters (e.g., park name, instructor name)
 */

function renderBookingCTA($type = 'general', $params = []) {
    $title = "準備開始您的滑雪之旅了嗎？";
    $subtitle = "立即預約專業教練，讓您的滑雪體驗更上一層樓";
    $buttonText = "立即預約課程";
    $buttonLink = "/schedule.php";

    // Customize based on type
    switch ($type) {
        case 'park':
            $parkName = $params['park_name'] ?? '';
            $parkCName = $params['park_cname'] ?? '';
            if ($parkName) {
                $title = "想在{$parkCName}學滑雪嗎？";
                $subtitle = "我們有專業的中文教練團隊，帶您暢遊{$parkCName}";
                $buttonLink = "/schedule.php?f=p&p={$parkName}";
            }
            break;

        case 'instructor':
            $instructorName = $params['instructor_name'] ?? '';
            if ($instructorName) {
                $title = "想跟{$instructorName}教練學滑雪嗎？";
                $subtitle = "立即預約，體驗專業的一對一教學服務";
                $buttonLink = "/schedule.php?f=i&i={$instructorName}";
            }
            break;

        case 'article':
            $title = "看完文章，準備開始行動了嗎？";
            $subtitle = "SKIDIY 專業教練團隊，陪您從零開始，享受滑雪樂趣";
            $buttonLink = "/schedule.php?f=a";
            break;
    }

    ?>
    <style>
        .booking-cta {
            background: linear-gradient(135deg, #1f2c46 0%, #162037 100%);
            padding: 52px 20px;
            margin: 60px 0 0 0;
            text-align: center;
            color: #f5f6fb;
            border-radius: 28px;
            box-shadow: 0 30px 70px rgba(3,8,20,0.5);
        }
        .booking-cta-title {
            font-size: 30px;
            font-weight: 600;
            margin-bottom: 12px;
        }
        .booking-cta-subtitle {
            font-size: 17px;
            margin-bottom: 26px;
            opacity: 0.8;
            line-height: 1.6;
        }
        .booking-cta-button {
            display: inline-flex;
            padding: 14px 34px;
            background: white;
            color: #1e88e5;
            font-size: 18px;
            font-weight: 600;
            border-radius: 999px;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(0,0,0,0.25);
            align-items: center;
            gap: 8px;
        }
        .booking-cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(0,0,0,0.35);
            color: #1565c0;
        }
        .booking-cta-features {
            margin-top: 34px;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 26px;
        }
        .booking-cta-feature {
            flex: 0 1 200px;
            text-align: center;
            color: rgba(245,246,251,0.85);
        }
        .booking-cta-feature i {
            font-size: 42px;
            margin-bottom: 10px;
            opacity: 0.85;
        }
        .booking-cta-feature-title {
            font-size: 15px;
            font-weight: 500;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .booking-cta-feature-desc {
            font-size: 13px;
            opacity: 0.75;
        }

        @media (max-width: 768px) {
            .booking-cta {
                padding: 38px 18px;
            }
            .booking-cta-title {
                font-size: 22px;
            }
            .booking-cta-subtitle {
                font-size: 15px;
                margin-bottom: 22px;
            }
            .booking-cta-button {
                padding: 12px 26px;
                font-size: 16px;
            }
            .booking-cta-features {
                margin-top: 26px;
                gap: 16px;
            }
            .booking-cta-feature {
                flex: 0 1 calc(50% - 8px);
                max-width: 160px;
            }
            .booking-cta-feature i {
                font-size: 32px;
                margin-bottom: 8px;
            }
            .booking-cta-feature-title {
                font-size: 14px;
                margin-bottom: 3px;
            }
            .booking-cta-feature-desc {
                font-size: 12px;
            }
        }
    </style>

    <div class="booking-cta">
        <div class="container">
            <h2 class="booking-cta-title"><?= $title ?></h2>
            <p class="booking-cta-subtitle"><?= $subtitle ?></p>
            <a href="<?= $buttonLink ?>" class="booking-cta-button">
                <?= $buttonText ?> <i class="material-icons" style="vertical-align: middle; margin-left: 8px;">arrow_forward</i>
            </a>

            <div class="booking-cta-features">
                <div class="booking-cta-feature">
                    <i class="material-icons">verified_user</i>
                    <div class="booking-cta-feature-title">專業認證</div>
                    <div class="booking-cta-feature-desc">國際認證教練</div>
                </div>
                <div class="booking-cta-feature">
                    <i class="material-icons">language</i>
                    <div class="booking-cta-feature-title">中文教學</div>
                    <div class="booking-cta-feature-desc">溝通無障礙</div>
                </div>
                <div class="booking-cta-feature">
                    <i class="material-icons">schedule</i>
                    <div class="booking-cta-feature-title">彈性時間</div>
                    <div class="booking-cta-feature-desc">自由安排</div>
                </div>
                <div class="booking-cta-feature">
                    <i class="material-icons">thumb_up</i>
                    <div class="booking-cta-feature-title">高滿意度</div>
                    <div class="booking-cta-feature-desc">學員好評推薦</div>
                </div>
            </div>
        </div>
    </div>
    <?php
}
