<?php
if(!isset($name)){
  $name = null;
}

$nav_display_name = '';
if(isset($park_info['cname']) && $park_info['cname']!==''){
  $nav_display_name = $park_info['cname'];
} elseif(!empty($name)){
  $nav_display_name = ucfirst($name);
}
?>
      <div class="navbar-fixed">
        <nav>
          <div class="nav-wrapper nav-header">
            <a href="#" data-target="mobile-nav" class="sidenav-trigger"><i class="material-icons">menu</i></a>
            <a href="/" class="brand-link"><img src="/assets/images/logo-skidiy.png?v20251026" alt="" class="logo"></a>
            <ul class="hide-on-med-and-down">
              <li><a href="/">雪場資訊</a></li>
              <li><a href="/instructorList.php">教練團隊</a></li>
              <li><a href="/articleList.php">相關文章</a></li>
              <li><a href="https://booking.diy.ski">預訂課程</a></li>
              <li><a href="https://faq.diy.ski" target="_blank">常見問題</a></li>
              <li><a href="mailto:service@diy.ski">聯絡我們</a></li>
            </ul>
            <!--<a class=" waves-effect waves-light btn btn-outline" href="account_login.php" >登入</a>-->
<?php
            if(isset($_SESSION['user_idx'])){ 
              //echo '<a class=" waves-effect waves-light btn btn-outline" href="https://'.domain_name.'/index.php?act=logout" >登出 '.$_SESSION['name'].'</a>';
              echo '<a class="waves-effect waves-light btn btn-outline header-login" href="https://'.domain_name.'/account_info.php" >帳號 </a>';
              //echo '<button data-target="logout" class="waves-effect waves-light btn-flat space-top-2 modal-trigger" type="submit" >登出 <i class="material-icons">help_outline</i></button>';
            }else{
              echo '<a class="waves-effect waves-light btn btn-outline header-login" href="https://booking.diy.ski/schedule?action=login&redirect=/schedule" >登入</a>';
            }
?>            

          </div>
        </nav>
      </div>
<?php
    $is_park_context = (strstr($_SERVER['PHP_SELF'],'routing') || strstr($_SERVER['PHP_SELF'],'park'));
?>
      <ul class="sidenav" id="mobile-nav">
        <li class="sidenav-header">
          <a href="/" class="sidenav-close logo-link">
            <img src="/assets/images/logo-skidiy.png?v251026" alt="SKIDIY" class="logo">
          </a>
          <?php if(!empty($nav_display_name)){ ?>
            <p class="sidenav-resort"><?=$nav_display_name?></p>
          <?php } ?>
        </li>

        <?php if($is_park_context && isset($SECTION_HEADER) && is_array($SECTION_HEADER)){ ?>
          <li class="sidenav-label">雪場導覽</li>
          <?php
            foreach($SECTION_HEADER as $key => $val){
              if($key == 'all') continue;
              if(strstr($_SERVER['PHP_SELF'],'park')){
                echo '<li><a class="sidenav-link sidenav-close" href="#'.$key.'">'.$val.'</a></li>';
              } else {
                echo '<li><a class="sidenav-link" href="https://'.domain_name.'/'.$name.'/'.$key.'">'.$val.'</a></li>';
              }
            }
          ?>
          <li class="sidenav-divider"></li>
        <?php } ?>

        <li class="sidenav-label">SKIDIY</li>
        <?php if($is_park_context){ ?>
          <li><a class="sidenav-link sidenav-close" href="/">其他雪場</a></li>
          <li><a class="sidenav-link" href="/instructorList.php">教練團隊</a></li>
          <li><a class="sidenav-link" href="/articleList.php">相關文章</a></li>
          <li><a class="sidenav-link" href="/schedule.php">預訂課程</a></li>
        <?php } else { ?>
          <li><a class="sidenav-link sidenav-close" href="/">雪場資訊</a></li>
          <li><a class="sidenav-link" href="/instructorList.php">教練團隊</a></li>
          <li><a class="sidenav-link" href="/articleList.php">相關文章</a></li>
          <li><a class="sidenav-link" href="/schedule.php">預訂課程</a></li>
        <?php } ?>
        <li><a class="sidenav-link" href="https://booking.diy.ski/schedule?action=login&redirect=/schedule">登入</a></li>
        <li><a class="sidenav-link" href="https://faq.diy.ski" target="_blank">常見問題</a></li>
        <li><a class="sidenav-link" href="mailto:service@diy.ski">聯絡我們</a></li>
      </ul>


