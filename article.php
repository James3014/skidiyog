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

      $article_id = isset($_REQUEST['idx']) ? str_replace('\'','', $_REQUEST['idx']) : null;
      $articleData = ContentRepository::getArticleData($article_id);
      if(empty($articleData)){
        header('Location: /articleList.php');
        exit();
      }
      $article_raw = $articleData['raw'];
      $article_title = $articleData['title'];
      $article_content_html = $articleData['content'];
      $article_hero = $articleData['hero_image'];
      $SEO_TITLE = $articleData['seo']['title'];
      $SEO_DESCRIPTION = $articleData['seo']['description'];
      $SEO_OG_IMAGE = $articleData['seo']['image'];
      $SEO_OG_DESC = $SEO_DESCRIPTION;
      $articleSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => $article_title,
        'description' => $SEO_DESCRIPTION,
        'inLanguage' => 'zh-TW',
        'image' => [$article_hero],
        'mainEntityOfPage' => [
          '@type' => 'WebPage',
          '@id' => 'https://'.domain_name.$_SERVER['REQUEST_URI']
        ],
        'author' => [
          '@type' => 'Organization',
          'name' => 'SKIDIY 自助滑雪'
        ],
        'publisher' => [
          '@type' => 'Organization',
          'name' => 'SKIDIY 自助滑雪',
          'logo' => [
            '@type' => 'ImageObject',
            'url' => 'https://diy.ski/assets/images/logo-skidiy.png'
          ]
        ]
      ];
      if(!empty($article_raw['timestamp'])){
        $published = date(DATE_ATOM, strtotime($article_raw['timestamp']));
        $articleSchema['datePublished'] = $published;
        $articleSchema['dateModified'] = $published;
      }
      //var_dump($article_data);
 
?>

<!DOCTYPE html>
  <html>
    <head>
      <?php require('pageHeader.php'); ?>
      <?php require_once __DIR__ . '/includes/ga4_tracking.php'; renderGA4Head(); ?>
      <script type="application/ld+json">
        <?=json_encode($articleSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);?>
      </script>

      <!--swiper-->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.3.3/css/swiper.min.css">
      <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.3.3/js/swiper.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.3.3/js/swiper.esm.bundle.js"></script>
    </head>
    <script type="text/javascript">
      $(document).ready(function(){
        $(function(){          
               $('#ordernow').on('click', function(e){         
                    window.location.replace('<?=$articleData['cta']['target']?>') 
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
          <h1 class="hero-title"><?=$article_title?></h1>
          <p class="hero-subtitle">最新滑雪攻略與真實經驗分享</p>
          <button class="btn waves-effect waves-light btn-primary space-top-2" type="button" id="ordernow" name="ordernow"><?=$articleData['cta']['label']?> <i class="material-icons">arrow_forward</i></button>
        </div>
      </div>

      <div class="container resort-info">
        <div class="row">





            <div class="col s12 l19 right resort-content">                 

              
              <?php 

                echo '<div class="section-content section-content--pre"><pre>'.$article_content_html.'</pre></div><hr>';

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
          if (isset($article_raw['category']) && !empty($article_raw['category'])) {
              $category = $article_raw['category'];
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
