<?php
require('includes/sdk.php');

// Get park name from URL
$name = isset($_GET['name']) ? $_GET['name'] : '';

if(empty($name)){
  header('Location: /parkList.php');
  exit();
}

// Redirect to main site for specific parks
if(in_array($name,['niseko'])){
  header('Location: https://diy.ski');exit();
}
if(in_array($name,['taipei'])){
  header('Location: https://diy.ski/iski');exit();
}

$PARKS = new PARKS();
$park_info = $PARKS->getParkInfo_by_Name($name);

if(empty($park_info)){
  header('Location: /parkList.php');
  exit();
}

// Special name mappings
if($name=='moiwa'){$park_info['cname']='二世谷';}
if($name=='gala'){$park_info['cname']='';}
if($name=='iski'){$park_info['cname']='iSKI';}

// Section definitions (mirrors diy.ski ordering)
$SECTION_HEADER = array(
  'about'  => '介紹',
  'photo' => '照片',
  'location'  => '位置',
  'slope'  => '雪道',
  'ticket' => '雪票',
  'time' => '開放時間',
  'access' => '交通',
  'live'  => '住宿',
  'rental' => '租借',
  'delivery'  => '宅配',
  'luggage' => '行前裝備',
  'workout'  => '體能',
  'remind'  => '上課地點及事項',
  'join'  => '約伴及討論',
  'event'  => '優惠活動',
  'all'  => '完整閱讀'
);

// Map to database column names
$field_mapping = array(
  'about' => 'about',
  'photo' => 'photo_section',
  'location' => 'location_section',
  'slope' => 'slope_section',
  'ticket' => 'ticket_section',
  'time' => 'time_section',
  'access' => 'access_section',
  'live' => 'live_section',
  'rental' => 'rental_section',
  'delivery' => 'delivery_section',
  'luggage' => 'luggage_section',
  'workout' => 'workout_section',
  'remind' => 'remind_section',
  'join' => 'join_section',
  'event' => 'event_section'
);

// Predefined media overrides so重要區塊有預設圖
$hero_overrides = array(
  'karuizawa' => 'https://diy.ski/photos/karuizawa/course1.jpg',
  'naeba' => 'https://diy.ski/photos/naeba/3.jpg?v3',
  'appi' => 'https://diy.ski/photos/appi/appi.jpg'
);

$gallery_overrides = array(
  'karuizawa' => array(
    array(
      'src' => 'https://diy.ski/photos/karuizawa/course1.jpg',
      'caption' => '山頂纜車沿線視角'
    ),
    array(
      'src' => 'https://diy.ski/photos/karuizawa/course02.jpg',
      'caption' => '親子友善的綠線雪道'
    ),
    array(
      'src' => 'https://diy.ski/photos/karuizawa/karuizawasite02b.jpg',
      'caption' => '雪道與購物中心的連結'
    ),
    array(
      'src' => 'https://diy.ski/photos/karuizawa/rental.jpg',
      'caption' => '王子飯店租借中心'
    )
  )
);

// Helper to render fallback gallery
function render_photo_gallery($photos, $resort_name){
  if(empty($photos)){return '';}
  ob_start(); ?>
  <div class="photo-grid">
    <?php foreach($photos as $idx => $photo):
      $src = $photo['src'];
      $caption = isset($photo['caption']) ? $photo['caption'] : '';
      $alt = isset($photo['alt']) ? $photo['alt'] : $resort_name . ' 照片 ' . ($idx + 1);
    ?>
      <figure class="photo-grid-item">
        <img src="<?=$src?>" alt="<?=htmlspecialchars($alt, ENT_QUOTES)?>">
        <?php if(!empty($caption)){ ?><figcaption><?=$caption?></figcaption><?php } ?>
      </figure>
    <?php endforeach; ?>
  </div>
  <?php
  return ob_get_clean();
}

if(empty($park_info['photo'])){
  if(isset($hero_overrides[$name])){
    $park_info['photo'] = $hero_overrides[$name];
  }else{
    $park_info['photo'] = 'https://diy.ski/photos/'.$name.'/3.jpg';
  }
}

$section_contents = array();
foreach($SECTION_HEADER as $key => $val){
  if($key == 'all'){continue;}
  $field = isset($field_mapping[$key]) ? $field_mapping[$key] : $key;
  $content = isset($park_info[$field]) ? $park_info[$field] : '';

  if($key === 'photo' && empty($content) && isset($gallery_overrides[$name])){
    $content = render_photo_gallery($gallery_overrides[$name], $park_info['cname']);
  }

  $section_contents[$key] = $content;
}

