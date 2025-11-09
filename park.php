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
        .park-content {
          padding: 20px;
          max-width: 1200px;
          margin: 0 auto;
        }
        .park-content img {
          max-width: 100%;
          height: auto;
        }
        .park-content h3 {
          margin-top: 30px;
          color: #2196F3;
        }

        /* Left navigation styles */
        .leftnav {
          background-color: #fff;
          padding: 20px;
          border-radius: 4px;
          box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .leftnav .resort-name {
          font-size: 1.5rem;
          font-weight: 500;
          margin-bottom: 20px;
          color: #333;
        }

        .leftnav .tabs {
          display: flex;
          flex-direction: column;
        }

        .leftnav .tab {
          display: block;
          padding: 12px 15px;
          color: #666;
          text-decoration: none;
          border-left: 3px solid transparent;
          transition: all 0.3s;
        }

        .leftnav .tab:hover,
        .leftnav .tab.leftnav-active {
          background-color: #f5f5f5;
          border-left-color: #2196F3;
          color: #2196F3;
        }

        .leftnav .tab li {
          list-style: none;
          margin: 0;
        }

        .leftnav.fixed {
          position: fixed;
          top: 100px;
          width: calc(25% - 40px);
        }

        /* Return to top button */
        #return-to-top {
          position: fixed;
          bottom: 40px;
          right: 40px;
          background: #2196F3;
          width: 50px;
          height: 50px;
          display: none;
          text-decoration: none;
          border-radius: 50%;
          z-index: 1000;
          box-shadow: 0 2px 5px rgba(0,0,0,0.3);
        }

        #return-to-top i {
          color: white;
          margin: 0;
          position: relative;
          top: 13px;
        }

        #return-to-top:hover {
          background: #1976D2;
        }

        /* Resort info container */
        .resort-info {
          padding: 40px 0;
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
      <?php
      // Define section display order and titles (matching original diy.ski)
      // MUST be defined BEFORE nav.inc.php is included
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
      ?>

      <?php require('nav.inc.php');?>

      <div class="container-fuild">
        <a href="javascript:" id="return-to-top" class="waves-effect waves-light"><i class="material-icons">arrow_upward</i></a>
        <div class="row header-block-resort">
            <div class="header-img-bottom"><img src="assets/images/header_img_bottom.png" alt=""></div>
            <img src="https://diy.ski/photos/naeba/3.jpg?v3">
            <div class="col s10 push-s1  m6 push-m3  header-block-content">
              <p class="resort-name"><?=$park_info['cname']?>  <small><?=($name!='iski')?ucfirst($name):'滑雪俱樂部'?></small></p>
              <p><?=$park_info['description']?></p>
              <button class="btn waves-effect waves-light btn-primary space-top-2" type="submit" id="ordernow" name="ordernow">現在就預訂 <i class="material-icons">arrow_forward</i></button>
            </div>
        </div>
      </div>

      <div class="container resort-info">
        <div class="row">
          <!-- Left navigation for desktop -->
          <div class="col l3 hide-on-med-and-down leftnav">
            <p class="resort-name"><?=$park_info['cname']?> <span><?=($name!='iski')?ucfirst($name):''?></span></p>
            <ul class="tabs tabs-transparent">
              <?php
              foreach($SECTION_HEADER as $key => $val){
                if($key == 'all') continue; // Skip "完整閱讀"
                $field = isset($field_mapping[$key]) ? $field_mapping[$key] : $key;
                if(!empty($park_info[$field])){
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

              $field = isset($field_mapping[$key]) ? $field_mapping[$key] : $key;

              if(!empty($park_info[$field])){
                echo '<h1 id="' . $key . '">' . $val . '</h1>';

                // For naeba, karuizawa, appi: output HTML directly (TinyMCE format)
                // For others: use <pre> tag
                if(in_array($name, ['naeba', 'karuizawa', 'appi'])){
                  echo $park_info[$field] . '<hr>';
                } else {
                  echo '<pre>' . $park_info[$field] . '</pre><hr>';
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
