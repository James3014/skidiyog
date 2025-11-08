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
      <!--Import workaround.css for mobile fixes-->
      <link rel="stylesheet" href="assets/css/workaround.css">
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

    <body class="index-bg">
      <?php require('nav.inc.php');?>

      <div class="container-fuild">
        <div class="row header-block-index">
            <div class="col s10 push-s1  m6 push-m3  header-block-content">
              <p class="text-center slogan-en"><?=$park_info['cname']?></p>
              <p class="slogan-ch"><?=($name!='iski')?ucfirst($name):'滑雪俱樂部'?></p>
              <button class="btn waves-effect waves-light btn-primary space-top-2" type="submit" id="ordernow" name="ordernow">現在就預訂 <i class="material-icons">arrow_forward</i></button>
            </div>
        </div>
      </div>

      <div class="container park-content">
        <div class="row">
          <div class="col s12">
            <?php
            // Define section display order and titles
            $sections = array(
              'about' => '介紹',
              'location_section' => '位置',
              'access_section' => '交通',
              'slope_section' => '雪道',
              'ticket_section' => '雪票',
              'time_section' => '開放時間',
              'live_section' => '住宿',
              'rental_section' => '租借',
              'delivery_section' => '宅配',
              'luggage_section' => '行前裝備',
              'workout_section' => '體能',
              'remind_section' => '上課地點及事項',
              'join_section' => '約伴及討論',
              'event_section' => '優惠活動'
            );

            $has_content = false;
            foreach($sections as $field => $title){
              if(!empty($park_info[$field])){
                echo '<h3>' . $title . '</h3>';
                echo '<div class="section-content">' . $park_info[$field] . '</div>';
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
      <!--custom js-->
      <script>
         $(document).ready(function(){
            $('.sidenav').sidenav();
          });
      </script>

      <?php renderGA4Events(); ?>
    </body>
  </html>
