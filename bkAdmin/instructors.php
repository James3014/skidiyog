<?php
require('../includes/sdk.php');



$filters = array(
    'date'        =>  FILTER_SANITIZE_FULL_SPECIAL_CHARS,
    'expertise'   =>  FILTER_SANITIZE_FULL_SPECIAL_CHARS,
    'park'        =>  FILTER_SANITIZE_FULL_SPECIAL_CHARS,
);//_v($_POST);

$in = filter_var_array(array_merge($_REQUEST,$_POST), $filters);//_v($in);//exit();
$in['date'] = empty($in['date']) ? date('Y-m-d') : $in['date'];
$in['expertise'] = empty($in['expertise']) ? 'sb' : $in['expertise'];
$in['instructor'] = empty($_POST['instructor']) ? [] : $_POST['instructor'];

//var_dump($in);

$ko = new ko();
$parks = $ko->getParkInfo();
$instructors = $ko->getInstructorInfo();

$query_name ='';
if(isset($in['instructor'])){
    //echo "load:".$in['instructor'][0];
    $SECTION_HEADER = array(
      'about'  => '自我介紹(必填)',
      'photo' => '教練照片',
      'certificate'  => '滑雪證照',
      'remind'  => '選課注意事項',
      'cloth' => '教練本季辨識服裝',    
    );    


    if(isset($in['instructor'][0]) && $in['instructor'][0] != 'any' ) {
        $query_name = $in['instructor'][0];
        $DB = new db();
        //$INSTRUCTORS = new INSTRUCTORS();
        //$instructor_basic_info = $INSTRUCTORS->get_instructor_profile_by_Name($query_name);    

        $sql = "SELECT * FROM `instructors` WHERE `name`='{$query_name}'"; //_d($sql);
        $r2 = $DB->QUERY('SELECT',$sql);//_v($r2);
        if(isset($r2[0]['name'])){
            foreach($r2 as $n => $v){
                //var_dump($v);
                $section_content[$v['section']]=$v['content'];  
            }       
            //var_dump($section_content);
        }
    }
}

