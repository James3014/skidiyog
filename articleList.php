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

      <div class="container-fuild">
        <div class="row header-block-index">
            <div class="col s10 push-s1  m6 push-m3  header-block-content">
              <p class="text-center slogan-en">Discover your SKI adventure</p>
              <p class="slogan-ch">發掘適合各種旅程安排的滑雪場</p>
              <button class="btn waves-effect waves-light btn-primary space-top-2" type="submit" id="ordernow" name="ordernow">現在就預訂 <i class="material-icons">arrow_forward</i></button>
            </div>
            
        </div>
      </div>

      <div class="container resort-block">
        <div class="row">
<?php
foreach($articleList as $n => $r){
 if(in_array($r['idx'], [24,25])) continue;
?>
            <div class="col s6 m4 l3">
              <div class="location-img">
                <a href="/article.php?idx=<?=$r['idx'];?>" class="waves-effect waves-light"><img src="/photos/articles/<?=$r['idx'];?>/<?=$r['idx'];?>.jpg?v221008" alt=""> </a>
              </div>
              <a href="/article.php?idx=<?=$r['idx'];?>" class="waves-effect waves-light"><p class="slogan"><?=($r['title'])?$r['title']:'n/a'; ?></p></a>
              <!--<p class="location"> </p>-->
              
            </div>

<?php
}
?>

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
      <!--custom js-->
      <script>
         $(document).ready(function(){
            $('.sidenav').sidenav();
          });
      </script>

      
    </body>
  </html>