<?php
require('includes/sdk.php');

// Get park name from URL
$name = isset($_GET['name']) ? $_GET['name'] : '';

if(empty($name)){
  header('Location: /parkList.php');
  exit();
}

// Redirect to main site for specific parks
if(in_array($name,['niseko'])){
  header('Location: https://diy.ski');exit();
}
if(in_array($name,['taipei'])){
  header('Location: https://diy.ski/iski');exit();
}

$PARKS = new PARKS();
$park_info = $PARKS->getParkInfo_by_Name($name);

if(empty($park_info)){
  header('Location: /parkList.php');
  exit();
}

// Special name mappings
if($name=='moiwa'){$park_info['cname']='二世谷';}
if($name=='gala'){$park_info['cname']='';}
if($name=='iski'){$park_info['cname']='iSKI';}

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
      <style>
        .park-content {
          padding: 20px;
          max-width: 1200px;
          margin: 0 auto;
        }
        .park-content img {
          max-width: 100%;
          height: auto;
        }
        .park-content h3 {
          margin-top: 30px;
          color: #2196F3;
        }
      </style>
    </head>

    <script type="text/javascript">
      $(document).ready(function(){
        $(function(){
               $('#ordernow').on('click', function(e){
                    window.location.replace('schedule.php?f=p&p=<?=$name?>')
               });
        });
      });
    </script>

    <body class="index-bg">
      <?php require('nav.inc.php');?>

      <div class="container-fuild">
        <div class="row header-block-index">
            <div class="col s10 push-s1  m6 push-m3  header-block-content">
              <p class="text-center slogan-en"><?=$park_info['cname']?></p>
              <p class="slogan-ch"><?=($name!='iski')?ucfirst($name):'滑雪俱樂部'?></p>
              <button class="btn waves-effect waves-light btn-primary space-top-2" type="submit" id="ordernow" name="ordernow">現在就預訂 <i class="material-icons">arrow_forward</i></button>
            </div>
        </div>
      </div>

      <div class="container park-content">
        <div class="row">
          <div class="col s12">
            <?php
            // Display park content from location field
            if(!empty($park_info['location'])){
              echo $park_info['location'];
            } else {
              echo '<p>暫無雪場詳細資訊</p>';
            }
            ?>
          </div>
        </div>
      </div>

      <div style="margin: 20px auto; text-align: center;">
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
