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

      // Generate Breadcrumb schema for article pages
      $breadcrumbs = [
        ['name' => 'SKIDIY 自助滑雪', 'url' => 'https://'.domain_name.'/'],
        ['name' => '文章', 'url' => 'https://'.domain_name.'/articleList.php'],
        ['name' => $article_title, 'url' => 'https://'.domain_name.$_SERVER['REQUEST_URI']]
      ];
      $breadcrumbSchema = ContentRepository::generateBreadcrumbSchema($breadcrumbs);
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
      <script type="application/ld+json">
        <?=json_encode($breadcrumbSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);?>
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
      <?php
        renderNav(array(
          'display_name' => $article_title,
          'is_park_context' => false
        ));
      ?>

      <a href="javascript:" id="return-to-top" class="waves-effect waves-light"><i class="material-icons">arrow_upward</i></a>
      <?php
        renderHero(array(
          'modifier' => 'site-hero--park',
          'image' => $article_hero,
          'pill' => 'SKIDIY 精選',
          'title' => $article_title,
          'subtitle' => '最新滑雪攻略與真實經驗分享',
          'cta' => array(
            'label' => $articleData['cta']['label'],
            'target' => $articleData['cta']['target'],
            'id' => 'ordernow',
            'name' => 'ordernow'
          )
        ));
      ?>

      <div class="container resort-info">
        <div class="row">





            <div class="col s12 l19 right resort-content">                 

              <?php renderSectionList(array(array(
                'key' => 'article-body',
                'title' => $article_title,
                'content' => $article_raw['article'],
                'render_mode' => 'pre'
              ))); ?>
            </div>
        </div>
      </div>

      <?php
      // 【新增】顯示根據文章 tags 自動抓取的相關 FAQ
      // DEBUG
      echo '<!-- DEBUG: articleData["raw"]["tags"] = ' . htmlspecialchars($articleData['raw']['tags'] ?? 'EMPTY') . ' -->';
      echo '<!-- DEBUG: count(related_faqs) = ' . count($articleData['related_faqs'] ?? array()) . ' -->';

      if (!empty($articleData['related_faqs']) && is_array($articleData['related_faqs'])) {
          echo '<div class="container related-faqs-section" style="margin-top: 40px; margin-bottom: 40px;">';
          echo '<h2 style="font-size: 1.5rem; margin-bottom: 20px; color: #333;">相關常見問題</h2>';
          echo '<div class="row">';

          foreach ($articleData['related_faqs'] as $faq) {
              echo '<div class="col s12 m6 l4" style="margin-bottom: 20px;">';
              echo '<div class="card" style="border-left: 4px solid #0066cc; margin: 0;">';
              echo '<div class="card-content">';
              echo '<span class="card-title" style="font-size: 1rem; color: #0066cc;">' . htmlspecialchars($faq['question']) . '</span>';
              echo '<p style="font-size: 0.9rem; color: #666; line-height: 1.5; margin: 10px 0;">'
                   . htmlspecialchars(substr($faq['answer_preview'], 0, 100)) . '...</p>';
              echo '</div>';
              echo '<div class="card-action" style="padding: 10px 16px;">';
              echo '<a href="https://faq.diy.ski/?q=' . urlencode($faq['question']) . '" target="_blank" style="color: #0066cc; text-decoration: none; font-weight: bold;">查看完整答案 →</a>';
              echo '</div>';
              echo '</div>';
              echo '</div>';
          }

          echo '</div>';
          echo '</div>';
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
