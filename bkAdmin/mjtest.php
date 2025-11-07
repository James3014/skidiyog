<?php
    require('../includes/sdk.php');
    $insuranceFUNC = new INSURANCE();
    $UTILITY_FUNC = new UTILITY();
    $PARK_FUNC = new PARKS();
    // porting from insurance

    // 更新
    if(isset($_REQUEST['act'])  && $_REQUEST['act']=='update'){      
      //_alert('update now');
      //var_dump($_POST);

      $status_index = 'statusup_'.$_REQUEST['idx'];      
      $trid_index = 'trid_update_'.$_REQUEST['idx'];      
      if($_POST[$trid_index] != -99 ){

      }
      //echo "trid >>".$_POST['trid_update'].' ,';
      //echo ' status='.$_POST[$status_index].' , idx='.$_REQUEST['idx'];
      $insurance_idx = $_REQUEST['idx'];
      $update_data['transid']   = $_POST[$trid_index];
      if($_POST[$status_index]=='allow' || $_POST[$status_index]=='deny'){
          $update_data['status']    = $_POST[$status_index];
      }else if($_POST[$status_index]=='unchange'){

      }
      //var_dump( $update_data);
      $insuranceFUNC->update($insurance_idx,$update_data);

    }

    $filters = array(
        'year'          =>  FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        'cyear'          =>  FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        'month'         =>  FILTER_SANITIZE_FULL_SPECIAL_CHARS,       
        'cmonth'         =>  FILTER_SANITIZE_FULL_SPECIAL_CHARS, 
        'cday'          =>  FILTER_SANITIZE_FULL_SPECIAL_CHARS,            
        'tdays'          =>  FILTER_SANITIZE_FULL_SPECIAL_CHARS,                
        'park'          =>  FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        'instructor'    =>  FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        'status'        =>  FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        'istatus'        =>  FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        'insurance'     =>  FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        'order_date_s'  =>  FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        'order_date_e'  =>  FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        'class_date_s'  =>  FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        'class_date_e'  =>  FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        'order_mail'    => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
    );//_v($_POST);
    $in = filter_var_array(array_merge($_REQUEST,$_POST), $filters);//_v($in);//exit();

    $ko = new ko();
    $parkInfo = $ko->getParkInfo();//_v($parkInfo);
    //$in['park'] = 'all';
    $in['instructor'] = 'all';
    $in['status'] = Payment::PAYMENT_SUCCESS;

    $in['year']   = ($in['year']==0) ? '9999' : $in['year'];
    $in['cyear']  = ($in['cyear']==0) ? date('Y') : $in['cyear'];
    $in['month']  = ($in['month']==0) ? 'all' : $in['month'];
    $in['cmonth'] = ($in['cmonth']==0) ? 'all' : $in['cmonth'];
    $in['cday']   = ($in['cday']==0) ? 'all' : $in['cday'];
    $in['tdays']  = ($in['tdays']==0) ? '9999' : $in['tdays'];
    $in['order_mail']  = ($in['order_mail']=='') ? '' : $in['order_mail'];
    

    //$in['order_date_s']  = ($in['order_date_s']=='') ? '9999' : $in['order_date_s'];

    $orders = (empty($in['year'])) ? [] : $ko->getOrders($in);//_j($orders);//exit();
    $groups = $ko->getGroupOrders($in);//_j($groups);exit();

    $status_str['collecting']     = '<font color=#f77cc6>資料不齊</font>';
    $status_str['submit_request'] = '<font color=#02c736>資料齊全</font>';
    $status_str['allow']          = '送至核保';
    $status_str['deny']           = '<font color=#ff0000>未能送核</font>';
    $status_str['Y']              = '送至核保';
    $status_str['N']              = '<font color=#ff0000>未能送核</font>';    
    $status_str['order_canceled'] = '<font color=#ff0000>訂單取消</font>';


    


    // 取得保單資料
    $query_arry['status']   ='all';   // default
    $query_arry['email']    =$in['order_mail'];        
    if(isset($_REQUEST['istatus'])){
      if($_REQUEST['istatus']==1)     $query_arry['status']   ='886'; //tw
      if($_REQUEST['istatus']==2)     $query_arry['status']   ='86';  // ch
      if($_REQUEST['istatus']==3)     $query_arry['status']   ='852'; // hk
      if($_REQUEST['istatus']==4)     $query_arry['status']   ='81';  // jp
      if($_REQUEST['istatus']==5)     $query_arry['status']   ='65';
      if($_REQUEST['istatus']=='all') $query_arry['status']   ='all';
    }else{
      $_REQUEST_REQUEST['istatus']=1; // default
      $query_arry['status']   ='886';
    }
  

    if(!empty($_REQUEST['year']) ){
      $query_arry['type']     ='DELAY_QUERY';
      $query_arry['q_year']   =$in['year'];
      $query_arry['q_cyear']  =$in['cyear'];
      $query_arry['q_month']  =$in['month'];
      $query_arry['q_cmonth'] =$in['cmonth'];
      $query_arry['q_cday']   =$in['cday'];      // 上課日期
      $query_arry['q_tdays']  =$in['tdays'];     // 上課總天數  
      $query_arry['q_o_date_s']  =$in['order_date_s'];     // 訂單日期（起）  
      $query_arry['q_o_date_e']  =$in['order_date_e'];     // 訂單日期（迄）  
      $query_arry['q_c_date_s']  =$in['class_date_s'];     // 上課日期（起）  
      $query_arry['q_c_date_e']  =$in['class_date_e'];     // 上課日期（迄）  
      $query_arry['q_park']      =$in['park'];             // 雪場



      $query_arry['q_country']=$query_arry['status']  ; // workaround for ths status filed in form
      //var_dump($query_arry);     

      
      //$insuranceResult = $insuranceFUNC->get_list_by_query($query_arry);   
      //$query_arry['type']     ='ALL';
      $insuranceResult = $UTILITY_FUNC->get_order_info_list_by_query($query_arry);   

    }else{  // default for search all
      $query_arry['type']     ='ALL';
      $insuranceResult = $insuranceFUNC->get_list_by_query($query_arry);   
    }
  //_d($query_arry['type']   );

