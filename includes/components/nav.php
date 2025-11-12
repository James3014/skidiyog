<?php

function resolveNavDisplayName($context = array())
{
    if (!empty($context['display_name'])) {
        return $context['display_name'];
    }
    if (!empty($context['name'])) {
        return ucfirst($context['name']);
    }
    return '';
}

function renderNav($context = array())
{
    $displayName = resolveNavDisplayName($context);
    $isParkContext = !empty($context['is_park_context']);
    $sections = !empty($context['sections']) ? $context['sections'] : array();
    ?>
    <div class="navbar-fixed">
      <nav>
        <div class="nav-wrapper nav-header">
          <a href="#" data-target="mobile-nav" class="sidenav-trigger"><i class="material-icons">menu</i></a>
          <a href="/" class="brand-link"><img src="https://diy.ski/assets/images/logo-skidiy.png" alt="SKIDIY" class="logo"></a>
          <ul class="hide-on-med-and-down">
            <li><a href="/">雪場資訊</a></li>
            <li><a href="/instructorList.php">教練團隊</a></li>
            <li><a href="/articleList.php">相關文章</a></li>
            <li><a href="https://booking.diy.ski">預訂課程</a></li>
            <li><a href="https://faq.diy.ski" target="_blank">常見問題</a></li>
            <li><a href="mailto:service@diy.ski">聯絡我們</a></li>
          </ul>
          <?php
          if(isset($_SESSION['user_idx'])){
            echo '<a class="waves-effect waves-light btn btn-outline header-login" href="https://'.domain_name.'/account_info.php" >帳號 </a>';
          }else{
            echo '<a class="waves-effect waves-light btn btn-outline header-login" href="https://booking.diy.ski/schedule?action=login&redirect=/schedule" >登入</a>';
          }
          ?>
          <button class="help-entry-btn" type="button" data-help-entry>需要幫忙？</button>
        </div>
      </nav>
    </div>

    <ul class="sidenav" id="mobile-nav">
      <li class="sidenav-header">
        <a href="/" class="sidenav-close logo-link">
          <img src="https://diy.ski/assets/images/logo-skidiy.png" alt="SKIDIY" class="logo">
        </a>
        <?php if(!empty($displayName)){ ?>
          <p class="sidenav-resort"><?=$displayName?></p>
        <?php } ?>
      </li>

      <?php if($isParkContext && !empty($sections)){ ?>
        <li class="sidenav-label">雪場導覽</li>
        <?php
          foreach($sections as $section){
            if(!empty($section['key']) && !empty($section['title'])){
              echo '<li><a class="sidenav-link sidenav-close" href="#'.$section['key'].'">'.$section['title'].'</a></li>';
            }
          }
        ?>
        <li class="sidenav-divider"></li>
      <?php } ?>

      <li class="sidenav-label">SKIDIY</li>
      <?php if($isParkContext){ ?>
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
    <?php
}
