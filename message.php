<?php
require('includes/sdk.php');

$filters = array(
    'msg'         => FILTER_SANITIZE_STRING,
    'status'      => FILTER_SANITIZE_STRING,
    'serial'      => FILTER_SANITIZE_STRING,
);//_v($_POST);
$in = filter_var_array(array_merge($_REQUEST,$_POST), $filters);//_v($in);//exit();

switch ($in['msg']) {
  case 'paylinkExpire':
    $html = $SYSMSG[$in['msg']]."<br>訊息：#{$in['serial']},".strtoupper($in['status']);
    break;
}
?>
<!DOCTYPE html>
  <html>
    <head>
      <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover, user-scalable=false"/>
      
      <!--Import materialize.css-->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-rc.2/css/materialize.min.css">
      <!--Import custom.css-->
      <link rel="stylesheet" href="assets/css/custom.min.css">
      <!--Import jQuery-->
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    </head>

    <body>
      <header>
      <?php require('nav.inc.php');?>
      </header>

      <form action="pay.php" method="post" id="paymentForm">
      <input type="hidden" name="id" value="<?=urldecode(crypto::ev($oidx))?>">
      <main>
        <div class="container-fuild">
          <a href="javascript:" id="return-to-top" class="waves-effect waves-light"><i class="material-icons">arrow_upward</i></button></a>

          
          <div class="row header-block-booking">
            <div class="header-img-bottom">
              <img src="assets/images/header_img_bottom.png" alt="">
            </div>
            <img src="assets/images/header_class_main_img.jpg">    
          </div> 

        <div class="row header-block-float container-xl">
          <div class="col S12 m4 offset-m2">
            <img src="assets/images/class_booking_steps.png" class="steps-img steps-img-left">
          </div>
        </div>


        <!--class table-->
         <div class="row container-xl">
           <div class="col s12 m10 col-centered">
              <div class="card-panel"><?=$html?></div>
            </div>
         </div>   
              <table>
                <tr>
                  <td class="center">
                    <button class="btn btn-primary" id="btnHome">返回官網</button>
                  </td>
                </tr>
              </table>     
      </main>
      
      <footer>
        <div class="footer-copyright">
          <p class="center-align">© 2018 diy.ski</p>
        </div>
      </footer>

      
      

      
      <!--JavaScript at end of body for optimized loading-->
      <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-rc.2/js/materialize.min.js"></script>
      
      <!--custom js-->

      <script src="skidiy.data.php"></script>
      <script src="skidiy.func.php"></script>
      <script>
      function _d(d){console.log(d)}
      function _a(a){alert(a)}

      $(document).ready(function(){
        $('.sidenav').sidenav();
        $('#btnHome').on('click', function(e){
          e.preventDefault();
          document.location.href='https://diy.ski';
        });
      });
      </script>      


    </body>
  </html>