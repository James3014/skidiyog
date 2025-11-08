      <div class="navbar-fixed">
        <nav>
          <div class="nav-wrapper nav-header">
            <a href="#" data-target="mobile-nav" class="sidenav-trigger"><i class="material-icons">menu</i></a>
            <a href="/" ><img src="/assets/images/logo-skidiy.png?v20251026" alt="" class="logo"></a>
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
              echo '<a class=" waves-effect waves-light btn btn-outline" href="https://'.domain_name.'/account_info.php" >帳號 </a>';
              //echo '<button data-target="logout" class="waves-effect waves-light btn-flat space-top-2 modal-trigger" type="submit" >登出 <i class="material-icons">help_outline</i></button>';
            }else{
              echo '<a class=" waves-effect waves-light btn btn-outline" href="https://booking.diy.ski/schedule?action=login&redirect=/schedule" >登入</a>';
            }
?>            

          </div>
        </nav>
      </div>
<?php
    // 雪場 ＆ 教練  手機選單個別處理
    if(strstr($_SERVER['PHP_SELF'],'routing') || strstr($_SERVER['PHP_SELF'],'park') ){
      //$_SERVER['PHP_SELF'].$target;
?>
      <ul class="sidenav" id="mobile-nav">
        <div class="row">
          <a href="#" class="sidenav-close">
            <img src="/assets/images/logo-skidiy.png?v251026" alt=""  class="logo">
          </a>
        </div>
        <li><?=isset($park_info['cname']) ? $park_info['cname'] : ucfirst($name)?></li>
        <li class="subnav">
        <!--
          <p><i class="material-icons">place</i></p>
          <p align="center" class="resort-name2" onclick="$('#childnav').toggle();"><?=$section_content['cname']?> <?=$name?></p>
          -->
          <ul id="childnav">
          <?php
                reset($SECTION_HEADER);
                foreach($SECTION_HEADER as $key => $val){
                  if($key == 'all') continue; // Skip "完整閱讀"
                  // Use anchor links for park.php, route links for routing.php
                  if(strstr($_SERVER['PHP_SELF'],'park')){
                    echo '<a href="#' . $key . '" class="tab"><li>'.$val.'</li></a>';
                  } else {
                    echo '<a href="https://'.domain_name.'/'.$name.'/'.$key.'" class="tab"><li>'.$val.'</li></a>';
                  }
                }
          ?>
          </ul>
        </li>

        <?php if(strstr($_SERVER['PHP_SELF'],'park')){ ?>
        <li><a href="/">其他雪場</a></li>
        <li><a href="/instructorList.php">教練團隊</a></li>
        <li><a href="/articleList.php">相關文章</a></li>
        <li><a href="/schedule.php">預訂課程</a></li>
        <?php } else { ?>
        <li><a href="../">其他雪場</a></li>
        <li><a href="../instructorList.php">教練團隊</a></li>
        <li><a href="../articleList.php">相關文章</a></li>
        <li><a href="https://booking.diy.ski">預訂課程</a></li>
        <?php } ?>
        <li><a href="https://faq.diy.ski" target="_blank">常見問題</a></li>
        <li><a href="mailto:service@diy.ski">聯絡我們</a></li>
      </ul>
<?php
    }else{
?>
      <ul class="sidenav" id="mobile-nav" style="z-index:999999999;">
        <div class="row">
            <a href="/" class="sidenav-close"> <img src="/assets/images/logo-skidiy.png?v251026" alt=""  class="logo"></a>
        </div>
        <li><a href="/">雪場資訊</a></li>
        <li><a href="/instructorList.php">教練團隊</a></li>
        <li><a href="/articleList.php">相關文章</a></li>
        <li><a href="/schedule.php">預訂課程</a></li>
        <li><a href="https://faq.diy.ski" target="_blank">常見問題</a></li>
        <li><a href="mailto:service@diy.ski">聯絡我們</a></li>
      </ul>
<?php
    }
?>


