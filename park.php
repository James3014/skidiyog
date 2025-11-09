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
      </div>

      <div class="site-hero site-hero--park" style="--hero-image:url('<?=$hero_image?>');">
        <div class="site-hero__overlay"></div>
        <div class="site-hero__content">
          <span class="hero-pill">Snow Resort Guide</span>
          <h1 class="hero-title"><?=$park_info['cname']?><span><?=($name!='iski')?ucfirst($name):'滑雪俱樂部'?></span></h1>
          <p class="hero-subtitle"><?=$park_info['description']?></p>
          <button class="btn waves-effect waves-light btn-primary space-top-2" type="button" id="ordernow" name="ordernow">現在就預訂 <i class="material-icons">arrow_forward</i></button>
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
