<?php
// load from routing.php
// $target = $name = $section  and section_content[]
$SECTION_HEADER = array(
  'about'  => '介紹',
  'photo' => '照片',
  'location'  => '位置',
  'slope'  => '雪道',
  'ticket' => '雪票',
  'time' => '開放時間',
  'access' => '交通',    
  'live'  => '住宿',
  'rental' => '租借',
  'delivery'  => '宅配',
  'luggage' => '行前裝備',  
  'workout'  => '體能',
  'remind'  => '上課地點及事項',
  'join'  => '約伴及討論',
  //'class'  => '雪場課程',
  //'article'  => '相關文章',
  'event'  => '優惠活動',
  'all'  => '完整閱讀',      
);

$PARKS = new PARKS();
$park_basic_info = $PARKS->getParkInfo_by_Name($name);
//var_dump($park_basic_info);
if(in_array($name,['niseko'])){
  header('Location: https://diy.ski');exit();
}
if(in_array($name,['taipei'])){
  header('Location: https://diy.ski/iski');exit();
}
?>
<!DOCTYPE html>
  <html>
    <head>
      <?php require('pageHeader.php'); ?>
    </head>
    <script type="text/javascript">
      $(document).ready(function(){
        $(function(){          
               $('#ordernow').on('click', function(e){         
                    window.location.replace('../schedule.php?f=p&p=<?=$name?>') 
               }); 
        });
      });  
    </script>
    <body>
      <?php
        require('nav.inc.php');
        if($name=='moiwa'){$park_basic_info['cname']='二世谷';}
        if($name=='gala'){$park_basic_info['cname']='';}
        if($name=='iski'){$park_basic_info['cname']='iSKI';}
      ?>
      <div class="container-fuild">
        <a href="javascript:" id="return-to-top" class="waves-effect waves-light"><i class="material-icons">arrow_upward</i></button></a>
        <div class="row header-block-resort">
            <div class="header-img-bottom"><img src="assets/images/header_img_bottom.png" alt=""></div>
            <img src="https://diy.ski/photos/naeba/3.jpg?v3">
            <div class="col s10 push-s1  m6 push-m3  header-block-content">
              <!--<p class="text-center"><?=$park_basic_info['location']?></p>-->
              <p class="resort-name"><?=$park_basic_info['cname']?>  <small><?=($name!='iski')?ucfirst($name):'滑雪俱樂部'?></small></p>
              <p><?=$park_basic_info['description']?></p>
              <button class="btn waves-effect waves-light btn-primary space-top-2" type="submit" id="ordernow" name="ordernow">現在就預訂 <i class="material-icons">arrow_forward</i></button>
            </div> 
        </div>
      </div>

      <div class="container resort-info">
        <div class="row">
            <div class="col l3 hide-on-med-and-down leftnav">
              <p class="resort-name"><?=$park_basic_info['cname']?> <span><?=($name!='iski')?ucfirst($name):''?></span></p>

              <ul class="tabs tabs-transparent">
<?php
                reset($SECTION_HEADER);
                foreach($SECTION_HEADER as $key => $val){
                  //echo $val;
                  if($name=='niseko'&&$key=='remind') continue;
                  echo '<a href="https://'.domain_name.'/'.$name.'/'.$key.'" class="tab"><li>'.$val.'</li></a>';
                }
?>
              </ul>
            </div>
            <div class="col s12 l9 right resort-content">                 

              
              <?php 
              // 單一斷落
              if(!empty($section) && $section != 'all'){
                echo '<h1 id="intro">'.$SECTION_HEADER[$section].'</h1>';
                //
                if($name=='appi' || $name=='naeba' || $name=='karuizawa'){ // for testing or $name='naeba' or $name='karuizawa'
                  echo $section_content[$section].'<hr>...';
                }else{
                  echo '<pre>'.$section_content[$section].'</pre><hr>';
                }
              }else{ // show all
                reset($SECTION_HEADER);
                foreach($SECTION_HEADER as $key => $val){
                  if($key != 'all'){
                    // 標題
                    echo '<h1 id="intro">'.$SECTION_HEADER[$key].'</h1>';
                    /*
                    //echo '<pre>'.$section_content[$key].'</pre><hr>';
                    echo $section_content[$key].'<hr>';
                    */
                    // 內文
                    if($name=='appi' || $name=='naeba' || $name=='karuizawa'){ // for testing or $name='naeba' or $name='karuizawa'
                      echo $section_content[$key].'<hr>';
                    }else{
                      echo '<pre>'.$section_content[$key].'</pre><hr>';
                    }
                  }
                }

              }
              ?>


            </div>
        </div>
      </div>
      <div style="margin: 0px auto;">
      <button class="btn btn-outline btn-outline-primary" onclick="history.back();"><i class="material-icons">keyboard_arrow_left</i> 回前一頁</button>
      </div>
      <footer class="footer-copyright">
        <div class="container footer-copyright">
            <p class="center-align">©2025 diy.ski</p>
        </div>
      </footer>

      
      <!--JavaScript at end of body for optimized loading-->
      <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-rc.2/js/materialize.min.js"></script>
      
      <!--materialize js Initialize-->
      <script>
          $('.sidenav').sidenav();
          $('.materialboxed').materialbox();
      </script>
    

      <!--window scroll -->
      <script>
        $(document).ready(function () {  
          var top = $('.leftnav').offset().top - parseFloat($('.leftnav').css('marginTop').replace(/auto/, 40));
          
          $(window).scroll(function () {
            var y = $(this).scrollTop();
            if (y >= top) {
              $('.leftnav').addClass('fixed');
              $('#return-to-top').fadeIn();
            } else {
              $('.leftnav').removeClass('fixed');
              $('#return-to-top').fadeOut(); 
            }
            });

            $('#return-to-top').click(function() {
                $('body,html').animate({
                    scrollTop : 0
                }, 500);
            });

        });
      </script>

      <!--left nav & side nav -->
      <script>
        $(function() {
          $('.leftnav a').click(function () {
              $('.leftnav a').removeClass('leftnav-active');
              $(this).addClass('leftnav-active');
           });

          $('.leftnav a, .sidenav a').click(function() {
            if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') 
          && location.hostname == this.hostname) {

                var target = $(this.hash);
                target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
                if (target.length) {
                  $('html,body').animate({
                    scrollTop: target.offset().top - 100 //offsets for fixed header
                  }, 500);
                  return false;
                }
              }
            });
            //Executed on page load with URL containing an anchor tag.
            if($(location.href.split("#")[1])) {
                var target = $('#'+location.href.split("#")[1]);
                if (target.length) {
                  $('html,body').animate({
                    scrollTop: target.offset().top - 100 //offset height of header here too.
                  }, 500);
                  return false;
                }
              }
          });
      </script>
     
    </body>
  </html>