?>

<!DOCTYPE html>
<html>
    <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover, user-scalable=false"/>
      
      <!--Import materialize.css-->
      <link rel="stylesheet" href="https://diy.ski/assets/css/materialize.min.css">      
      <!--Import custom.css-->
      <link rel="stylesheet" href="https://diy.ski/assets/css/custom.min.css">
      <!--Import jQuery-->
      <script src="https://diy.ski/assets/js/jquery.min.js"></script>
      
    <style type="text/css">
    table.order{
      font-size: 1rem;
      width: 98%;
      margin: auto;
      border: 1px solid #CCC;
    }
    table.order td, 
    table.order th{
      padding: 3px;
      border-radius: 0px;
    }
    tr.divider td{
      padding: 0.4rem 0.4rem;
      background-color: #ffcc00;
    }
    .card-panel{
      padding: 0.4rem;
    }
    cB{color: blue; font-weight: bold;}
    cR{color: red;}
    sup{
      font-weight: bold;
      color: blue;
      font-size: 0.8rem;
    }
    .input-field>label{
      font-size: 0.8rem;
    }
    a{
      text-decoration: underline;
      font-size: 0.8rem;
    }
    .btn{
      padding: 0.4rem !important;
      border-radius: 6px;
    }
    </style>
    </head>
    <body>

    <header>
        
    </header>

  <main>

    <?php require('menu.php');?>
    <blockquote>
        <h5>訂單查詢</h5>
    </blockquote>

    <!--form-->
    <form action="?" method="post" id="filter">
    <div class="row">

      <div class="input-field col s5" >
        <input type="text" name="order_mail" id="order_mail"  value="<?=$in['order_mail'];?>" placeholder="E-Mail 或 訂單編號">
        <!--<label ><span></span>Email / 訂單編號</label>-->
      </div>
      
    </div> 
    <div class="row">

      <div class="input-field col s3" >
        <input type="text" name="order_date_s" id="order_date_s" class="datepicker2" value="<?=$in['order_date_s'];?>" placeholder="（起始）">
        <label ><span></span>訂單日</label>
      </div>
      <div class="input-field col s3" >
        <input type="text" name="order_date_e" id="order_date_e" class="datepicker2" value="<?=$in['order_date_e'];?>" placeholder="（結束）">
        <label class="active"><span></span>訂單日</label>
      </div>      
      <div class="input-field col s3" >
        <input type="text" name="class_date_s" id="class_date_s" class="datepicker2" value="<?=$in['class_date_s'];?>" placeholder="（開始）">
        <label class="active"><span></span>上課日</label>
      </div>      
      <div class="input-field col s3" >
        <input type="text" name="class_date_e" id="class_date_e" class="datepicker2" value="<?=$in['class_date_e'];?>" placeholder="（結束）">
        <label class="active"><span></span>上課日</label>
      </div>      


      <div class="input-field col s1" style="display:none">
        <select class="icons year" name="year" id="year">
          <option value="9999">不限</option>
          <?php for($y=date('Y')+1;$y>=2018;$y--){ ?>
            <option value="<?=$y?>" <?=($in['year']==$y)?'selected':''?>><?=$y?></option>
          <?php } ?>
        </select>
        <label><span></span>送單年份</label>
      </div>

      <div class="input-field col s1" style="display:none">
        <select class="icons year" name="month" id="month" >
          <option value="9999">不限</option>
          <?php for($y=1;$y<=12;$y++){ ?>
            <option value="<?=$y?>" <?=($in['month']==$y)?'selected':''?>><?=$y?></option>
          <?php } ?>
        </select>        
        <label>送單月份</label>
      </div>  


      <div class="input-field col s1" style="display:none">
        <select class="icons year" name="cyear" id="cyear">
          <option value="9999">不限</option>
          <?php for($y=date('Y')+1;$y>=2018;$y--){ ?>
            <option value="<?=$y?>" <?=($in['cyear']==$y)?'selected':''?>><?=$y?></option>
          <?php } ?>
        </select>
        <label><span></span>上課年份</label>
      </div>

      <div class="input-field col s1" style="display:none">
        <select class="icons year" name="cmonth" id="cmonth">
          <option value="9999">不限</option>
          <?php for($y=1;$y<=12;$y++){ ?>
            <option value="<?=$y?>" <?=($in['cmonth']==$y)?'selected':''?>><?=$y?></option>
          <?php } ?>
        </select>        
        <label>上課月份</label>
      </div>       
      <div class="input-field col s1" style="display:none">
        <select class="icons year" name="cday" id="cday">
          <option value="9999">不限</option>
          <?php for($y=1;$y<=31;$y++){ ?>
            <option value="<?=$y?>" <?=($in['cday']==$y)?'selected':''?>><?=$y?></option>
          <?php } ?>
        </select>        
        <label>上課日期</label>
      </div> 

      <div class="input-field col s1" style="display:none">
        <select class="icons year" name="tdays" id="tdays">
          <option value="9999">不限</option>
          <option value="3" <?=($in['tdays']==3)?'selected':''?> >3 天以上</option>
          <option value="5" <?=($in['tdays']==5)?'selected':''?> >5 天以上</option>
          <option value="8" <?=($in['tdays']==8)?'selected':''?> >8 天以上限</option>
        </select>        
        <label class="active">上課天數</label>
      </div>       
  </div> 
  <div class="row">
      <div class="input-field col s6">
        <select name="park" id="park">
          <option value="all">🌀 不限</option>
          <?=$PARK_FUNC->park_oplist_v2($in['park']);?>
        </select>
        <label class="active"><span></span>雪場</label>
      </div>   

      <div class="input-field col s3">
        <select name="istatus" id="istatus">
          <option <?=(isset($_REQUEST['istatus']) && $_REQUEST['istatus']=='all')?'selected':''?> value="all">🌀 不限</option>
          <option <?=(isset($_REQUEST['istatus']) && $_REQUEST['istatus']=='1')?'selected':''?> value="1" >台灣</option>
          <option <?=(isset($_REQUEST['istatus']) && $_REQUEST['istatus']=='2')?'selected':''?> value="2" >大陸</option>
          <option <?=(isset($_REQUEST['istatus']) && $_REQUEST['istatus']=='3')?'selected':''?> value="3" >香港</option>        
          <option <?=(isset($_REQUEST['istatus']) && $_REQUEST['istatus']=='4')?'selected':''?> value="4" >日本</option>        
          <option <?=(isset($_REQUEST['istatus']) && $_REQUEST['istatus']=='5')?'selected':''?> value="5" >新加坡</option>         
        </select>
        <label class="active"><span></span>國家</label>
      </div>

      <div class="input-field col s3">
        <button id="filterBtn" class="btn waves-effect waves-light" type="button">查詢</button>
        <!--<button id="exportBtn" class="btn waves-effect waves-light" type="button">匯出</button>-->
      </div>
      

    </div>
    </form>
    
    <!--form-->
    <div class="order">
    <table class="order" valign="top" width="90%">
    <thead>
      <tr bgcolor="#b8b6b6">
        <th width="3%"><p class="left">筆數</th>
        <th width="5%"><p class="left">訂單編號</th>
        <th width="5%"><p class="left">訂課日期</th>
        <th width="7%"><p class="left">上課日期</th>
        <th width="5%"><p class="center">人數<br>（核准/已填/應填）</th>
        <th width="3%"><p class="left">Email</th>
        <th width="7%"><p class="left">電話</th>        
        <th width="10%"><p class="left">教練</th>  
        <th width="7%"><p class="left">雪場</th>
        <th width="7%"><p class="left">國籍</th>  

      </tr>
    </thead>
    <tbody>

