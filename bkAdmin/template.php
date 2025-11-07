<?php
require('../includes/sdk.php');
require('../includes/auth.php'); // Admin authentication check



$filters = array(
    'date'        =>  FILTER_SANITIZE_FULL_SPECIAL_CHARS,
    'expertise'   =>  FILTER_SANITIZE_FULL_SPECIAL_CHARS,
    'park'        =>  FILTER_SANITIZE_FULL_SPECIAL_CHARS,
);//_v($_POST);

$in = filter_var_array(array_merge($_REQUEST,$_POST), $filters);//_v($in);//exit();
$in['date'] = empty($in['date']) ? date('Y-m-d') : $in['date'];
$in['expertise'] = empty($in['expertise']) ? 'sb' : $in['expertise'];
$in['instructor'] = empty($_POST['instructor']) ? [] : $_POST['instructor'];



?>
<!DOCTYPE html>
  <html>
    <head>
      <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover, user-scalable=false"/>
      
      <!--Import materialize.css-->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-rc.2/css/materialize.min.css">
      <!--Import custom.css-->
      <link rel="stylesheet" href="../assets/css/custom.min.css">
      <!--Import jQuery-->
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
      
      <!--swiper-->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.3.3/css/swiper.min.css">
      <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.3.3/js/swiper.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.3.3/js/swiper.esm.bundle.js"></script>

      
    </head>

    <body>
      <header>
        <?php require('menu.php');?>
      </header>


    <!--form-->

    <!--form-->      



      

      <footer>
        <div class="footer-copyright">
          <p class="center-align">© 2018 diy.ski</p>
        </div>
      </footer>

      <div id="success-msg" class="modal center">
        <div class="modal-content">
          <i class="material-icons">beenhere</i>
          <h4>修改成功</h4>
          <div class="row space-top-2">
            <div class="input-field col s12 m8 col-centered">
              <p class="space-2">您所修改的資料己儲存成功。</p> 
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button href="#!" class="modal-close waves-effect btn btn-primary align-center">知道了 <i class="material-icons">check</i></button>
        </div>
      </div>

      <div id="err_msg" class="modal center">
        <div class="modal-content">
          <i class="material-icons">sentiment_very_dissatisfied</i>
          <h4>Ooooops.....</h4>
          <div class="row space-top-2">
            <div class="input-field col s12 m8 col-centered">
              <p id="PERRMSG" class="space-2">....</p> 
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button href="#!" class="modal-close waves-effect btn btn-primary align-center">知道了 <i class="material-icons">check</i></button>
        </div>
      </div> 
      
      <!--JavaScript at end of body for optimized loading-->
      <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-rc.2/js/materialize.min.js"></script>
      
      <!--custom js-->
      <script src="../assets/js/custom.js"></script>
      <script>
      $('.class .material-icons').hide();
      $('.class').click(function () {
        $(this).find('.class-m').toggleClass('class-m-active').find('.coach-name').toggle();
        $(this).find('.class-d').toggleClass('class-d-active');
        $(this).find('.material-icons').toggle();
        return false;
      });
      function _d(d){console.log(d)}
      function _a(a){alert(a)}      
      </script>


      <script>
      $(document).ready(function(){
        
         //alert('x')
        $('select.park').on('change',function(){
            var park = $('#park').val();_d(park);
            $("#query_form").submit();  
        });
        $('select.instructor').on('change',function(){
            var instructor = $('#instructor').val();_d(instructor);    
            $("#query_form").submit();      
        });
   
        $('#modifybt').on('click', function(e){         
                e.preventDefault();
                $.ajax({
                    //url: "account_info.php?act=up_2fcheck",
                    url: "post-cgi.php?cmd=park_update",                    
                    type: "POST",
                    data: $('#instructorForm').serialize(),                   
                    success: function(resp){
                        //alert("Successfully submitted."+resp)
                        if(resp==101009){ // save ok                              
                             $('#success-msg').modal('open'); 
                        }else{
                            $('#PERRMSG').text('internal err: code='+resp);
                            $('#err_msg').modal('open');                                
                        }                         
                    }
                });
           });
           
           $('#logoutbt').on('click', function(e){         
              window.location.replace('index.php?act=logout') 
        });
        //alert('done');

               
      });
      </script>      

    </body>
  </html>



