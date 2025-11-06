<?php
Header('Location: https://booking.diy.ski/order/list');exit();
require('includes/sdk.php'); 
    $ACCOUNT = new MEMBER();
    $insuranceFUNC = new INSURANCE();
    if(isset($_SESSION['account'])){
      $account_info['email'] = trim($_SESSION['account']);
      $R=$ACCOUNT->get_account($account_info);
      $ORDER_OBJ = new ORDER();
      //_dbg($R['idx']);
      if(isset($_REQUEST['act']) && $_REQUEST['act']=='cancel'){
        $cancel_id = crypto::dv($_REQUEST['id']);
        //echo $cancel_id.'-'.payment::PAYMENT_CANCELING;
        $ORDER_OBJ->update($cancel_id,['status'=>payment::PAYMENT_CANCELING ]);
        $ko = new ko();
        
        $ko->notify([
               'oidx'              => $cancel_id,
               'type'              => 'orderCanceling',
               'resp'              => 'student',
               'createDateTime'    => date('Y-m-d H:i:s'),
           ]);                
      } 
      $myorder = $ORDER_OBJ->get_myorder_list(['student'=>$R['idx'],'status'=>'success' ]);
      //$myorder = $ORDER_OBJ->get_myorder_list(['student'=>$R['idx']]);//_v($myorder);//exit();
      //var_dump($myorder);
      //echo count($myorder);
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
                <p class="resort-name">訂單資訊 <span></span></p>
                <p><?php echo $R['name']; ?></p>
                <!--
                <button id="logoutbt"  class="btn waves-effect waves-light btn-primary space-top-2" name="logoutbt">登出 <i class="material-icons">exit_to_app</i></button>
                -->
                <button id="myprofile"  class="btn waves-effect waves-light btn-primary space-top-2" name="logoutbt">回帳號資訊 <i class="material-icons">account_box</i></button>

              </div> 
          </div> 


        <div class="row group-list margin-top-3">
          <div class="col s12 m10 col-centered container-xl">
            <h5>我的訂單資訊</h5>
          </div>
          <div class="col s12 m10 col-centered container-xl">
            <!--<p class="text-right">保險填寫連結👉<a href="http://goo.gl/vh5noU">http://goo.gl/vh5noU</a></p>-->
            <p class="text-right">保險填寫連結👉<a href="https://diy.ski/insurance_s.php">我們已經搬家囉～點我說明</a></p>
          </div>
          <div class="col s12 m10 col-centered container-xl">

<?php 
$ko = new ko();
$parkInfo = $ko->getParkInfo();

foreach($myorder as $n => $r){
    $order_id = $r['oidx'];        
    $order = $ko->getOneOrderInfo(['oidx'=>$order_id]);
    //var_dump($order['schedule']);

    if(!empty($order['gidx'])){//團體課程
      $groupOrderInfo = $ko->getGroupOrderInfo($order['gidx']);//_v($groupOrderInfo['group']);
      $order['schedule'][] = [
        'park'        =>  $groupOrderInfo['group']['park'],
        'instructor'  =>  $groupOrderInfo['group']['instructor'],
        'expertise'   =>  $groupOrderInfo['group']['expertise'],
        'studentNum'  =>  1,
        'date'        =>  $groupOrderInfo['group']['start'],
      ];
    }
    
    $c =0;
    $order_summary['man_min'] = 10000;
    $order_summary['man_max'] = 1;

    $order_summary['date_min'] = 0;
    $order_summary['date_max'] = 0;
    $order_summary['class_cnt']= 0;
    foreach ($order['schedule'] as $n => $o) {
        $c++;
        $order_summary['class_cnt']=$c;    
        $order_summary['park']=isset($parkInfo[$o['park']]['cname'])?$parkInfo[$o['park']]['cname']:'';
        $order_summary['instructor']= $o['instructor'];
        $order_summary['expertise']= strtoupper($o['expertise']);

        if( $o['studentNum'] < $order_summary['man_min'] ) $order_summary['man_min'] =  $o['studentNum'] ;
        if( $o['studentNum'] > $order_summary['man_max'] ) $order_summary['man_max'] =  $o['studentNum'] ;
        if($c==1) $order_summary['date_min'] = strtotime($o['date']);
        if( strtotime($o['date']) < $order_summary['date_min'] ) $order_summary['date_min'] =  strtotime($o['date']) ;
        if( strtotime($o['date']) >= $order_summary['date_max'] ) $order_summary['date_max'] =  strtotime($o['date']) ;
    }
    if($order_summary['man_min'] == $order_summary['man_max']) $order_summary['people'] = $order_summary['man_min'];
    if($order_summary['man_min'] != $order_summary['man_max']) $order_summary['people'] = $order_summary['man_min'].' ~ '.$order_summary['man_max'];

    if($order_summary['date_min'] == $order_summary['date_max']) $order_summary['oder_date'] = date('m/d',$order_summary['date_min']);
    if($order_summary['date_min'] != $order_summary['date_max']) $order_summary['oder_date'] = date('m/d',$order_summary['date_min']).' ~ '.date('m/d',$order_summary['date_max']);


    if($order_summary['class_cnt'] > 0){
      if($order['status'] != 'fail'){
      $oid = crypto::ev($order_id);      
?>
<a href="class_booking_edit.php?id=<?=$oid?>">
        <div class="card-panel item item-link">
              
            <div class="row">
                <div class="col s12 l3 coach">
                  <div class="coach-d">
                    <div class="avatar-img">
                      <img src="https://diy.ski/photos/<?=$order_summary['instructor']?>/<?=$order_summary['instructor']?>.jpg" alt="">
                    </div>
                    <p><?=($order_summary['instructor']=='virtual')?'教練未定':ucfirst($order_summary['instructor'])?></p>
                    <span class="badge badge-gray"><?=$order_summary['expertise']?></span>
                  </div>
                </div>
                
                <div class="col s12 l6">
                  <h5><?=$order_summary['park']?></h5>
                  <p><?=($order['gidx']!=0)?'團體課程':'私人課程'; ?> : <?=$order_summary['oder_date']?><?=($order['lock']=='sars')?'<cR> (課程已延期)</cR>':''; ?></p>  
                  <?php if($order['gidx']!=0){
                    echo '課程名稱 : '.$groupOrderInfo['group']['title'].'<br>';
                  } ?>
                  
                  <p><?='訂單狀態 : '.payment::STATUS_NAME[$order['status']].'<br>訂單(保險)編號: #'.$order_id?></p>
                  <!--<p><?=$order['insuranceChecked']?'<cB>✅保險已確認</cB>':'<cR>🚫保險未確認</cR>'?></p>-->
                  保險資料： <?=$INSURANCE_STATUS_LABEL[$insuranceFUNC->check_order_status($order['oidx'])];?>
                </div>            

                <div class="info col s12 l4">
                  <p class="date col s12">課堂 <span class="badge badge-gray"><?=$order_summary['class_cnt']?></span></p>
                  <p class="price col s12"><small>$</small><?=$order['price']?> <span class="badge badge-gray"><?=$order['currency']?></span></p>
                  <p class="people col s12">人數<span class="badge badge-primary"><?=$order_summary['people']?></span></p>
                </div>

            </div>              
        </div>
</a>

<?php
      } // end of if status check
    }
}
?>
            <!--
            <div class="card-panel item item-link">
              <div class="row">
                <div class="col s12 l3 coach">
                  <div class="coach-d">
                    <div class="avatar-img">
                      <img src="https://diy.ski/photos/james/james.jpg" alt="">
                    </div>
                    <p>James</p>
                    <span class="badge badge-gray">SB</span>
                  </div>
                </div>
                <div class="col s12 l6">
                  <h5>白馬 SB Advance 進階班</h5>
                  <p>初次嘗試滑雪的朋友們，在初級班指導員細心耐心指導下，從穿脫雪具開始，慢慢進入基本滑行、煞車、上下纜車的教學。</p>
                </div>
                <div class="info col s12 l4">
                  <p class="date col s12">2018/12/1-2019/01/05 <span class="badge badge-gray">6天</span></p>
                  <p class="price col s12"><small>$</small>46,000 <span class="badge badge-gray">NTD</span></p>
                  <p class="people col s12">可報名人數<span class="badge badge-primary"> 3 人</span></p>
                </div>
              </div>
            </div>
            -->

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
        $(function(){             
          $('#myprofile').on('click', function(e){         
              window.location.replace('account_info.php') 
          }); 

        });
      }); 

      <?=_msg(empty($_REQUEST['msg'])?'':$_REQUEST['msg'])?>     
      </script>

    </body>
  </html>