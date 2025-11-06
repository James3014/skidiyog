<?php
require('includes/sdk.php'); 
//echo 'Login: '.$_SESSION['account']."<br>";
//echo 'Status: '.$_SESSION['status']."<br>";
// 團員（非會員）收信 直接修改
if(isset($_REQUEST['m']) && ($_REQUEST['m']=='m') ) session_destroy();
$ACCOUNT = new MEMBER();
$insuranceFUNC = new INSURANCE();
$POST_CMD = 'insurance_fapply';

$filters = array(
    'act'         => FILTER_SANITIZE_STRING,    
    'id'          => FILTER_SANITIZE_STRING,
    'payment'     => FILTER_SANITIZE_STRING,
);//_v($_POST);
$in = filter_var_array(array_merge($_REQUEST,$_POST), $filters);//_v($_POST['payment']);exit();
$order_id = crypto::dv($in['id']);
if(!is_numeric($order_id)){
  _alert('錯誤保單鏈結，請重新登入!!');
  _go('https://diy.ski/my_order_list.php');
}

  if($insuranceFUNC->check_entry_overflow($order_id)){
    //echo $order_id."xxxx";
  }

if(isset($_REQUEST['m']) && ($_REQUEST['m']=='m' || $_REQUEST['m']=='mm') ){ // edit mode

  $query_arry['type']     ='OIDX_S_BYID';
  $query_arry['oidx']     =$order_id;
  //$query_arry['twid']     =crypto::dv($_REQUEST['mtid']);
  //$query_arry['pno']      =crypto::dv($_REQUEST['mpno']);
  $query_arry['idx']      =crypto::dv($_REQUEST['qid']);
  $insuranceData        = $insuranceFUNC->get_list_by_query($query_arry);  //_v($insuranceData);
  if(count($insuranceData)>=1){
    $insuranceResult = $insuranceData[0];
    //_v($insuranceResult);
  }
  $edit_mode='m';
}else{// 第一次填寫    
    $edit_mode = ''; // new one
    $insuranceResult['twid']      = '';
    $insuranceResult['pnumber']   = '';
    $insuranceResult['pcname']    = '';
    $insuranceResult['pename']    = '';
    $insuranceResult['birthday']  = '';
    $insuranceResult['sex']       = -1;
    $insuranceResult['email']     = '';
    $insuranceResult['phone']     = '';
    $insuranceResult['address']  = '';
    $insuranceResult['country']  = '';
    $insuranceResult['emergencyName']  = '';
    $insuranceResult['inusrance_num'] =1;
    $insuranceResult['c_days']    =1;
    $insuranceResult['ski_days']  =1;
    $insuranceResult['cont_turn'] ='';   
    $insuranceResult['departure_date'] ='';   
    $insuranceResult['arrival_date'] ='';       
    $insuranceResult['day1_class_date']       ='';   
    $insuranceResult['idx']       ='0';   
    
}



