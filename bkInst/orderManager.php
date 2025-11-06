<?php
    require('../includes/sdk.php');

    $filters = array(
        'year'          =>  FILTER_SANITIZE_STRING,
        'month'         =>  FILTER_SANITIZE_STRING,        
        'park'          =>  FILTER_SANITIZE_STRING,
        'instructor'    =>  FILTER_SANITIZE_STRING,
        'status'        =>  FILTER_SANITIZE_STRING,
    );//_v($_POST);

    $in = filter_var_array(array_merge($_REQUEST,$_POST), $filters);//_v($in);//exit();
    //$in['date'] = empty($in['date']) ? date('Y-m-d') : $in['date'];
    $in['year'] = empty($in['year']) ? date('Y') : $in['year'];
    $in['month'] = empty($in['month']) ? date('m') : $in['month'];
    $in['expertise'] = empty($in['expertise']) ? 'sb' : $in['expertise'];
    $in['instructor'] = empty($_POST['instructor']) ? [0=>'any'] : $_POST['instructor'];
    $in['park'] = empty($in['park']) ? 'any' : $in['park'];
    $in['status'] = empty($in['status']) ? 'all' : $in['status'];
    //_v($in);

    $ORDER_OBJ = new ORDER();


    $ko = new ko();
    $parks = $ko->getParkInfo();
    $parkInfo = $ko->getParkInfo();//_v($parkInfo);
    $instructors = $ko->getInstructorInfo(['type'=>'picker','expertise'=>$in['expertise']]);

    // query schedule first
    $schedule_query['park']             = $in['park'];
    if(!empty($in['instructor'])){
        $schedule_query['instructor']   = $in['instructor'][0];
    }
    $schedule_query['month']            = $in['month'];
    $schedule_query['year']             = $in['year'];
    $schedule_query['status']           = $in['status'];
    $order_query_result = $ORDER_OBJ->get_query_order_list($schedule_query);    


?>



