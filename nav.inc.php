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
            <a href="/" class="brand-link"><img src="https://diy.ski/assets/images/logo-skidiy.png" alt="SKIDIY" class="logo"></a>
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
            echo '<button class="help-entry-btn" type="button" data-help-entry>需要幫忙？</button>';
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
            <img src="https://diy.ski/assets/images/logo-skidiy.png" alt="SKIDIY" class="logo">
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


      <button class="help-entry-fab" type="button" data-help-entry aria-label="需要幫忙？">
        <span>?</span>
      </button>

      <div class="help-drawer" aria-hidden="true" role="dialog" aria-label="常見問題">
        <div class="help-drawer__overlay" data-help-close></div>
        <div class="help-drawer__panel">
          <button class="help-drawer__close" type="button" data-help-close aria-label="關閉">&times;</button>
          <div class="help-drawer__header">
            <p class="helper-pill">Need help?</p>
            <h3>常見問題</h3>
            <p class="helper-subtitle">快速瞭解流程、費用與挑教練方式</p>
          </div>
          <div class="help-drawer__list" data-help-faq-list></div>
          <div class="help-drawer__cta">
            <a href="https://booking.diy.ski/schedule" class="btn btn-primary" data-help-cta target="_blank" rel="noopener">前往預約</a>
          </div>
        </div>
      </div>

      <script>
      (function(){
        const ENTRY_ATTR = '[data-help-entry]';
        const helpButtons = document.querySelectorAll(ENTRY_ATTR);
        const drawer = document.querySelector('.help-drawer');
        if(!drawer || helpButtons.length === 0) return;

        const faqListEl = drawer.querySelector('[data-help-faq-list]');
        const ctaBtn = drawer.querySelector('[data-help-cta]');
        const overlayElements = drawer.querySelectorAll('[data-help-close]');
        let helpOpened = false;

        const FAQ_DATA = {
          general: [
            {
              id: 'faq.general.048',
              title: '預約流程幾步？',
              summary: '線上填表 ➜ 確認細節 ➜ 付款 ➜ 教練聯繫，4 步就能排課。',
              cta: { label: '開始預約', link: 'https://booking.diy.ski/schedule' }
            },
            {
              id: 'faq.general.040',
              title: '費用包含哪些服務？',
              summary: '含教練費、保險、集合與課程通知，需自行加購雪票/裝備。',
              cta: { label: '了解費用', link: 'https://booking.diy.ski/schedule' }
            },
            {
              id: 'faq.instructor.067',
              title: '可以指定教練嗎？',
              summary: '一般訂課可直接在課表挑選，若申請開課需教練回覆同意。',
              cta: { label: '查看教練', link: 'https://diy.ski/instructorList.php' }
            }
          ],
          kids: [
            {
              id: 'faq.general.048',
              title: '預約流程幾步？',
              summary: '線上填表 ➜ 收信確認 ➜ 付款 ➜ 教練聯繫，4 步就能排課。',
              cta: { label: '開始預約', link: 'https://booking.diy.ski/schedule' }
            },
            {
              id: 'faq.kids.071',
              title: '兒童上課如何保障安全？',
              summary: '專用裝備＋緩坡教學，全程安全帽並有家長陪視區。',
              cta: { label: '預約親子課', link: 'https://booking.diy.ski/schedule' }
            },
            {
              id: 'faq.kids.insurance',
              title: '兒童保險要怎麼加購？',
              summary: '課程含基本保險，可在開課前 24 小時內加購兒童專屬保險。',
              cta: { label: '瞭解保險', link: 'https://booking.diy.ski/schedule' }
            }
          ]
        };

        const bodyVariant = document.body.dataset.helpVariant === 'kids' ? 'kids' : 'general';

        function renderFaqItems(){
          const items = FAQ_DATA[bodyVariant] || FAQ_DATA.general;
          faqListEl.innerHTML = items.map(item => `
            <article class="help-faq" data-faq-id="${item.id}">
              <div>
                <p class="help-faq__title">${item.title}</p>
                <p class="help-faq__summary">${item.summary}</p>
              </div>
              <a href="${item.cta.link}" class="help-faq__cta" target="_blank" rel="noopener" data-help-faq-link>
                ${item.cta.label}
              </a>
            </article>
          `).join('');

          const defaultCta = items[0]?.cta || {label:'前往預約', link:'https://booking.diy.ski/schedule'};
          ctaBtn.textContent = defaultCta.label;
          ctaBtn.href = defaultCta.link;
        }

        function openDrawer(){
          renderFaqItems();
          drawer.setAttribute('aria-hidden', 'false');
          document.body.classList.add('help-drawer-open');
          helpOpened = true;
          if(window.gtag){
            gtag('event','help_open',{source:'home',variant:bodyVariant});
          }
        }

        function closeDrawer(){
          drawer.setAttribute('aria-hidden', 'true');
          document.body.classList.remove('help-drawer-open');
        }

        helpButtons.forEach(btn => {
          btn.addEventListener('click', openDrawer);
        });

        overlayElements.forEach(el => el.addEventListener('click', closeDrawer));

        drawer.addEventListener('click', function(evt){
          if(evt.target.matches('[data-help-faq-link]')){
            const faqId = evt.target.closest('.help-faq').dataset.faqId;
            if(window.gtag){
              gtag('event','faq_link_click',{faq_id:faqId,source:'home',position:'drawer'});
            }
          }
        });

        ctaBtn.addEventListener('click', function(){
          if(window.gtag){
            gtag('event','help_to_book',{source:'home',variant:bodyVariant});
          }
        });

        document.addEventListener('keydown', function(evt){
          if(evt.key === 'Escape' && drawer.getAttribute('aria-hidden') === 'false'){
            closeDrawer();
          }
        });

        let highlightTimer = setTimeout(function(){
          if(helpOpened) return;
          helpButtons.forEach(btn => btn.classList.add('help-entry-btn--highlight'));
          setTimeout(() => helpButtons.forEach(btn => btn.classList.remove('help-entry-btn--highlight')), 1500);
        }, 12000);

        window.addEventListener('scroll', function(){
          if(highlightTimer){
            clearTimeout(highlightTimer);
            highlightTimer = null;
          }
        }, { once: true });
      })();
      </script>