?>
<!DOCTYPE html>
  <html>
    <head>
      <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover, user-scalable=false"/>
      
      <!--Import materialize.css-->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-rc.2/css/materialize.min.css">
      <!--Import custom.css-->
      <link rel="stylesheet" href="../assets/css/custom.min.css">
      <!--Import jQuery-->
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
      
      <!--swiper-->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.3.3/css/swiper.min.css">
      <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.3.3/js/swiper.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.3.3/js/swiper.esm.bundle.js"></script>

      
    </head>

    <body>
      <header>
        <?php require('menu.php');?>
      </header>


    <!--form-->
    <form action="?q=yes" method="post" id="query_form">            
          <div class="col s12 m6 offset-m1 pull-m3 header-block-content-w">
            <div class="row space-top-1 row-margin-b0">
              
              <div class="col s11 col-centered" id="private">
                <!--
                <div class="input-field col s6">
                  <select class="icons park" name="park" id="park">
                      <option value="any">選擇雪場</option>
                      <?php foreach ($parks as $name => $park) { ?>
                        <option value="<?=$name?>" data-icon="https://diy.ski/photos/<?=$name?>/<?=$name?>.jpg"
                          <?=($in['park']==$name)?'selected':''?>><?=$park['cname']?></option>
                      <?php } ?>                   
                  </select>
                  <label><span></span> 選擇雪場</label>
                </div>
                -->
                <div class="input-field col s6">
                  <select class="icons instructor" name="instructor[]" id="instructor">
                      <option value="any" >選擇教練</option>
                      <?php 
                        foreach ($instructors as $name => $instructor) { 
                          if(!$ko->instructorObsolete($name)){
                      ?>
                        <option value="<?=$name?>" data-icon="https://diy.ski/photos/<?=$name?>/<?=$name?>.jpg"
                          <?=(in_array($name, $in['instructor']))?'selected':''?>><?=$instructor['name']?>, <?=$instructor['cname']?></option>
                      <?php 
                          }
                        } // end of foreach 
                      ?>                    
                  </select>
                  <label><span></span> 選擇教練</label>
                </div>
              </div>
            </div>
          </div>
    </form>
    <!--form-->      



    
          <div class="col s12 m6 offset-m1 pull-m3 header-block-content-w" style="z-index: 0">
            <div class="row space-top-1 row-margin-b0">              
              <form action="instructor_form.php" method="post" id="instructorForm">
              <div class="col s11 col-centered" id="private2">
                <input type="hidden" name="cmd" id="cmd" value="inst_update">
                <input type="hidden" name="action" id="action" value="">
                <input type="hidden" name="qname" value="<?=$query_name?>">
                <h2><?=($query_name=='any' || $query_name=='')?'請選擇教練':$query_name;?></h2>

                <?php 
                    if($query_name != 'any' && $query_name != '')
                    foreach($SECTION_HEADER as $key => $val){                         
                ?>
                
                <div class="row" style="margin-top:2rem;">
                    
                        <h2><?=$SECTION_HEADER[$key]?></h2>
                        <!--<textarea name="<?=$key?>" id="<?=$key?>" cols="10" rows="20"><?=$section_content[$key]?></textarea>-->
                        <div class="input-field col s12">
                          <textarea name="<?=$key?>" id="<?=$key?>" class="materialize-textarea"><?=$section_content[$key]?></textarea>                          
                        </div>                  
                  
                
                </div>
                <?php }reset($SECTION_HEADER); ?>
                <button id="modifybt"  class="btn waves-effect waves-light btn-primary space-top-2 modal-trigger" type="submit" name="action">修改 <i class="material-icons">chevron_right</i></button>                
              </div>
              </form>
            </div>
          </div>
    
      

      <footer>
        <div class="footer-copyright">
          <p class="center-align">© 2018 diy.ski</p>
        </div>
      </footer>

      <div id="success-msg" class="modal center">
        <div class="modal-content">
          <i class="material-icons">beenhere</i>
          <h4>修改成功</h4>
          <div class="row space-top-2">
            <div class="input-field col s12 m8 col-centered">
              <p class="space-2">您所修改的資料己儲存成功。</p> 
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button href="#!" class="modal-close waves-effect btn btn-primary align-center">知道了 <i class="material-icons">check</i></button>
        </div>
      </div>

      <div id="err_msg" class="modal center">
        <div class="modal-content">
          <i class="material-icons">sentiment_very_dissatisfied</i>
          <h4>Ooooops.....</h4>
          <div class="row space-top-2">
            <div class="input-field col s12 m8 col-centered">
              <p id="PERRMSG" class="space-2">....</p> 
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button href="#!" class="modal-close waves-effect btn btn-primary align-center">知道了 <i class="material-icons">check</i></button>
        </div>
      </div> 
      
      <!--JavaScript at end of body for optimized loading-->
      <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-rc.2/js/materialize.min.js"></script>
      <script src="https://diy.ski/assets/js/select_workaround.js"></script>
      
      <!--custom js-->
      <script src="../assets/js/custom.js"></script>
      <script>
      $('.class .material-icons').hide();
      $('.class').click(function () {
        $(this).find('.class-m').toggleClass('class-m-active').find('.coach-name').toggle();
        $(this).find('.class-d').toggleClass('class-d-active');
        $(this).find('.material-icons').toggle();
        return false;
      });
      function _d(d){console.log(d)}
      function _a(a){alert(a)}      
      </script>


      <script>
      $(document).ready(function(){
        
         //alert('x')
        $('select.park').on('change',function(){
            var park = $('#park').val();_d(park);
            $("#query_form").submit();  
        });
        $('select.instructor').on('change',function(){
            var instructor = $('#instructor').val();_d(instructor);    
            $("#query_form").submit();      
        });
   
        $('#modifybt').on('click', function(e){         
                e.preventDefault();
                $.ajax({
                    //url: "account_info.php?act=up_2fcheck",
                    url: "post-cgi.php?cmd=inst_update",                    
                    type: "POST",
                    data: $('#instructorForm').serialize(),                   
                    success: function(resp){
                        //alert("Successfully submitted."+resp)
                        if(resp==101009){ // save ok                              
                             $('#success-msg').modal('open'); 
                        }else{
                            $('#PERRMSG').text('internal err: code='+resp);
                            $('#err_msg').modal('open');                                
                        }                         
                    }
                });
           });
           
           $('#logoutbt').on('click', function(e){         
              window.location.replace('index.php?act=logout') 
        });
        //alert('done');

               
      });
      </script>      

    </body>
  </html>