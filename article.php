<?php
  // 301 Redirect to new SEO-friendly URLs (DISABLED until .htaccess rewrite rules are configured)
  // require_once __DIR__ . '/includes/article_mapping.php';
  //
  // if (isset($_GET['idx'])) {
  //     $idx = intval($_GET['idx']);
  //     if (articleExists($idx)) {
  //         $newUrl = getArticleNewUrl($idx);
  //         if ($newUrl) {
  //             header("HTTP/1.1 301 Moved Permanently");
  //             header("Location: " . $newUrl);
  //             exit();
  //         }
  //     }
  // }

  require('includes/sdk.php');
      // load from routing.php
      // $target = $name = $section  and section_content[]

      $SECTION_HEADER = array(
        'about'  => '自我介紹',
        'photo' => '教練照片',
        'certificate'  => '滑雪證照',
        'remind'  => '選課注意事項',
        'cloth' => '教練本季辨識服裝',    
      );

      $ARTICLE = new ARTICLE();
      //$article_id = mysql_real_escape_string($_REQUEST['idx']);
      $ID=str_replace('\'','', $_REQUEST['idx']); // Workaround for anti-sql-injection
      //$article_id = $_REQUEST['idx'];
      $article_id = $ID;
      $article_data = $ARTICLE->readByIdx($article_id);
      $SEO_OG_DESC = $article_data['title'];
      //var_dump($article_data);
 
?>

<!DOCTYPE html>
  <html>
    <head>
      <?php require('pageHeader.php'); ?>
      <?php require_once __DIR__ . '/includes/ga4_tracking.php'; renderGA4Head(); ?>

      <!--swiper-->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.3.3/css/swiper.min.css">
      <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.3.3/js/swiper.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.3.3/js/swiper.esm.bundle.js"></script>
    </head>
    <script type="text/javascript">
      $(document).ready(function(){
        $(function(){          
               $('#ordernow').on('click', function(e){         
                    window.location.replace('../schedule.php?f=a') 
               }); 
        });
      });  
    </script>
    <body>
      <?php require('nav.inc.php');?>

      <div class="container-fuild">
        <a href="javascript:" id="return-to-top" class="waves-effect waves-light"><i class="material-icons">arrow_upward</i></button></a>
        <div class="row header-block-resort">
            <div class="header-img-bottom"><img src="assets/images/header_img_bottom.png" alt=""></div>
            <img src="https://diy.ski/photos/naeba/3.jpg?v3">
            <div class="col s10 push-s1  m6 push-m3  header-block-content">              
              <p class="resort-name"><?=$article_data['title']?></p>                          
              <button class="btn waves-effect waves-light btn-primary space-top-2" type="submit" id="ordernow" name="ordernow">現在就預訂 <i class="material-icons">arrow_forward</i></button>
            </div> 
        </div>
      </div>

      <div class="container resort-info">
        <div class="row">





            <div class="col s12 l19 right resort-content">                 

              
              <?php 

                //echo '<h1 id="intro">'.$article_data['title'].'</h1>';
                echo '<pre>'.$article_data['article'].'</pre><hr>';

              ?>


            </div>
        </div>
      </div>

      <?php
      // Add FAQ section with proxy (connects to faq.diy.ski)
      require_once __DIR__ . '/includes/faq_proxy.php';

      // Auto-detect category from article title and content
      $title = isset($article_data['title']) ? $article_data['title'] : '';
      $content = isset($article_data['article']) ? $article_data['article'] : '';
      $combinedText = $title . ' ' . $content;

      $category = 'general'; // Default category

      // 關鍵字對應表（優先順序由上到下）
      $categoryKeywords = [
          'kids' => ['小朋友', '兒童', '親子', '家族', '小孩', '孩童', '幼兒', '寶寶', '幾歲'],
          'gear' => ['裝備', '雪鏡', '雪板', '雪杖', '護具', '租借', '選購', '挑選', '雪衣', '雪褲', '手套'],
          'instructor' => ['教練', '教學', '指導', '師資', '證照', '認證', '經驗'],
          'safety' => ['保險', '安全', '意外', '受傷', '醫療', '理賠', '保障'],
          'payment' => ['費用', '價格', '支付', '付款', '優惠', '折扣', '退費'],
          'booking' => ['預約', '預訂', '報名', '取消', '變更', '修改', '確認'],
          'itinerary' => ['行程', '住宿', '機票', '交通', '規劃', '安排'],
          'course' => ['課程', '教學', '分級', '進度', '內容'],
          'grouping' => ['團體', '私人', '一對一', '同班', '湊班'],
          'transport' => ['集合', '地點', '交通', '怎麼去', '接駁'],
      ];

      // 根據關鍵字匹配分類
      foreach ($categoryKeywords as $cat => $keywords) {
          foreach ($keywords as $keyword) {
              if (mb_strpos($combinedText, $keyword) !== false) {
                  $category = $cat;
                  break 2; // 找到第一個匹配就跳出
              }
          }
      }

      // 渲染 FAQ（顯示 5 個）
      renderRecommendedFAQsProxy($category, 5, 'zh');
      ?>

      <div style="margin: 0px auto;">
      <button class="btn btn-outline btn-outline-primary" onclick="history.back();"><i class="material-icons">keyboard_arrow_left</i> 回前一頁</button>
      </div>

      <?php
      // Add Booking CTA
      require_once __DIR__ . '/includes/booking_cta.php';
      renderBookingCTA('article');
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

      <!--Swiper -->
      <script>
        var swiper = new Swiper('.swiper-container', {
          slidesPerView: 2,
          spaceBetween: 10,
          // init: false,
          pagination: {
            el: '.swiper-pagination',
            clickable: true,
            type: 'bullets',
          },
          navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
          },
          scrollbar: {
            el: '.swiper-scrollbar',
          },
          breakpoints: {
            1024: {
              slidesPerView: 1,
              spaceBetween: 10,
            },
            768: {
              slidesPerView: 2,
              spaceBetween: 10,
            },
            640: {
              slidesPerView: 1,
              spaceBetween: 0,
            },
            320: {
              slidesPerView: 1,
              spaceBetween: 0,
            }
          }
        });
      </script>
    

      <!--window scroll -->
      <script>
        $(document).ready(function () {  
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

            $('#return-to-top').click(function() {
                $('body,html').animate({
                    scrollTop : 0
                }, 500);
            });

        });
      </script>

      <!--left nav & side nav -->
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