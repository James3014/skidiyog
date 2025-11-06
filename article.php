<?php
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
      <div style="margin: 0px auto;">
      <button class="btn btn-outline btn-outline-primary" onclick="history.back();"><i class="material-icons">keyboard_arrow_left</i> 回前一頁</button>
      </div>
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
     
    </body>
  </html>