<!DOCTYPE html>
  <html>
    <head>
      <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover, user-scalable=false"/>
      
      <!--Import materialize.css-->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-rc.2/css/materialize.min.css">
      <!--Import custom.css-->
      <link rel="stylesheet" href="https://<?=domain_name?>/assets/css/custom.min.css">
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
          <div class="col s12 m6 offset-m1 pull-m3 " >
            <div class="row space-top-1 row-margin-b0">              
              <div class="col s11 col-centered" id="private">
                    <div class="input-field col s2">
                      <select class="icons year" name="year" id="year">                      
                            <option value="all" >不限</option>
                            <?php for($y=date('Y')+1 ;$y>=date('Y'); $y--){ ?>
                            <option <?=($in['year']==$y)?'selected':''?> value="<?=$y?>" ><?=$y;?></option>
                            <?php } ?>                            
                            <!--
                            <option value="2018" >2018</option>
                            <option value="2017" >2017</option>
                            <option value="2016" >2016</option>                  
                            -->
                      </select>
                      <label><span></span> 年份</label>
                    </div>

                    <div class="input-field col s2">
                      <select class="icons month" name="month" id="month">                      
                            <option value="all" >不限</option>
                            <?php for($m=1 ;$m<=12; $m++){ ?>
                            <option <?=($in['month']==$m)?'selected':''?> value="<?=$m?>" ><?=$m;?></option>
                            <?php } ?>                                                                                   
                      </select>
                      <label><span></span> 月份</label>
                    </div>

                    <div class="input-field col s2">
                      <select class="icons park" name="park" id="park">
                          <option value="any">不限雪場</option>
                          <?php foreach ($parks as $name => $park) { ?>
                            <option value="<?=$name?>" data-icon="https://diy.ski/photos/<?=$name?>/<?=$name?>.jpg"
                              <?=($in['park']==$name)?'selected':''?>><?=$park['cname']?></option>
                          <?php } ?>                   
                      </select>
                      <label><span></span> 選擇雪場</label>
                    </div>
                    
                    <div class="input-field col s2">
                      <select class="icons instructor" name="instructor[]" id="instructor">
                          <option value="any" >不限教練</option>
                          <?php foreach ($instructors as $name => $instructor) { ?>
                            <option value="<?=$name?>" data-icon="https://diy.ski/photos/<?=$name?>/<?=$name?>.jpg"
                              <?=(in_array($name, $in['instructor']))?'selected':''?>><?=$instructor['cname']?></option>
                          <?php } ?>                    
                      </select>
                      <label><span></span> 選擇教練</label>
                    </div>

                    <div class="input-field col s2">
                          <select name="status" id="status" class="icons status">
                            <option <?=(Payment::PAYMENT_SUCCESS==$in['status'])?'selected':''?> value="<?=Payment::PAYMENT_SUCCESS?>" ><?=Payment::STATUS_NAME[Payment::PAYMENT_SUCCESS]?></option>
                            <option <?=(Payment::PAYMENT_CREATED==$in['status'])?'selected':''?> value="<?=Payment::PAYMENT_CREATED?>" ><?=Payment::STATUS_NAME[Payment::PAYMENT_CREATED]?></option>
                            <option <?=(Payment::PAYMENT_TIMEOUT==$in['status'])?'selected':''?> value="<?=Payment::PAYMENT_TIMEOUT?>" ><?=Payment::STATUS_NAME[Payment::PAYMENT_TIMEOUT]?></option>
                            <option <?=(Payment::PAYMENT_CANCELING==$in['status'])?'selected':''?> value="<?=Payment::PAYMENT_CANCELING?>" ><?=Payment::STATUS_NAME[Payment::PAYMENT_CANCELING]?></option>
                            <option <?=(Payment::PAYMENT_CANCELED==$in['status'])?'selected':''?> value="<?=Payment::PAYMENT_CANCELED?>" ><?=Payment::STATUS_NAME[Payment::PAYMENT_CANCELED]?></option>
                            <option <?=(Payment::PAYMENT_FAILURE==$in['status'])?'selected':''?> value="<?=Payment::PAYMENT_FAILURE?>" ><?=Payment::STATUS_NAME[Payment::PAYMENT_FAILURE]?></option>
                            <option <?=('all'==$in['status'])?'selected':''?> value="all">🌀 不限</option>
                          </select>
                      <label><span></span>交易狀態</label>
                    </div>
                    <!--
                    <button id="modifybt"  class="btn waves-effect waves-light btn-primary space-top-2 modal-trigger" type="submit" name="action">查詢 <i class="material-icons">chevron_right</i></button>  
                    -->   
 
                    
                         

                  
              </div>

            </div>

          <div class="row space-top-1 row-margin-b0">
                    <div class="input-field row s4"> 
                    <label><span></span> 查詢結果：<?php echo '共計'.count($order_query_result).'筆訂單';?></label>
                    </div>            
          </div>

          </div>

    </form>
    <!--form-->      

