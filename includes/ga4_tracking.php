<?php
/**
 * Google Analytics 4 (GA4) Tracking Code
 *
 * Features:
 * - Cross-domain tracking (skidiyog.zeabur.app ↔ diy.ski)
 * - Custom events tracking
 * - Enhanced measurement
 *
 * Usage:
 * In <head> section: <?php require_once 'includes/ga4_tracking.php'; renderGA4Head(); ?>
 * Before </body>: <?php renderGA4Events(); ?>
 */

// GA4 Measurement ID (replace with actual ID)
$GA4_MEASUREMENT_ID = 'G-XXXXXXXXXX'; // TODO: Replace with actual GA4 ID

/**
 * Render GA4 tracking code in <head>
 */
function renderGA4Head() {
    global $GA4_MEASUREMENT_ID;

    // If no measurement ID set, don't render
    if ($GA4_MEASUREMENT_ID === 'G-XXXXXXXXXX') {
        echo "<!-- GA4 not configured. Set measurement ID in includes/ga4_tracking.php -->\n";
        return;
    }

    ?>
    <!-- Google Analytics 4 -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= $GA4_MEASUREMENT_ID ?>"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      // GA4 Configuration with cross-domain tracking
      gtag('config', '<?= $GA4_MEASUREMENT_ID ?>', {
        'linker': {
          'domains': ['skidiyog.zeabur.app', 'diy.ski', 'www.diy.ski'],
          'accept_incoming': true
        },
        'send_page_view': true,
        'cookie_flags': 'SameSite=None;Secure'
      });

      // Custom dimensions (optional)
      gtag('set', 'user_properties', {
        'site_version': 'zeabur'
      });
    </script>
    <!-- End Google Analytics 4 -->
    <?php
}

/**
 * Render GA4 custom events
 * Call this before </body>
 */
function renderGA4Events() {
    global $GA4_MEASUREMENT_ID;

    if ($GA4_MEASUREMENT_ID === 'G-XXXXXXXXXX') {
        return;
    }

    ?>
    <script>
    // GA4 Custom Events
    document.addEventListener('DOMContentLoaded', function() {

      // Track booking button clicks
      document.querySelectorAll('[id*="ordernow"], .booking-cta-button').forEach(function(button) {
        button.addEventListener('click', function() {
          var buttonText = this.textContent.trim();
          var currentPage = window.location.pathname;

          gtag('event', 'booking_intent', {
            'event_category': 'Booking',
            'event_label': buttonText,
            'page_location': currentPage
          });

          console.log('[GA4] Booking intent tracked:', buttonText);
        });
      });

      // Track FAQ interactions
      document.querySelectorAll('.faq-question').forEach(function(question) {
        question.addEventListener('click', function() {
          var faqText = this.textContent.trim();

          gtag('event', 'faq_interaction', {
            'event_category': 'Engagement',
            'event_label': faqText.substring(0, 100),
            'page_location': window.location.pathname
          });

          console.log('[GA4] FAQ interaction tracked:', faqText.substring(0, 50));
        });
      });

      // Track outbound links (to diy.ski)
      document.querySelectorAll('a[href*="diy.ski"]').forEach(function(link) {
        link.addEventListener('click', function(e) {
          var destination = this.href;

          gtag('event', 'outbound_link', {
            'event_category': 'Navigation',
            'event_label': destination,
            'transport_type': 'beacon'
          });

          console.log('[GA4] Outbound link tracked:', destination);
        });
      });

      // Track scroll depth
      var scrollTracked = {25: false, 50: false, 75: false, 100: false};
      window.addEventListener('scroll', function() {
        var scrollPercent = Math.round((window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100);

        [25, 50, 75, 100].forEach(function(threshold) {
          if (scrollPercent >= threshold && !scrollTracked[threshold]) {
            scrollTracked[threshold] = true;

            gtag('event', 'scroll_depth', {
              'event_category': 'Engagement',
              'event_label': threshold + '%',
              'value': threshold
            });

            console.log('[GA4] Scroll depth tracked:', threshold + '%');
          }
        });
      });

      // Track video plays (if any)
      document.querySelectorAll('video').forEach(function(video) {
        video.addEventListener('play', function() {
          gtag('event', 'video_start', {
            'event_category': 'Video',
            'event_label': video.src || 'inline_video'
          });
        });
      });

      console.log('[GA4] Custom events initialized');
    });
    </script>
    <?php
}

/**
 * Track custom conversion event
 * Call this on booking confirmation page
 */
function trackGA4Conversion($orderValue, $currency = 'JPY', $transactionId = null) {
    global $GA4_MEASUREMENT_ID;

    if ($GA4_MEASUREMENT_ID === 'G-XXXXXXXXXX') {
        return;
    }

    ?>
    <script>
    gtag('event', 'purchase', {
      'transaction_id': '<?= $transactionId ?>',
      'value': <?= $orderValue ?>,
      'currency': '<?= $currency ?>',
      'items': [{
        'item_name': 'Ski Lesson',
        'item_category': 'Education',
        'price': <?= $orderValue ?>,
        'quantity': 1
      }]
    });
    console.log('[GA4] Conversion tracked:', '<?= $transactionId ?>', <?= $orderValue ?>);
    </script>
    <?php
}