<?php

              
    $mcontent  = "由於疫情影響
課程系統會自動往後延至10月份
期間如果有確定想預約的時間
再麻煩來信告知
系統會協助更改

另外skidiy下一季已經有確定合作優惠
如果有興趣 也可以在這邊參考
https://diy.ski/article.php?idx=27";
    

    $utility_func = new UTILITY();    
    $c=0;
    if(!empty($insuranceResult))
    foreach ($insuranceResult as $key => $value) {
      $LAST_QUERY = 'act=update&idx='.$value['oidx'].'&year='.$in['year'].'&cyear='.$in['cyear'].'&month='.$in['month'].'&cmonth='.$in['cmonth'].'&cday='.$in['cday'].'&tdays='.$in['tdays'].'&istatus='.$in['istatus'];
      $c++;
      //echo $c.'. '.$value['pcname'].','.$value['birthday'].','.$value['twid'].'<br>';
      $bgcolor='#ffffff';
      //if($value['class_days']>3) $bgcolor='#f77cc6';
      //$dateArray = date_parse_from_format('Y-m-d', $value['birthday']);
      //$tw_birthday = ($dateArray['year']-1911).'/'.$dateArray['month'].'/'.$dateArray['day'];
      $mail_info['subject']  = "重要提醒： 🏂  SKIDIY 課程延期通知 (#".$value['oidx'].")";              // 發信               
      if($value['oidx'] == 11290 || 1==1){
                //$mail_info['email']    = 'mauji168@gmail.com';      
                $mail_info['email']    = $value['email'];
                $mail_info['content']  = $value['name']." 您好\r\n\r\n".$mcontent;   
                $utility_func->send_mail($mail_info);  // 副本給管理者  
                //_d('提醒信件已寄出');          
      }

?>
      
  
  <tr bgcolor="<?=$bgcolor; ?>">
    
    <td><?=$c ?></td>
    <td><a target="_blank" href="https://admin.diy.ski/orders.php?soidx=<?=$value['oidx'] ?>"><?=$value['oidx'] ?><br><?php //$UTILITY_FUNC->change_order_schedule_date($value['oidx']);  ?></a></td>
    <td><?=$value['createDateTime'] ?></td>
    <td><?=$value['day1_class'] ?></td>
    <td><?=$insuranceFUNC->allow_status($value['oidx']) ?></td>
    <td><?=$value['email'] ?></td>
    <td><?=$value['phone'] ?></td>
    <td><?=$value['instructor'] ?></td>
    <td><?=$value['park'] ?></td>
    <td><?=($value['country']=='')?'未填寫':$COUNTRY_CODE2[$value['country']]['cname'] ?></td>
  </tr>
  


<?php
    } // end of foreach