?>
<!DOCTYPE html>
  <html>
    <head>
    <?php require('head.php'); ?>
    </head>

    <script>
      $(document).ready(function(){
        $(function(){          
           $('#modifybt').on('click', function(e){         
                e.preventDefault();
                $.ajax({
                    //url: "account_info.php?act=up_2fcheck",
                    url: "post-cgi.php?cmd=<?=$POST_CMD?>&m=<?=$edit_mode?>",                    
                    type: "POST",
                    data: $('#accountinfo-form').serialize(),                   
                    success: function(resp){
                        //alert("Successfully submitted."+resp)
                            if(resp==101005){ // user profile verify & MAIL CHECK PASS
                                $('#email_verify').modal('open');
                            }else if(resp==101008){ // user profile save ok                              
                                 $('#success-msg').modal('open');
                            }else if(resp==<?=ERR_PASSPORT?>){
                                $('#PERRMSG').text('護照號碼填寫不完整');
                                $('#err_msg').modal('open'); 
                            }else if(resp==<?=ERR_PASSPORT_N?>){
                                $('#PERRMSG').text('護照英文名稱填寫不完整');
                                $('#err_msg').modal('open');  
                            }else if(resp==<?=ERR_CHINESE_N?>){
                                $('#PERRMSG').text('被保人姓名需要為中文名稱唷');
                                $('#err_msg').modal('open');                                  
                            }else if(resp==<?=ERR_TWID?>){
                                $('#PERRMSG').text('身分證格式不完整');
                                $('#err_msg').modal('open');                                                                
                            }else if(resp==<?=ERR_EMAIL?>){
                                $('#PERRMSG').text('Email 格式填寫不完整');
                                $('#err_msg').modal('open');  
                            }else if(resp==<?=ERR_ADDR?>){
                                $('#PERRMSG').text('地址內容填寫不完整');
                                $('#err_msg').modal('open'); 
                            }else if(resp==<?=ERR_PHONE?>){
                                $('#PERRMSG').text('電話格式不完整');
                                $('#err_msg').modal('open'); 
                            }else if(resp==<?=ERR_BIRTH?>){
                                $('#PERRMSG').text('生日格式不完整');
                                $('#err_msg').modal('open');
                            }else if(resp==<?=ERR_BIRTH15?>){
                                $('#PERRMSG').text('年滿18歲才能投保');
                                $('#err_msg').modal('open');
                            }else if(resp==<?=ERR_NULL_OID?>){
                                //$('#ERRMSG').text('資料填寫不完整');
                                $('#PERRMSG').text('訂單編號讀取錯誤！');
                                $('#err_msg').modal('open');  
                            }else if(resp==<?=ERR_NULL_COU?>){
                                //$('#ERRMSG').text('資料填寫不完整');
                                $('#PERRMSG').text('請填寫您的國籍！');
                                $('#err_msg').modal('open');                                 
                            }else if(resp==<?=ERR_INFO_DUP?>){
                                //$('#ERRMSG').text('資料填寫不完整');
                                $('#PERRMSG').text('保單資料重複！（您所填寫之身分證已存在本訂單內！）');
                                $('#err_msg').modal('open');                                                                                                  
                            }else if(resp==<?=NULL_INPUT?>){
                                //$('#ERRMSG').text('資料填寫不完整');
                                $('#PERRMSG').text('資料填寫不完整');
                                $('#err_msg').modal('open'); 
                            }else if(resp==<?=ERR_NULL_DA_DATE?>){
                                $('#PERRMSG').text('出發日期或是回國日期尚未填寫');
                                $('#err_msg').modal('open');                                 
                            }else{
                                $('#err_msg').modal('open');                                
                            }                         
                    }
                });
           });
           
          var today = (+new Date()); //Date.now() milliseconds 微秒數        
          var act_date = new Date(today - (86400000 * 3));
          $('.datepicker').datepicker({
            minDate: act_date,
            selectMonths: true, // Creates a dropdown to control month
            selectYears: 100, // Creates a dropdown of 15 years to control year
            format: 'yyyy-mm-dd',
            setDefaultDate: false,
            defaultDate: new Date(),
          });

          $('.countrySelect').on('change',function(){
            var country = $('#nationality').val();
            if(country=='none'){
              $('#PERRMSG').text('請填寫您的國籍！');
              $('#err_msg').modal('open'); 
            }            
            if(country=='CN'){
              window.location.replace('insurance_chnote.php');
            }
            if(country=='HK'){
              window.location.replace('insurance_nontwnote.php');
            }            
          }); 

           $('#logoutbt').on('click', function(e){       
              window.location.replace('index.php?act=logout') 
           });
           $('#myorder').on('click', function(e){         
              window.location.replace('my_order_list.php') 
           }); 

           $('#gotit').on('click', function(e){         
<?php
  // m: 團員收信，修改鏈結
  // mm: 主揪修改
  // ma: 主揪新增
  if(isset($_REQUEST['m']) && ($_REQUEST['m']=='mm'|| $_REQUEST['m']=='ma')  ) {
?>            
              window.location.replace('class_booking_edit.php?id=<?=urlencode($in['id']);?>') 
<?php
  }else{
?>   
              window.location.replace('insurance_note.php')  
<?php
  }
?>                         
           });    

           $('#err_confirm').on('click', function(e){         
              
           });                               
<?php
          if($insuranceFUNC->check_entry_overflow($order_id)){
            echo "$('#PERRMSG').text('已超過本訂單額定之保單人數！，請聯繫本訂單會員．');";
            echo "$('#err_msg').modal('open');";
          }
?>
           //$('#err_msg').modal('open'); 

        });
      });  
    </script>

    <body>
      <header>
        <?php require('nav.inc.php');?>
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
                <p class="text-center">訂單編號:<?=$order_id?></p>
                <p class="resort-name">團員保險資訊 <span></span></p>
              </div> 
          </div> 
        </div>

              <div class="container">
               <div class="row">
                 <div class="col s12 l8 col-centered center">
                    <p class="text-center"></p>
                    <form class="col s12 space-top-2" id="accountinfo-form">
                      <div class="input-field col s12 m6">
                        <label for="" class="active">國籍</label>
                        <!--<input type="text" name="nationality" value="<?=$insuranceResult['country']?>" >-->
                        <select id="nationality" name="nationality" class="countrySelect">   
                          <option value="none" <?=($insuranceResult['country']=='none')?'selected':'' ?> >請選擇</option>
                          <option value="TW" <?=($insuranceResult['country']=='TW')?'selected':'' ?> >台灣</option>
                          <option value="HK" <?=($insuranceResult['country']=='HK')?'selected':'' ?> >香港</option>
                          <option value="CN" <?=($insuranceResult['country']=='CN')?'selected':'' ?> >大陸</option>
                        </select>                         
                      </div>                       
                      <div class="input-field col s12 m6">
                        <label for="">被保人姓名: </label>
                        <input name="p_cn" type="text" value="<?=$insuranceResult['pcname']?>">
                        <span style="color:red;">*18歲以下不得投保</span>
                      </div>
                      <div class="input-field col s12 m6" style="display:none">
                        <label for="">護照號碼</label>
                        <input name="p_no" type="text" value="7777">
                      </div>

                      <div class="input-field col s12 m6">
                        <label for="">同護照之英文名稱</label>
                        <input name="p_en" type="text" value="<?=$insuranceResult['pename']?>">
                      </div>
                      <div class="input-field col s12 m6">
                        <label for="">生日 （yyyy-mm-dd）</label>
                        <input name="birth" type="text" value="<?=$insuranceResult['birthday']?>" placeholder="yyyy-mm-dd">
                      </div>
                      <div class="input-field col s12 m6">
                        <label for="" class="active">性別: </label>
                        <select id="sex" name="sex">   
                          <option value="-1" <?=($insuranceResult['sex']==-1)?'selected':'' ?> >請選擇</option>
                          <option value="0" <?=($insuranceResult['sex']==0)?'selected':'' ?> >女</option>
                          <option value="1" <?=($insuranceResult['sex']==1)?'selected':'' ?> >男</option>
                        </select>
                      </div>                       
                      <div class="input-field col s12 m6">
                        <label for="">身分證字號</label>
                        <input name="tid" type="text" value="<?=$insuranceResult['twid']?>">
                      </div>
                      <!--
                      <div class="input-field col s12 m6">
                        <label for="">密碼</label>
                        <input name="password" type="password">
                      </div>
                      -->
                      <div class="input-field col s12 m6">
                        <label for="">手機</label>
                        <input type="text" name="phone" value="<?=$insuranceResult['phone']?>" >
                      </div>
                      <div class="input-field col s12 m6">
                        <label for="">電子信箱</label>
                        <input type="text" name="mail" value="<?=$insuranceResult['email']?>" >
                      </div>        

                      <div class="input-field col s12 m6">
                        <label for="">地址</label>
                        <input type="text" name="addr" value="<?= $insuranceResult['address']; ?>" placeholder="（必填）">
                      </div>           
                      <div class="input-field col s12 m6">
                        <label for="" class="active" >上課日期</label>
                        <input name="d1_date" type="text" class="datepicker" value="<?= $insuranceResult['day1_class_date']; ?>" placeholder="第一天上課日期" >
                      </div>

                      <div class="input-field col s12 m6" style="display:none">
                        <label for="" class="active" >出發日期</label>
                        <input name="d_date" type="text" class="datepicker" value="<?= $insuranceResult['departure_date']; ?>" placeholder="本次行程出發日期" >
                      </div>
                      <div class="input-field col s12 m6" style="display:none">
                        <label for="" class="active" >回國日期</label>
                        <input name="a_date" type="text" class="datepicker" value="<?= $insuranceResult['arrival_date']; ?>" placeholder="本次行程回國日期">
                      </div> 

                      <!--<div class="input-field col s12 m6">
                        <label for="" class="active">曾經上課的天數 </label>
                        <select id="c_days" name="c_days">   
                          <option value="0" <?=($insuranceResult['c_days']==0)?'selected':'' ?> >請選擇</option>
                          <option value="1" <?=($insuranceResult['c_days']==1)?'selected':'' ?> >無</option>
                          <option value="2" <?=($insuranceResult['c_days']==2)?'selected':'' ?> >1-3 天</option>
                          <option value="3" <?=($insuranceResult['c_days']==3)?'selected':'' ?> >3-7 天</option>
                          <option value="4" <?=($insuranceResult['c_days']==4)?'selected':'' ?> >7 天以上</option>              
                        </select>
                      </div> 
                      <div class="input-field col s12 m6">
                        <label for="" class="active">過去累積滑雪天數（評估程度用）</label>
                        <select id="ski_days" name="ski_days">   
                          <option value="0" <?=($insuranceResult['ski_days']==0)?'selected':'' ?> >請選擇</option>
                          <option value="1" <?=($insuranceResult['ski_days']==1)?'selected':'' ?> >無</option>
                          <option value="2" <?=($insuranceResult['ski_days']==2)?'selected':'' ?> >1-3 天</option>
                          <option value="3" <?=($insuranceResult['ski_days']==3)?'selected':'' ?> >3-14 天</option>
                          <option value="4" <?=($insuranceResult['ski_days']==4)?'selected':'' ?> >15 天以上</option>
                        </select>
                      </div> 
                      <div class="input-field col s12 m6">
                        <label for="" class="active">是否可以連續轉彎？: </label>
                        <select id="turn" name="turn">   
                          <option value="0" <?=($insuranceResult['cont_turn']==0)?'selected':'' ?> >請選擇</option>
                          <option value="1" <?=($insuranceResult['cont_turn']==1)?'selected':'' ?> >否</option>
                          <option value="2" <?=($insuranceResult['cont_turn']==2)?'selected':'' ?> >綠線</option>
                          <option value="3" <?=($insuranceResult['cont_turn']==3)?'selected':'' ?> >紅線</option>
                          <option value="4" <?=($insuranceResult['cont_turn']==4)?'selected':'' ?> >黑線</option>
                        </select>
                      </div>-->

                      <div class="input-field col s12 m6">
                        <label for="" class="active">未滿18歲，請填父母姓名</label>
                        <input type="text" name="emergencyman" value="<?=$insuranceResult['emergencyName']?>" placeholder="ex: 王大明（父）或李珍珍（母）; Richard(Ｆather)">
                      </div>

                      <input type="hidden" name="oidx" value="<?php echo $_REQUEST['id']; ?>" >
