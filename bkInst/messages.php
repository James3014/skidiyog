<?php
require('../includes/auth.php');
require('../includes/sdk.php');
$filters = array(
    'msg'       => FILTER_SANITIZE_STRING,
    'idx'       => FILTER_SANITIZE_STRING,
);//_v($_POST);
$in = filter_var_array(array_merge($_REQUEST,$_POST), $filters);//_v($in);//exit();

$loggedInstructor = $_SESSION['SKIDIY']['instructor'];
$ko = new ko();
$instructor = $ko->getInstructorInfo(['type'=>'instructor', 'name'=>$loggedInstructor]);//_v($instructor);

switch($in['msg']){
    case 'resvLinkError':
        $html = sprintf($SYSMSG[$in['msg']]);
        break;
    case 'resvAccepted':
        $idx = Crypto::dv($in['idx']);
        $accept = $ko->getAcception(['idx'=>$idx]);
        $ans = ($accept[0]['accepted']=='true') ? '<b>接受</b>' : '<b>拒絕</b>';
        $html = sprintf($SYSMSG[$in['msg']], $ans, $accept[0]['oidx'],  $accept[0]['modifyDateTime']);
        break;
    case 'resvResponsed':
        $idx = Crypto::dv($in['idx']);
        $accept = $ko->getAcception(['idx'=>$idx]);
        $ans = ($accept[0]['accepted']=='true') ? '<b>接受</b>' : '<b>拒絕</b>';
        $html = sprintf($SYSMSG[$in['msg']], $ans);
        break;
    case 'resvFail':
        $html = "設定異常請聯繫管理員。";
        break;
}
?>

<!DOCTYPE html>
<html>
    <head>
    <?php require('head.php'); ?>
    </head>
    <body>
    <?php require('menu.php'); ?>

      <blockquote>
      <h5>系統訊息</h5>
      <span><?=$html?></span>
    </blockquote>
      
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
      $(document).ready(function(){
        $('.sidenav').sidenav();
      });
      </script>

    </body>
</html>