<?php
require('includes/sdk.php');

// Get park name from URL
$name = isset($_GET['name']) ? $_GET['name'] : '';

if(empty($name)){
  header('Location: /parkList.php');
  exit();
}

$parkData = ContentRepository::getParkData($name);
if(empty($parkData)){
  header('Location: /parkList.php');
  exit();
}
$park_info = isset($parkData['raw']) ? $parkData['raw'] : array();
if(isset($parkData['redirect_to'])){
  header('Location: ' . $parkData['redirect_to']);
  exit();
}
$hero_image = $parkData['hero_image'];
$display_name = $parkData['display_name'];
$SEO_TITLE = $parkData['seo']['title'];
$SEO_DESCRIPTION = $parkData['seo']['description'];
$SEO_OG_IMAGE = $parkData['seo']['image'];
$faq_keyword = $parkData['faq_keyword'];
$related_links = $parkData['related_links'];

$parkSchema = [
  '@context' => 'https://schema.org',
  '@type' => 'SkiResort',
  'name' => $display_name,
  'description' => $SEO_DESCRIPTION,
  'url' => 'https://' . domain_name . $_SERVER['REQUEST_URI'],
  'image' => [$hero_image],
  'touristType' => 'Skiers',
  'provider' => [
    '@type' => 'Organization',
    'name' => 'SKIDIY 自助滑雪'
  ]
];

// Generate Breadcrumb schema for SEO
$breadcrumbs = [
  ['name' => 'SKIDIY 自助滑雪', 'url' => 'https://' . domain_name . '/'],
  ['name' => '雪場介紹', 'url' => 'https://' . domain_name . '/parkList.php'],
  ['name' => $display_name, 'url' => 'https://' . domain_name . $_SERVER['REQUEST_URI']]
];
$breadcrumbSchema = ContentRepository::generateBreadcrumbSchema($breadcrumbs);

if (!empty($park_info['location'])) {
  $parkSchema['areaServed'] = strip_tags($park_info['location']);
}

if (!empty($park_info['address'])) {
  $parkSchema['address'] = [
    '@type' => 'PostalAddress',
    'streetAddress' => $park_info['address'],
    'addressCountry' => 'JP'
  ];
} elseif (!empty($park_info['location'])) {
  $parkSchema['address'] = [
    '@type' => 'PostalAddress',
    'addressCountry' => 'JP',
    'addressRegion' => strip_tags($park_info['location'])
  ];
}

if (!empty($park_info['time_section'])) {
  $parkSchema['openingHoursSpecification'] = [
    '@type' => 'OpeningHoursSpecification',
    'description' => strip_tags($park_info['time_section'])
  ];
}

if (!empty($park_info['ticket_section'])) {
  $parkSchema['priceRange'] = strip_tags($park_info['ticket_section']);
}

