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
      $article_title = isset($article_data['title']) ? trim($article_data['title']) : 'SKIDIY 滑雪專欄';
      $article_plain = '';
      if(!empty($article_data['article'])){
        $article_plain = strip_tags(convert_media_urls($article_data['article']));
        $article_plain = preg_replace('/\s+/', ' ', $article_plain);
      }
      if(function_exists('mb_substr')){
        $article_snippet = mb_substr($article_plain, 0, 150);
        if(mb_strlen($article_plain) > 150){
          $article_snippet .= '…';
        }
      }else{
        $article_snippet = substr($article_plain, 0, 150);
        if(strlen($article_plain) > 150){
          $article_snippet .= '…';
        }
      }
      $article_hero = '';
      if (!empty($article_data['hero_image'])) {
        $article_hero = $article_data['hero_image'];
      } elseif(!empty($article_id)) {
        $article_hero = "https://diy.ski/photos/articles/{$article_id}/{$article_id}.jpg?v221008";
      }
      if(empty($article_hero)){
        $article_hero = 'https://diy.ski/assets/images/header_index_main_img.png';
      }
      $SEO_TITLE = $article_title . ' - SKIDIY 滑雪攻略';
      $SEO_DESCRIPTION = !empty($article_snippet) ? $article_snippet : 'SKIDIY 滑雪專欄：雪場攻略、教練分享與裝備建議。';
      $SEO_OG_IMAGE = $article_hero;
      $SEO_OG_DESC = $SEO_DESCRIPTION;
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

      <a href="javascript:" id="return-to-top" class="waves-effect waves-light"><i class="material-icons">arrow_upward</i></a>
      <div class="site-hero site-hero--park" style="--hero-image:url('<?=$article_hero?>');">
        <div class="site-hero__overlay"></div>
        <div class="site-hero__content">
          <span class="hero-pill">SKIDIY 精選</span>
          <h1 class="hero-title"><?=$article_data['title']?></h1>
          <p class="hero-subtitle">最新滑雪攻略與真實經驗分享</p>
          <button class="btn waves-effect waves-light btn-primary space-top-2" type="button" id="ordernow" name="ordernow">現在就預訂 <i class="material-icons">arrow_forward</i></button>
        </div>
      </div>

      <div class="container resort-info">
        <div class="row">





            <div class="col s12 l19 right resort-content">                 

              
              <?php 

                //echo '<h1 id="intro">'.$article_data['title'].'</h1>';
                echo '<pre>'.convert_media_urls($article_data['article']).'</pre><hr>';

              ?>


            </div>
        </div>
      </div>

      <?php
      // Add FAQ section with proxy (connects to faq.diy.ski)
      if (file_exists(__DIR__ . '/includes/faq_proxy.php')) {
          require_once __DIR__ . '/includes/faq_proxy.php';

          // Recommended FAQs by category (default: general)
          $category = 'general';
          if (isset($article_data['category']) && !empty($article_data['category'])) {
              $category = $article_data['category'];
          }

          if (function_exists('renderRecommendedFAQsProxy')) {
              renderRecommendedFAQsProxy($category, 5, 'zh');
          }
      }
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
      <script>
        document.addEventListener('DOMContentLoaded', function(){
          var backTop = document.getElementById('return-to-top');
          if(!backTop){ return; }
          var toggle = function(){
            if(window.scrollY > 300){
              backTop.style.display = 'flex';
            }else{
              backTop.style.display = 'none';
            }
          };
          toggle();
          window.addEventListener('scroll', toggle);
          backTop.addEventListener('click', function(){
            window.scrollTo({top:0, behavior:'smooth'});
          });
        });
      </script>

      <?php renderGA4Events(); ?>
    </body>
  </html>
