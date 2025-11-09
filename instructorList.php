<?php
require('includes/sdk.php'); 
if(isset($_REQUEST['act']) && $_REQUEST['act']=='logout'){
  session_destroy();
  Header('Location: https://'.domain_name);
}

$INSTRUCTORS = new INSTRUCTORS();
$instructorList = $INSTRUCTORS->instructorList(); //_v($instructorList);
$target = 'instructors';

?>
<!DOCTYPE html>
  <html>
    <head>
    <?php require('pageHeader.php'); ?>        
    </head>

    <script type="text/javascript">
      $(document).ready(function(){
        $(function(){          
               $('#ordernow').on('click', function(e){         
                    window.location.replace('schedule.php?f=il') 
               }); 
        });
      });  
    </script>



    <body class="index-bg">
      <?php require('nav.inc.php');?>

      <div class="site-hero" style="--hero-image:url('/assets/images/header_index_main_img.png');">
        <div class="site-hero__overlay"></div>
        <div class="site-hero__content">
          <span class="hero-pill">Instructor Team</span>
          <h1 class="hero-title">最專業的教練夥伴</h1>
          <p class="hero-subtitle">擁有國際證照的教練團隊，為你客製化滑雪課程</p>
          <button class="btn waves-effect waves-light btn-primary space-top-2" type="button" id="ordernow" name="ordernow">現在就預訂 <i class="material-icons">arrow_forward</i></button>
        </div>
      </div>

      <section class="site-section">
        <div class="card-grid">
<?php 
//_v($instructorList);
foreach($instructorList as $n => $r){
    if(in_array($r['name'],['phil','wawa','eden','tommy','sophia','tim','wakka','kimi','linlin','john','tong'])) continue;
    $r['cname'] = (stripos($r['cname'], '放肆客')!==false) ? 'Paul' : $r['cname'];
    $expertise = 'n/a';
    if($r['expertise']=='sb') $expertise = "Snowboard";
    if($r['expertise']=='ski') $expertise = "Ski";
    if($r['expertise']=='both') $expertise = "Snowboard & Ski";
    $expertise = ($r['name']==='firsttrack') ? 'Snowboard & Ski' : $expertise;
    if(file_exists('photos/'.$r['name'].'/1.jpg')){
        $ver = 'v180615a';
        //$ver = time();
?>
            <a class="grid-card" href="<?=$r['name']?>/">
              <div class="grid-card__image">
                <img src="/photos/<?=$r['name'];?>/1.jpg?<?=$ver?>" alt="<?=$r['cname']?>" onerror="this.src='/assets/images/index-bg.jpg'">
              </div>
              <div class="grid-card__body">
                <p class="grid-card__title"><?=($r['cname'])?$r['cname']:'n/a'; ?><?php  if( $r['jobType']=='support') echo ' <sub>(支援教練)</sub>';  ?></p>
                <span class="grid-card__meta"><?=$expertise;?></span>
              </div>
            </a>

<?php
    }else{
      //echo $r['name'];
    }
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