<?php
if(isset($_REQUEST['m']) && ($_REQUEST['m']=='m' || $_REQUEST['m']=='mm') ){ // edit mode (from mail link or order page)
?>                      
                      <input type="hidden" name="dbidx" value="<?php echo $_REQUEST['qid']; ?>" >
<?php
}
?>                      
                      </form>   
                      提醒您： 如果您的行程本預定投保富邦產物旅平險，因為同一保險公司規定無法重複加保，
                      在我們投保後，您自行加保的保單會被富邦系統擋下，本次行程請改用「富邦產險以外」的旅平險。 <br>
<?php
 if(!$insuranceFUNC->check_entry_overflow($order_id)){
    if(isset($_REQUEST['m']) && ($_REQUEST['m']=='m' || $_REQUEST['m']=='mm') ){
      $label ='儲存';
    }else{
      $label ='送出';
    }

?>
                        <button id="modifybt"  class="btn waves-effect waves-light btn-primary space-top-2 modal-trigger" type="submit" name="action"><?=$label?> <i class="material-icons">chevron_right</i></button>
<?php
}
?>                        

                    <p>親愛的SKIDIY學員，
因為法規和行政流程的限制，投保的學員需：

1.台灣國籍
2.在台灣有本人或法定代理人的銀行戶頭以利理賠金的匯入。
3.同意委託代為投保。

