<?php
require('includes/sdk.php'); 
if(isset($_REQUEST['act']) && $_REQUEST['act']=='logout'){
  session_destroy();
  Header('Location: /');
}

$ARTICLE = new ARTICLE();
$articleList = $ARTICLE->listing();


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
                    window.location.replace('schedule.php?f=al') 
               }); 
        });
      });  
    </script>



    <body class="index-bg">
      <?php require('nav.inc.php');?>

      <div class="site-hero" style="--hero-image:url('/assets/images/header_index_main_img.png');">
        <div class="site-hero__overlay"></div>
        <div class="site-hero__content">
          <span class="hero-pill">Insights</span>
          <h1 class="hero-title">雪場攻略與最新文章</h1>
          <p class="hero-subtitle">從裝備準備到雪場攻略，掌握所有滑雪靈感</p>
          <button class="btn waves-effect waves-light btn-primary space-top-2" type="button" id="ordernow" name="ordernow">現在就預訂 <i class="material-icons">arrow_forward</i></button>
        </div>
      </div>

      <section class="site-section">
        <div class="card-grid">
<?php
foreach($articleList as $n => $r){
 if(in_array($r['idx'], [24,25])) continue;
 $img = "https://diy.ski/photos/articles/{$r['idx']}/{$r['idx']}.jpg?v221008";
 $title = ($r['title'])?$r['title']:'n/a';
 $published = !empty($r['timestamp']) ? date('Y.m.d', strtotime($r['timestamp'])) : 'SKIDIY GUIDE';
?>
            <a class="grid-card" href="/article.php?idx=<?=$r['idx'];?>">
              <div class="grid-card__image">
                <img src="<?=$img?>" alt="<?=$title?>" onerror="this.src='/assets/images/index-bg.jpg'">
              </div>
              <div class="grid-card__body">
                <p class="grid-card__title"><?=$title?></p>
                <span class="grid-card__meta"><?=$published?></span>
              </div>
            </a>

<?php
}
?>

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

      
    </body>
  </html>
