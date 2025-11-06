<?php
require('includes/sdk.php'); 
    $ACCOUNT = new MEMBER();
    if(isset($_SESSION['account'])){
      $account_info['email'] = trim($_SESSION['account']);
      $R=$ACCOUNT->get_account($account_info);
      $ko = new ko();
      $parkInfo = $ko->getParkInfo();
      $instructorInfo = $ko->getInstructorInfo();

      $follow = new follow();
      if(isset($_REQUEST['act']) && $_REQUEST['act']=='delete'){
        $follow->delete($R['idx'], $_REQUEST['idx']);
      }
      $followList = $follow->getList($R['idx']);//var_dump($followList);

    }else{
      _go('account_login.php?act=relogin');
    }
?>
<!DOCTYPE html>
  <html>
    <head>
      <?php require('head.php'); ?>
    </head>

    <body>
      <header>
        <?php require('nav.inc.php');?>
        <style>
        cR{color:red;}
        cB{color:blue;}
        </style>
      </header>


      <main>
        <div class="container-fuild">

          <a href="javascript:" id="return-to-top" class="waves-effect waves-light"><i class="material-icons">arrow_upward</i></button></a>
          <div class="row header-block-login">
            <div class="header-img-bottom">
              <img src="assets/images/header_img_bottom.png" alt="">
            </div>
            <img src="assets/images/header_login_main_img.jpg"> 
            <div class="col s10 push-s1  m6 push-m3  header-block-content">
                <p class="text-center"></p>
                <p class="resort-name">課程追蹤 <span></span></p>
                <p><?php echo $R['name']; ?></p>
                <button id="myprofile"  class="btn waves-effect waves-light btn-primary space-top-2" name="logoutbt">回帳號資訊 <i class="material-icons">account_box</i></button>

              </div> 
          </div> 


        <div class="row group-list margin-top-3">
          <div class="col s12 m10 col-centered container-xl">
            <h5>我的追蹤清單</h5>
          </div>

          <div class="col s12 m10 col-centered container-xl">
          <table>
            <thead style="">
              <th></th>
              <th style="width: 5rem;text-align:center;">日期</th>
              <th style="width: 5rem;">雪場</th>
              <th style="width: 4rem;">課程</th>
              <th>教練</th>
            </thead>
            <tbody>
              <?php
                foreach ($followList as $f) {
                  $startDate = date('n/j', strtotime('-3 days', strtotime($f['date'])));
                  $endDate = date('n/j', strtotime('+3 days', strtotime($f['date'])));
              ?>
                <tr>
                  <td>
                    <a href="#" class="delete" idx="<?=$f['idx']?>"><i class="material-icons" style="color:#FAA;">delete_forever</i></a>
                  </td>
                  <td style="text-align:center;"><?=$startDate?><br>~<br><?=$endDate?> </td>
                  <td><?=($f['park']=='any')?'不限':$parkInfo[$f['park']]['cname']?></td>
                  <td><?=($f['expertise']=='sb')?'單板':'雙板'?></td>
                  <td><?php
                    $inst = json_decode($f['instructors'], true);
                    if(count($inst)==1 && $inst[0]=='any'){
                      echo '不限';
                    }else{
                      foreach ($inst as $i) {
                        echo $instructorInfo[$i]['cname'].', ';
                      }
                    }
                  ?>
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
          </div>
        </div>

      </main>

      
      <footer>
        <div class="footer-copyright">
          <p class="center-align">© 2018 diy.ski</p>
        </div>
      </footer>


      
      <!--JavaScript at end of body for optimized loading-->
      <script src="assets/js/materialize.min.js"></script>
      
      <!--custom js-->
      <script src="assets/js/custom.js"></script>
      <script>
      $('.class .material-icons').hide();
      $('.class').click(function () {
        $(this).find('.class-m').toggleClass('class-m-active').find('.coach-name').toggle();
        $(this).find('.class-d').toggleClass('class-d-active');
        $(this).find('.material-icons').toggle();
        return false;
      });


      $(document).ready(function(){
        $('#myprofile').on('click', function(e){         
          window.location.replace('account_info.php') 
        });

        $('.delete').on('click', function(e){
          var idx = $(this).attr('idx');//alert(idx);
          var del = confirm('確認刪除此追蹤？');
          if(del){
            window.location.replace('my_follow_list.php?act=delete&idx='+idx);
          }
        });
      }); 

      <?=_msg(empty($_REQUEST['msg'])?'':$_REQUEST['msg'])?>     
      </script>

    </body>
  </html>