若以上條件不符，請恕我們無法代為投保，強烈建議您，出發前先在您的國內投保旅行平安險。
很抱歉造成不便，祝您有愉快的旅程！

Dear SKIDIY students ,

Due to law and process restrictions , we can only plan travel accident insurance for students who fit all the following conditions:

1.Taiwanese 
2.Have bank account in Taiwan.
3.Authorize us to plan your Travel Accident Insurance.

If you can’t fit all conditions , then we are sorry that we can’t plan insurance for you.
But we strongly recommend that you plan your Travel Accident Insurance before your journey in your country.
Sorry for the inconvenience and have a nice trip.</p><hr>                        
                                                                                   
                  </div>
               </div>
              </div>

      </main>

      
      <footer>
        <div class="footer-copyright">
          <p class="center-align">© 2018 diy.ski</p>
        </div>
      </footer>

      <!-- Modal -->
      <div id="verify" class="modal center">
        <div class="modal-content">
          <i class="material-icons">phone_iphone</i>
          <h4>手機驗證碼</h4>
          <div class="row">
            <div class="input-field col s12 m8 col-centered">
              <p class="space-2">己將驗證碼傳送至您的手機，<br>請您前往確認後輸入。</p> 
              <input type="text" value="" placeholder="手機簡訊驗證碼" class="center">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button data-target="success-msg" class="modal-trigger modal-close waves-effect btn btn-primary align-center">確認 <i class="material-icons">check</i></button>
        </div>
      </div>