if (!empty($park_info['access_section'])) {
  $parkSchema['hasMap'] = strip_tags($park_info['access_section']);
}
?>
<!DOCTYPE html>
  <html>
    <head>
      <?php require('pageHeader.php'); ?>
      <?php require_once __DIR__ . '/includes/ga4_tracking.php'; renderGA4Head(); ?>
      <script type="application/ld+json">
        <?=json_encode($parkSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);?>
      </script>
      <script type="application/ld+json">
        <?=json_encode($breadcrumbSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);?>
      </script>
      <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover"/>
      <!--Import materialize.css-->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-rc.2/css/materialize.min.css">
      <!--Import custom.css-->
      <link rel="stylesheet" href="assets/css/custom.min.css">
      <!--Import jQuery-->
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    </head>

    <script type="text/javascript">
      $(document).ready(function(){
        $(function(){
               $('#ordernow').on('click', function(e){
                    window.location.replace('<?=$parkData['cta']['target']?>')
               });
        });
      });
    </script>

    <body data-help-variant="general" data-help-park="<?=$faq_keyword?>" data-help-park-slug="<?=$name?>">
      <?php
        renderNav(array(
          'display_name' => $display_name,
          'name' => $name,
          'is_park_context' => true,
          'sections' => $parkData['sections']
        ));
      ?>

      <div class="container-fuild">
        <a href="javascript:" id="return-to-top" class="waves-effect waves-light"><i class="material-icons">arrow_upward</i></a>
      </div>

      <?php
        renderHero(array(
          'modifier' => 'site-hero--park',
          'image' => $hero_image,
          'pill' => $parkData['hero_pill'],
          'title' => $display_name,
          'title_suffix' => ($name!='iski')?ucfirst($name):'滑雪俱樂部',
          'subtitle' => $parkData['description'],
          'cta' => array(
            'label' => $parkData['cta']['label'],
            'target' => $parkData['cta']['target'],
            'id' => 'ordernow',
            'name' => 'ordernow'
          )
        ));
      ?>

      <div class="container resort-info">
        <div class="row">
          <!-- Left navigation for desktop -->
          <?php renderLeftnav(array(
            'display_name' => $display_name,
            'slug' => $name,
            'sections' => $parkData['sections']
          )); ?>

          <!-- Main content -->
          <div class="col s12 l9 right resort-content">
            <?php
            renderSectionList($parkData['sections']);
            ?>
          </div>
        </div>
      </div>

      <section class="related-links" aria-labelledby="related-links-title">
        <div class="related-links__inner">
          <div class="related-links__header">
            <p class="hero-pill">需要更多資訊？</p>
            <h2 id="related-links-title"><?=$display_name?> FAQ 與預約</h2>
          </div>
          <div class="related-links__grid">
            <a class="related-links__card" href="<?=$related_links['faq_url']?>" target="_blank" rel="noopener">
              <span class="related-links__eyebrow">FAQ</span>
              <p class="related-links__title">查看 <?=$display_name?> 常見問題</p>
              <p class="related-links__body">包含交通方式、雪票購買、課程安排與裝備如何選，快速找到常見疑問的解答。</p>
              <span class="related-links__cta">開啟 FAQ</span>
            </a>
            <a class="related-links__card" href="<?=$related_links['booking_url']?>" target="_blank" rel="noopener">
              <span class="related-links__eyebrow">Booking</span>
              <p class="related-links__title">立即預約 <?=$display_name?> 課程</p>
              <p class="related-links__body">直接前往預約系統，依照日期、雪場與教練挑選最適合的課程或活動。</p>
              <span class="related-links__cta">前往預約</span>
            </a>
          </div>
        </div>
      </section>

      <?php $faqs = ContentRepository::getParkFAQs($name); ?>
      <div class="container">
        <?php renderFAQSection($faqs, $display_name . ' 常見問題'); ?>
      </div>

      <div class="back-button-wrap">
        <button class="btn btn-outline btn-outline-primary back-button" onclick="history.back();"><i class="material-icons">keyboard_arrow_left</i> 回前一頁</button>
      </div>

      <?php
      // Add Booking CTA
      require_once __DIR__ . '/includes/booking_cta.php';
      renderBookingCTA('park', ['park_name' => $name, 'park_cname' => $display_name]);
      ?>

      <footer class="footer-copyright">
          <div class="container footer-copyright">
              <p class="center-align">©2025 diy.ski</p>
        </div>
      </footer>

      <!--JavaScript at end of body for optimized loading-->
      <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-rc.2/js/materialize.min.js"></script>

      <!--materialize js Initialize-->
      <script>
        $('.sidenav').sidenav();
        $('.materialboxed').materialbox();
      </script>

      <!--window scroll -->
      <script>
        $(document).ready(function () {
          // Only enable fixed nav if leftnav exists (desktop only)
          if ($('.leftnav').length > 0) {
            var top = $('.leftnav').offset().top - parseFloat($('.leftnav').css('marginTop').replace(/auto/, 40));

            $(window).scroll(function () {
              var y = $(this).scrollTop();
              if (y >= top) {
                $('.leftnav').addClass('fixed');
                $('#return-to-top').fadeIn();
              } else {
                $('.leftnav').removeClass('fixed');
                $('#return-to-top').fadeOut();
              }
            });
          } else {
            // On mobile, just show/hide return to top button
            $(window).scroll(function () {
              if ($(this).scrollTop() > 300) {
                $('#return-to-top').fadeIn();
              } else {
                $('#return-to-top').fadeOut();
              }
            });
          }

          $('#return-to-top').click(function() {
            $('body,html').animate({
              scrollTop : 0
            }, 500);
          });
        });
      </script>

      <!--left nav & smooth scroll -->
      <script>
        $(function() {
          $('.leftnav a').click(function () {
              $('.leftnav a').removeClass('leftnav-active');
              $(this).addClass('leftnav-active');
           });

          $('.leftnav a, .sidenav a').click(function() {
            if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'')
          && location.hostname == this.hostname) {

                var target = $(this.hash);
                target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
                if (target.length) {
                  // Close mobile sidenav if open
                  var sidenavInstance = M.Sidenav.getInstance($('#mobile-nav'));
                  if (sidenavInstance && sidenavInstance.isOpen) {
                    sidenavInstance.close();
                  }

                  $('html,body').animate({
                    scrollTop: target.offset().top - 100 //offsets for fixed header
                  }, 500);
                  return false;
                }
              }
            });
            //Executed on page load with URL containing an anchor tag.
            if($(location.href.split("#")[1])) {
                var target = $('#'+location.href.split("#")[1]);
                if (target.length) {
                  $('html,body').animate({
                    scrollTop: target.offset().top - 100 //offset height of header here too.
                  }, 500);
                  return false;
                }
              }
          });
      </script>

      <?php renderGA4Events(); ?>
    </body>
  </html>