<?php

    
    //var_dump($schedule_query);
    $TOTAL_QUERY_INCOME = 0;
    if(!empty($order_query_result))
    foreach($order_query_result as $oidx,$r){
        //echo $oidx." | ";
        //$order = $ko->getOneOrderInfo(['oidx'=>$r['oidx']]);//_v($order);exit();
        //$order = $ORDER_OBJ->getOneOrderInfo(['oidx'=>$r['oidx']],$schedule_query);
        $q['oidx'] = $oidx;
        //_v($r);
        $order_result = $ORDER_OBJ->get_myorder_list($q);
        if(count($order_result)>0){
            $order = $order_result[0];
            $order['schedule'] = $order_query_result[$oidx];
            //_v($order['schedule']);
            //$order['detail'] = json_decode($order['detail'], true);//_v($order);exit();
            if(!empty($order['schedule'])){
                $TOTAL_QUERY_INCOME = $TOTAL_QUERY_INCOME +$order['price'];
                
                //_v($order_info);
                $student = $ko->getMembers(['idx'=>$order['student']]);
                $student = $student[0];//_v($student);
                //_d($order['schedule'][0][date]);
            
?>


          <div class="col s12 m6 offset-m1 pull-m3 "  style="z-index: 0; width: 100%">
            <div class="row space-top-1 row-margin-b0">              
              <div class="col s11 col-centered" id="private2">
                    <div class="input-field col s12">
                    <table class="order" width="100%">                   
                        <thead>
                         <tr>
                           <td colspan="2">
                           
                            <b>訂單編號:#<?=$order['oidx']?><br>           
                           </td>
                           <td colspan="2"><b>訂單日期: <?=$order['schedule'][0]['date'] ?></b></td>
                         </tr>                         
                          <tr bgcolor="#ffcc00">
                              <th width="30%"><p class="left">日期 堂次</p></th>
                              <th width="40%"><p class="left">雪場 教練</p></th>
                              <th width="10%"><p class="left">人數</p></th>
                              <th width="20%"><p class="right">金額</p></th>
                          </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($order['schedule'] as $n => $o) {//_v($o); ?>
                          <tr>
                      
                            <td><?=substr($o['date'],5)?> <?=$parkInfo[$o['park']]['timeslot'][$o['slot']]?><sub><?=$o['slot']?>th</sub></td>
                            <td><p><?=$parkInfo[$o['park']]['cname'].' ' ?><?=ucfirst($o['instructor'])?>/<?=strtoupper($o['expertise'])?></p>
                            <!--
                              <div class="class">
                                <p><?=ucfirst($o['instructor'])?>/<?=strtoupper($o['expertise'])?></p>
                              </div>
                            -->  
                            </td>
                            <td>
                                <?=$o['studentNum']?>
                            </td>
                            <td>
                              <p class="price right"><?=number_format($o['fee'])?><sub><?=$order['currency']?></sub></p>
                            </td>
                          </tr>
                          <?php }//foreach ?>
                         <tr>
                           <td rowspan="5" colspan="2" >                            
                            學生姓名：<?=$student['name']?><br>
                            手機： +<?=$student['country']?> <?=$student['phone']?><br>
                            EMAIL： <?=$student['email']?><br>
                            FB: <?=$student['fbid']?>, Line ID： <?=$student['line']?>, WeChat： <?=$student['wechat']?>  <br>     
                            學生備註：<?=$order['requirement']?><br>
                            管理備註：<?=$order['note']?><br>
                            交易狀態：<?=Payment::STATUS_NAME[$order['status']]?></b>    
                            </td><td>學費</td><td><p class="price right"><?=number_format($order['price'])?><sub><?=$order['currency']?></sub></p></td>
                         </tr>
                         <tr>
                           <td>折扣</td><td><p class="price right"><?=number_format($order['discount'])?><sub><?=$order['currency']?></sub></p></td>
                         </tr>
                         <tr>
                           <td>訂金<td><p class="price right"><?=number_format($order['prepaid'])?><sub><?=$order['currency']?></sub></p></td>
                         </tr>
                         <tr>
                           <td>刷卡<sub><b><?=$order['exchangeRate']?></b></sub><td><p class="price right"><?=number_format($order['paid'])?><sub>NTD</sub></p></td>
                         </tr>
                         <tr>
                           <td>尾款</td><td><p class="price right"><b><?=number_format($order['payment'])?></b><sub><?=$order['currency']?></sub></p></td>
                         </tr>
                         <!--
                         <tr>
                           <td colspan="3"><?=$student['name']?> 聯絡資訊<br>
                            <?=$student['email']?>, +<?=$student['country']?> <?=$student['phone']?><br>
                            FB: <?=$student['fbid']?>, Line ID: <?=$student['line']?>, WeChat: <?=$student['wechat']?>
                           </td>
                           <td>訂單編號:#<?=$order['oidx']?></td>
                         </tr>
                         -->
                        </tbody>
                    </table>                        
                    </div> 

              </div>
            </div>
          </div>
<?php 
            } // end if if(!empty($order['schedule'])){
        }    
    } 
    //echo 'total：'.$TOTAL_QUERY_INCOME.' ';
?>


      

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
      
      <!--custom js-->
      <script src="https://<?=domain_name?>/assets/js/custom.js"></script>      

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
        $('select.year').on('change',function(){
            var y = $('#year').val();_d(y);
            //alert(y);
            $("#query_form").submit();  
        });
        $('select.month').on('change',function(){
            var m = $('#month').val();_d(m);
            //alert('m:'+m);
            $("#query_form").submit();  
        });                 
        $('select.park').on('change',function(){
            var park = $('#park').val();_d(park);
            //alert('p'+park);
            $("#query_form").submit();  
        });
        $('select.instructor').on('change',function(){
            //alert('xx');
            var instructor = $('#instructor').val();_d(instructor);    
            //alert(instructor);
            $("#query_form").submit();      

        });
        $('select.status').on('change',function(){
            var s = $('#status').val();_d(s);
            //alert(park);
            $("#query_form").submit();  
        });        
   
        $('#modifybt').on('click', function(e){         
                e.preventDefault();
                $.ajax({
                    //url: "account_info.php?act=up_2fcheck",
                    url: "post-cgi.php?cmd=park_update",                    
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