$hero_image = $park_info['photo'];
?>
<!DOCTYPE html>
  <html>
    <head>
      <?php require('pageHeader.php'); ?>
      <?php require_once __DIR__ . '/includes/ga4_tracking.php'; renderGA4Head(); ?>
      <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover"/>
      <!--Import materialize.css-->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-rc.2/css/materialize.min.css">
      <!--Import custom.css-->
      <link rel="stylesheet" href="assets/css/custom.min.css">
      <!--Import jQuery-->
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
      <style>
        body {
          background-color: #f8f9fb;
        }
        .navbar-fixed nav {
          background: linear-gradient(90deg,#f16f6f 0%,#f48b84 100%);
          box-shadow: 0 4px 18px rgba(241,111,111,0.25);
        }
        .nav-header {
          display: flex;
          align-items: center;
          justify-content: space-between;
          padding: 0 32px;
          min-height: 72px;
        }
        .nav-header .logo {
          height: 40px;
          width: auto;
        }
        .nav-header ul {
          display: flex;
          gap: 20px;
          margin: 0 48px 0 60px;
        }
        .nav-header ul li a {
          color: #fff;
          font-weight: 500;
          text-transform: none;
          letter-spacing: .3px;
        }
        .btn.btn-outline {
          border: 1px solid rgba(255,255,255,0.85);
          border-radius: 999px;
          padding: 0 24px;
          line-height: 36px;
          height: 38px;
          color: #fff;
          font-weight: 600;
          background: transparent;
        }
        .btn.btn-outline:hover {
          background: rgba(255,255,255,0.15);
        }
        .header-block-resort {
          position: relative;
          min-height: 420px;
          border-radius: 20px;
          overflow: hidden;
          margin: 40px auto 20px;
          background-size: cover;
          background-position: center;
          display: flex;
          align-items: center;
        }
        .header-overlay {
          position: absolute;
          inset: 0;
          background: linear-gradient(135deg,rgba(0,0,0,0.55),rgba(0,0,0,0.25));
        }
        .header-block-content {
          position: relative;
          z-index: 2;
          padding: 40px;
          color: #fff;
        }
        .resort-pill {
          display: inline-flex;
          align-items: center;
          gap: 6px;
          padding: 6px 16px;
          border-radius: 999px;
          border: 1px solid rgba(255,255,255,0.4);
          text-transform: uppercase;
          font-size: 12px;
          letter-spacing: 2px;
          margin-bottom: 16px;
        }
        .resort-name {
          font-size: 48px;
          font-weight: 700;
          line-height: 1.1;
          margin: 0;
        }
        .resort-name span {
          display: block;
        }
        .resort-name small {
          display: block;
          font-size: 18px;
          letter-spacing: 4px;
          text-transform: uppercase;
          opacity: .9;
          margin-top: 8px;
        }
        .resort-tagline {
          font-size: 20px;
          margin: 16px 0 30px;
          opacity: .95;
        }
        .btn-primary {
          background: #fff;
          color: #f16f6f;
          font-weight: 600;
          border-radius: 999px;
          padding: 14px 36px;
          box-shadow: 0 8px 24px rgba(0,0,0,0.2);
        }
        .btn-primary i {
          vertical-align: middle;
        }
        .leftnav {
          background-color: #fff;
          padding: 24px;
          border-radius: 18px;
          box-shadow: 0 18px 45px rgba(15,23,42,0.08);
          position: sticky;
          top: 120px;
        }
        .leftnav-brand {
          display: flex;
          align-items: center;
          gap: 10px;
          margin-bottom: 16px;
        }
        .leftnav-brand img {
          width: 32px;
        }
        .leftnav-brand span {
          font-weight: 700;
          letter-spacing: 1px;
          color: #ff665a;
        }
        .leftnav .resort-name {
          font-size: 22px;
          color: #0f172a;
          margin-bottom: 12px;
        }
        .leftnav .resort-name span {
          font-size: 14px;
          text-transform: uppercase;
          color: #94a3b8;
          letter-spacing: 3px;
        }
        .leftnav .tabs {
          display: flex;
          flex-direction: column;
          gap: 4px;
        }
        .leftnav .tab {
          display: block;
          padding: 12px 16px;
          border-radius: 10px;
          color: #475569;
          text-decoration: none;
          font-weight: 500;
        }
        .leftnav .tab li {
          list-style: none;
        }
        .leftnav .tab:hover,
        .leftnav .tab.leftnav-active {
          background: #eef2ff;
          color: #4338ca;
        }
        #return-to-top {
          position: fixed;
          bottom: 40px;
          right: 40px;
          background: #4338ca;
          width: 50px;
          height: 50px;
          display: none;
          border-radius: 50%;
          box-shadow: 0 12px 30px rgba(67,56,202,0.35);
        }
        #return-to-top i {
          color: #fff;
          line-height: 50px;
        }
        .resort-info {
          padding: 40px 0 80px;
        }
        .resort-content {
          background: #fff;
          border-radius: 24px;
          padding: 40px;
          box-shadow: 0 25px 60px rgba(15,23,42,0.08);
        }
        .resort-content h1 {
          font-size: 26px;
          font-weight: 700;
          color: #111827;
          margin: 40px 0 16px;
          position: relative;
          padding-left: 18px;
        }
        .resort-content h1:first-of-type {
          margin-top: 0;
        }
        .resort-content h1:before {
          content: '';
          position: absolute;
          left: 0;
          top: 0;
          width: 6px;
          height: 100%;
          background: linear-gradient(180deg,#ef4444,#f97316);
          border-radius: 999px;
        }
        .photo-grid {
          display: grid;
          grid-template-columns: repeat(auto-fit,minmax(220px,1fr));
          gap: 16px;
          margin: 20px 0 30px;
        }
        .photo-grid-item {
          border-radius: 16px;
          overflow: hidden;
          background: #f8fafc;
          box-shadow: inset 0 0 0 1px rgba(148,163,184,0.2);
        }
        .photo-grid-item img {
          width: 100%;
          height: 180px;
          object-fit: cover;
          display: block;
        }
        .photo-grid-item figcaption {
          padding: 12px;
          font-size: 14px;
          color: #475569;
        }
        @media (max-width: 992px){
          .nav-header {
            padding: 0 16px;
          }
          .leftnav {
            position: relative;
            top: auto;
            margin-bottom: 20px;
          }
          .resort-content {
            padding: 24px;
          }
          .header-block-resort {
            border-radius: 0;
          }
          .resort-name {
            font-size: 36px;
          }
        }
      </style>
    </head>

    <script type="text/javascript">
      $(document).ready(function(){
        $(function(){
               $('#ordernow').on('click', function(e){
                    window.location.replace('schedule.php?f=p&p=<?=$name?>')
               });
        });
      });
    </script>

    <body>
      <?php require('nav.inc.php');?>

      <div class="container-fuild">
        <a href="javascript:" id="return-to-top" class="waves-effect waves-light"><i class="material-icons">arrow_upward</i></a>
        <div class="row header-block-resort" style="background-image:url('<?=$hero_image?>');">
            <div class="header-overlay"></div>
            <div class="col s12 m10 push-m1 header-block-content" style="background-image:url('assets/images/header_img_bottom.png');background-size:contain;background-repeat:no-repeat;background-position:bottom right;width:100%;">
              <div class="resort-pill">Snow Resort Guide</div>
              <p class="resort-name">
                <span><?=$park_info['cname']?></span>
                <small><?=($name!='iski')?ucfirst($name):'滑雪俱樂部'?></small>
              </p>
              <p class="resort-tagline"><?=$park_info['description']?></p>
              <button class="btn waves-effect waves-light btn-primary space-top-2" type="button" id="ordernow" name="ordernow">現在就預訂 <i class="material-icons">arrow_forward</i></button>
            </div>
        </div>
      </div>

      <div class="container resort-info">
        <div class="row">
          <!-- Left navigation for desktop -->
          <div class="col l3 hide-on-med-and-down leftnav">
            <div class="leftnav-brand">
              <img src="/assets/images/logo-skidiy.png?v20251026" alt="SKIDIY">
              <span>SKIDIY</span>
            </div>
            <p class="resort-name"><?=$park_info['cname']?> <span><?=($name!='iski')?ucfirst($name):''?></span></p>
            <ul class="tabs tabs-transparent">
              <?php
              foreach($SECTION_HEADER as $key => $val){
                if($key == 'all') continue; // Skip "完整閱讀"
                if(!empty($section_contents[$key])){
                  echo '<a href="#' . $key . '" class="tab"><li>' . $val . '</li></a>';
                }
              }
              ?>
            </ul>
          </div>

          <!-- Main content -->
          <div class="col s12 l9 right resort-content">
            <?php
            $has_content = false;
            foreach($SECTION_HEADER as $key => $val){
              if($key == 'all') continue; // Skip "完整閱讀"
              if(!empty($section_contents[$key])){
                echo '<h1 id="' . $key . '">' . $val . '</h1>';

                // For naeba, karuizawa, appi: output HTML directly (TinyMCE format)
                // For others: use <pre> tag
                if(in_array($name, ['naeba', 'karuizawa', 'appi'])){
                  echo $section_contents[$key] . '<hr>';
                } else {
                  echo '<pre>' . $section_contents[$key] . '</pre><hr>';
                }

                $has_content = true;
              }
            }

            if(!$has_content){
              echo '<p>暫無雪場詳細資訊</p>';
            }
            ?>
          </div>
        </div>
      </div>

      <?php
      // Add FAQ section
      require_once __DIR__ . '/includes/faq_component.php';
      $faqs = getParkFAQs($name);
      ?>
      <div class="container">
        <?php renderFAQSection($faqs, $park_info['cname'] . ' 常見問題'); ?>
      </div>

      <div style="margin: 20px auto; text-align: center;">
        <button class="btn btn-outline btn-outline-primary" onclick="history.back();"><i class="material-icons">keyboard_arrow_left</i> 回前一頁</button>
      </div>

      <?php
      // Add Booking CTA
      require_once __DIR__ . '/includes/booking_cta.php';
      renderBookingCTA('park', ['park_name' => $name, 'park_cname' => $park_info['cname']]);
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
