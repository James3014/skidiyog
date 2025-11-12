<?php
require('includes/sdk.php');
if(isset($_REQUEST['act']) && $_REQUEST['act']=='logout'){
  session_destroy();
  Header('Location: /');
}

$PARKS = new PARKS();
$parkList = $PARKS->listing();

?>
<!DOCTYPE html>
  <html>
    <head>
      <?php require('pageHeader.php'); ?>
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
                    window.location.replace('schedule.php?f=pl')
               });
        });
      });
    </script>



    <body class="index-bg">
      <?php
        renderNav(array(
          'is_park_context' => false
        ));
      ?>

      <a href="javascript:" id="return-to-top" class="waves-effect waves-light"><i class="material-icons">arrow_upward</i></a>
      <?php
        renderHero(array(
          'image' => '/assets/images/header_index_main_img.png',
          'pill' => 'Snow Resort Guide',
          'title' => 'Discover your SKI adventure',
          'subtitle' => '發掘適合各種旅程安排的滑雪場',
          'cta' => array(
            'label' => '現在就預訂',
            'target' => 'schedule.php?f=pl',
            'id' => 'ordernow',
            'name' => 'ordernow'
          )
        ));
      ?>

      <section class="site-section">
        <div class="card-grid">
          <?php foreach($parkList as $n => $r){ 
            $title = ($r['cname'])?$r['cname']:$r['name'];
            $subtitle = ($r['name']=='iski') ? 'SKI CLUB' : ucfirst($r['name']);
            $img = "https://diy.ski/photos/{$r['name']}/{$r['name']}.jpg?v190707";
          ?>
            <a class="grid-card" href="/park.php?name=<?=$r['name'];?>">
              <div class="grid-card__image">
                <img src="<?=$img?>" alt="<?=$title?>" onerror="this.onerror=null;this.src='https://diy.ski/assets/images/header_index_main_img.png';">
              </div>
              <div class="grid-card__body">
                <p class="grid-card__title"><?=$title?></p>
                <span class="grid-card__meta"><?=$subtitle?></span>
              </div>
            </a>
          <?php } ?>
        </div>
      </section>

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
      <!--custom js-->
      <script>
         $(document).ready(function(){
            $('.sidenav').sidenav();
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


    </body>
  </html>
