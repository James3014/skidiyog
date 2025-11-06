<?php
Header('Location: https://instructors.diy.ski');exit();
require('../includes/sdk.php');
require('../includes/cauth.php');

?>
<!DOCTYPE html>
<html>
    <head>
    <?php require('head.php'); ?>
    </head>
    <body>
        <div class="row">
            <div class="col s10 offset-s1 m8 offset-m2 l8 offset-l4">
                <h5>SKIDIY 教練後台</h5>
                <form action="login.php" method="post">
                    <input type="text" name="email" placeholder="Email">
                    <input type="password" name="password" placeholder="密碼">
                    <button class="btn waves-effect waves-light" type="submit">登入</button>
                        <?php 
                            $check_string = (isset($_COOKIE['inst_reme']) && $_COOKIE['inst_reme'] == 'y') ? 'checked="checked"':'';
                        ?>                    
                        <label class="align-center">
                          <input id="rememberme" name="rememberme" type="checkbox" class="filled-in"  <?=$check_string; ?> />
                          <span> 記住我</span>
                        </label>                    
                </form>
            </div>
        </div>


    <?php require('foot.php'); ?>
    <script>
    $(document).ready(function(){

    });
    <?php
      if(isset($_REQUEST['msg'])){
        if(isset($SYSMSG[$_REQUEST['msg']])){
          echo "alert('{$SYSMSG[$_REQUEST['msg']]}');";
        }
      }
    ?>
    </script>
    </body>
</html>