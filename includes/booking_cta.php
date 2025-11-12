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
