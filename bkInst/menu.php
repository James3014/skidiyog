  <nav>
    <div class="nav-wrapper">
      <a href="#" data-target="mobile-demo" class="sidenav-trigger"><i class="material-icons">menu</i></a>
      SKIDIY 教練後台
      <ul class="right hide-on-med-and-down">
        <li><a href="#">Hi! <?=ucfirst($instructor[$loggedInstructor]['name'])?> 教練</a></li>
        <li><a href="schedule.php">📅 上課排程</a></li>
        <li><a href="orders.php">💰 訂單資訊</a></li>
        <!--<li><a href="messages.php">聯絡訊息</a></li>-->
        <li><a href="certificate.php">🆔 教練證</a></li>
        <li><a href="follow.php">💟追蹤資訊</a></li>
        <li><a href="logout.php">登出</a></li>
      </ul>
    </div>
  </nav>
  <ul class="sidenav" id="mobile-demo">
        <li>Hi! <?=ucfirst($instructor[$loggedInstructor]['name'])?> 教練</li>
        <li><a href="schedule.php">📅 上課排程</a></li>
        <li><a href="orders.php">💰 訂單資訊</a></li>
        <li><a href="certificate.php">🆔 教練證</a></li>
        <!--<li><a href="messages.php">聯絡訊息</a></li>-->
        <li><a href="follow.php">💟追蹤資訊</a></li>
        <li><a href="logout.php">◀登出</a></li>
  </ul>