?>




    </tbody>
    </table>
    </div>
  </main>





      <!--JavaScript at end of body for optimized loading-->
      <script src="https://diy.ski/assets/js/materialize.min.js"></script>
      <script src="https://diy.ski/assets/js/select_workaround.js"></script>
      <!--custom js-->
      <script src="https://diy.ski/assets/js/custom.js"></script>

      <script>
      function _d(d){console.log(d)}
      function _a(a){alert(a)}
      <?php
      if(isset($_REQUEST['c']) && $_REQUEST['c']>0){
          echo "alert('保單匯入完成！ 共匯入".$_REQUEST['c']."筆')";
      }
       if(!empty($_REQUEST['msg'])){
         if(isset($SYSMSG[$_REQUEST['msg']])){
          echo "alert('{$SYSMSG[$_REQUEST['msg']]}');";
         }else{
           if($_REQUEST['msg']=='nodata')
           echo "alert('注意:沒有任何資料可匯出，請重新查詢');"; 
         }
       }
      ?>
      </script>


      <script>
      $(document).ready(function(){

          var today = (+new Date()); //Date.now() milliseconds 微秒數        
          var act_date = new Date(today + (86400000 * 3));
          var act_date2 = new Date(today - (86400000 * 365 * 10)); // 可查前半年
          $('.datepicker').datepicker({
            minDate: act_date,
            selectMonths: true, // Creates a dropdown to control month
            selectYears: 100, // Creates a dropdown of 15 years to control year
            format: 'yyyy-mm-dd',
            setDefaultDate: false,
            defaultDate: new Date(),
          });

          $('.datepicker2').datepicker({
            minDate: act_date2,
            selectMonths: true, // Creates a dropdown to control month
            selectYears: 100, // Creates a dropdown of 15 years to control year
            format: 'yyyy-mm-dd',
            setDefaultDate: false,
            defaultDate: new Date(),
          });          

        $('#filterBtn').on('click', function(){
          $('#filter').attr('action','mjtest.php');
          $('#filter').submit();
        });

        $('#exportBtn').on('click', function(){
          $('#filter').attr('action','fexport.php');
          $('#filter').submit();
        });        

        $('.setBtn').on('click',function(e){
          e.preventDefault();//_d('send notify mail');
          var idx = $(this).attr('oidx');//alert($('#trid_'+idx).val());
          var trid = $('#trid_'+idx).val();
          var status = $('#statusup_'+idx).val();
          if(trid>0){
            $('#trid_update_'+idx).val(trid);
            //alert('trid>0:'+$('#trid_update_'+idx).val());
            $('#setForm_'+idx).submit();
          }else{
            //alert('提醒您:交易序號尚未設定');
            $('#trid_update_'+idx).val('');
            //alert('trid <0 :'+$('#trid_update_'+idx).val());
            $('#setForm_'+idx).submit();
          }
        });


        $('.confirmSelect').on('change',function(){
          var oidx = $(this).attr('oidx');//alert(oidx);
          $('#configForm'+oidx).submit();
        });


        $('.emailBtn').on('click',function(e){
          e.preventDefault();//_d('send notify mail');
          var oidx = $(this).attr('oidx');//alert(oidx);
          $('#notify'+oidx).val('yes');
          $('#configForm'+oidx).submit();
        });
      });
      </script> 


    </body>
</html>