<?php
require('includes/sdk.php');
if(!isset($_SESSION['user_idx'])){
  _go('https://'.domain_name.'/account_login.php');
}
$filters = array(
    'park'        =>  FILTER_SANITIZE_STRING,
);//_v($_POST);
$in = filter_var_array(array_merge($_REQUEST,$_POST), $filters);//_v($in);//exit();
$in['instructor'] = empty($_POST['instructor']) ? [] : $_POST['instructor'];
$ko = new ko();
$parkInfo = $ko->getParkInfo();//_v($parkInfo);
$instructorInfo = $ko->getInstructorInfo();
$groupLessons = $ko->getGroupLessons(null,1);//_v($groupLessons);//exit();

if(sizeof($groupLessons)){
  $distinctInfo = $ko->distinctGroupParkInstruct($groupLessons);//_v($distinctInfo);
}

?>
<!DOCTYPE html>
  <html>
    <head>
      <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover, user-scalable=false"/>
      
      <!--Import materialize.css-->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-rc.2/css/materialize.min.css">
      <!--Import custom.css-->
      <link rel="stylesheet" href="assets/css/custom.min.css">
      <!--Import jQuery-->
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
      
    </head>

    <body>
      <header>
        <?php require('nav.inc.php');?>
        <style>
        .group-list div.item-num{
          border: 1px solid #F00 !important;
        }
        .group-list div.item-num:hover{
          border: 1px solid #F00 !important;
        }
        sup{
          color: red;
        }
        .header-block-class {
            height: 200px;
        }        
        </style>
      </header>


      <main>
        <div class="container-fuild">
          <a href="javascript:" id="return-to-top" class="waves-effect waves-light"><i class="material-icons">arrow_upward</i></button></a>
          <div class="row header-block-class">
            <div class="header-img-bottom">
              <img src="assets/images/header_img_bottom.png" alt="">
            </div>
            <img src="assets/images/header_class_main_img.jpg">    
          </div> 

        <div class="row header-block-float">
          <div class="col m3 push-m8 hide-on-small-only">
            <img src="assets/images/class_booking_steps.png" class="steps-img">
          </div>
            <ul class="tabs col s12 m6 offset-m1 pull-m3">
              <li class="tab col s6"><a target="_self" href="schedule.php"><i class="material-icons">stars</i> 私人課程</a></li>
              <li class="tab col s6"><a class="active" target="_self"  href="class_group_list.php"><i class="material-icons">supervised_user_circle</i> 團體課程</a></li>
            </ul>
          <div class="col s12 m6 offset-m1 pull-m3 header-block-content-w">
            <div class="row space-top-1 row-margin-b0">
              <div class="col s11 col-centered" id="private">
                <div class="input-field col s6">
                  <select class="icons park" name="park" id="park">
                    <option value="any">不限</option>
                      <?php foreach ($distinctInfo['park'] as $name) { $parkInfo[$name]['cname'] = ($name=='others') ? '其它': $parkInfo[$name]['cname'];?>
                      <option value="<?=$name?>" data-icon="https://diy.ski/photos/<?=$name?>/<?=$name?>.jpg"><?=$parkInfo[$name]['cname']?></option>
                    <?php } ?>
                  </select>
                  <label>開課雪場</label>
                </div>
                <div class="input-field col s6">
                  <select class="icons instructor" multiple name="instructor[]" id="instructor">
                    <option value="any" selected="selected">不限</option>
                    <?php foreach ($distinctInfo['instructor'] as $name) { ?>
                      <option value="<?=$name?>" data-icon="https://diy.ski/photos/<?=$name?>/<?=$name?>.jpg"
                        <?=(in_array($name, $in['instructor']))?'selected':''?>><?=$instructorInfo[$name]['cname']?></option>
                    <?php } ?>
                  </select>
                  <label>選擇教練(可複選)</label>
                </div>
              </div>
            </div>
          </div>
          
        </div>

        <div class="row group-list margin-top-3">
          <div class="col s12 m10 col-centered container-xl">
            <h5 class="hide-on-small-only">挑選團體課程</h5>
            <p class="hide-on-small-only space-2">三人以上成團，上限六人，一天 5 小時</p>
            <?php if(sizeof($groupLessons)==0){ ?>
              <h5>團體課程尚未開放</h5>
            <?php } ?>

            <?php foreach ($groupLessons as $n => $lesson) {
              $studentNum = $ko->getGroupLessonStudents($lesson['gidx']);
              $available = ($studentNum>=$lesson['max']) ? '已滿' : ($lesson['max']-$studentNum).' 人';
            ?>
            <div class="card-panel item item-link <?=empty($studentNum)?'':'item-num'?>" schedule="p=<?=$lesson['park']?>,i=<?=$lesson['instructor']?>">
              <a href="class_group_detail.php?gidx=<?=$lesson['gidx']?>">
                <div class="row">
                  <div class="col s12 m4 l4 coach">
                    <div class="coach-d" style="width:100%;">
                      <div class="avatar-img">
                        <img src="https://diy.ski/photos/<?=$lesson['instructor']?>/<?=$lesson['instructor']?>.jpg" alt="">
                      </div>
                      <p><?=$instructorInfo[$lesson['instructor']]['cname']?> 教練</p>
                      <span class="badge badge-gray"><?=strtoupper($lesson['expertise'])?></span>
                    </div>
                  </div>
                  <div class="col s12 l6">
                    <h5><?=isset($parkInfo[$lesson['park']]['cname'])?$parkInfo[$lesson['park']]['cname'].', ':''?><?=$lesson['title']?></h5>
                    <p><?=mb_substr($lesson['content'], 0, 60, 'UTF-8')?>...<span class="badge badge-primary">詳細內容</span></p>
                  </div>
                  <div class="info col s6 m3 l3">
                  <?php if(!empty($studentNum)){ ?>
                    <p class="center-align">
                    <b style="font-size:2em; color:red;"><?=$studentNum?></b>人<br>已報名
                      <?php if($studentNum>=$lesson['min']){ ?>
                        <br><sup>確定開班</sup>
                      <?php }else{ ?>
                        <br><sup>尚缺<?=($lesson['min']-$studentNum)?>人開班</sup>
                      <?php } ?>
                    </p>
                  <?php }else{ ?>
                    <b style="font-size:1.2em; color:red;" class="center-align">申請團體開課</b>
                  <?php } ?>
                  </div>
                  <div class="info col s6 m6 l6">
                    <p class="date col s12"><b><?=$lesson['start']?> ～ <?=$lesson['end']?></b></p>
                    <?php if($studentNum<$lesson['max']){ ?>
                    <p class="price col s12"><small>$</small><?=number_format($lesson['fee'])?><small>/人</small> <span class="badge badge-gray"><?=$lesson['currency']?></span></p>
                    <p class="people col s12">開班限制<span class="badge badge-primary"> <?=$lesson['min']?> ~ <?=$lesson['max']?> 人</span></p>
                    <p class="people col s12">可報名人數<span class="badge badge-primary"> <?=$available?></span></p>
                    <?php }else{ ?>
                    <p class="people col s12"><span class="badge badge-primary"><?=$available?></span></p>
                    <?php } ?>
                  </div>
                </div>
              </a>
            </div>
            <?php }//foreach ?>

          </div>
        </div>

      </main>

      
      <footer>
        <div class="footer-copyright">
          <p class="center-align">© 2018 diy.ski</p>
        </div>
      </footer>


      
      <!--JavaScript at end of body for optimized loading-->
      <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-rc.2/js/materialize.min.js"></script>
      <script src="https://diy.ski/assets/js/select_workaround.js"></script>
      
      <!--custom js-->
      <script>
      $(document).ready(function(){
        $('.sidenav').sidenav();
        $('select').formSelect();

        $('select.park, select.instructor').on('change',function(){
          var park = $('#park').val();//_d(park);
          var instructor = $('#instructor').val();//_d(instructor);
          
          $("[schedule]").hide();
          if(park=='any' && instructor=='any'){//_d('1');
            $("[schedule]").show();
          }else if(park=='any' && instructor!='any'){//_d('2');
            $.each(instructor, function(i,name){
              $("[schedule*='i="+name+"']").show();
            });
          }else if(park!='any' && instructor=='any'){//_d('3');
            $("[schedule*='p="+park+",']").show(); 
          }else if(park!='any' && instructor!='any'){//_d('4');
            $.each(instructor, function(i,name){
              $("[schedule*='p="+park+",i="+name+"']").show();
            });
          }else{
            alert('Select error!!');
          }
        });//select

      });
      </script>
      <?=_msg(empty($_REQUEST['msg'])?'':$_REQUEST['msg'])?>
    </body>
  </html>