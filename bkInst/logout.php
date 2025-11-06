<?php
require('../includes/auth.php');
unset($_SESSION['SKIDIY']);
session_destroy();

unset($_COOKIE['instname']);	
unset($_COOKIE['instpwd']);	
//unset($_COOKIE['user_rememberme']);	
setcookie("instname",null,time()-3600);
setcookie("instpwd",null,time()-3600);



Header('Location: index.php');