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
            <div class="col s6 m4 l3">
              <div class="">
                <a href="<?=$r['name']?>/" class="waves-effect waves-light"><img src="/photos/<?=$r['name'];?>/1.jpg?<?=$ver?>" alt=""></a>
              </div>
              <a href="<?=$r['name']?>/" class="waves-effect waves-light">
              <p class="slogan"><?=($r['cname'])?$r['cname']:'n/a'; ?><?php  if( $r['jobType']=='support') echo ' <sub>(支援教練)</sub>';  ?> </p>
              <p class="location"><?=$expertise;?> </p>
              </a>
              
            </div>

<?php
    }else{
      //echo $r['name'];
    }
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