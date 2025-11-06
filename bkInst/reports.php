<?php
require('../includes/auth.php');
require('../includes/sdk.php');
$loggedInstructor = $_SESSION['SKIDIY']['instructor'];
?>

<!DOCTYPE html>
<html>
    <head>
    <?php require('head.php'); ?>
    </head>
    <body>
    <?php require('menu.php'); ?>

    <blockquote>
        <h5>結算統計</h5>
        <span>待設計</span>
    </blockquote>

    <?php require('foot.php'); ?>
        <script>
        $(document).ready(function(){
            $('.sidenav').sidenav();
        });
        </script>
    </body>
</html>