<?php
    if(@$_REQUEST['m']=='m' || @$_REQUEST['m']=='mm' || @$_REQUEST['m']=='ma'){
      $info_string = '已重新修改完成您的保單基本資料資料！';
    }else{
      $info_string = '已送出您的保單基本資料資料！';
    }
?>
      <div id="success-msg" class="modal center">
        <div class="modal-content">
          <i class="material-icons">beenhere</i>
          <h4>保單資料</h4>
          <div class="row space-top-2">
            <div class="input-field col s12 m8 col-centered">
              <p class="space-2"><?=$info_string?><br>審核結果將於出發前兩週寄送至您所填寫信箱。</p> 
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button id="gotit" href="#!" class="modal-close waves-effect btn btn-primary align-center">知道了 <i class="material-icons">check</i></button>
        </div>
      </div>

      <div id="email_verify" class="modal center">
        <div class="modal-content">
          <i class="material-icons">drafts</i>
          <h4>Email 驗證</h4>
          <div class="row space-top-2">
            <div class="input-field col s12 m8 col-centered">
              <p class="space-2">基本資料更新完成！</p> 
              <p class="space-2">請前往您的Email點擊「驗證連結」。</p> 
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
          <button id="err_confirm" href="#!" class="modal-close waves-effect btn btn-primary align-center">知道了 <i class="material-icons">check</i></button>
        </div>
      </div> 
     

      
      <!--JavaScript at end of body for optimized loading-->
      <script src="assets/js/materialize.min.js"></script>
      <script src="https://diy.ski/assets/js/select_workaround.js"></script>
      
      <!--custom js-->
      <script src="assets/js/custom.js?v180920"></script>


    </body>
  </html>