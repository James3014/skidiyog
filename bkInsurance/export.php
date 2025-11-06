<?php
    require('../includes/sdk.php');
    $insuranceFUNC = new INSURANCE();
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
      /*
      if($_POST[$status_index]=='allow' || $_POST[$status_index]=='deny'){
          $update_data['status']    = $_POST[$status_index];
      }else if($_POST[$status_index]=='unchange'){

      }
      */
      if($_POST[$status_index]!='unchange'){
        $update_data['status']    = $_POST[$status_index];
      }

      //var_dump( $update_data);
      $insuranceFUNC->update($insurance_idx,$update_data);

    }

    $filters = array(
        'year'          =>  FILTER_SANITIZE_STRING,
        'cyear'          =>  FILTER_SANITIZE_STRING,
        'month'         =>  FILTER_SANITIZE_STRING,       
        'cmonth'         =>  FILTER_SANITIZE_STRING, 
        'cday'          =>  FILTER_SANITIZE_STRING,            
        'tdays'          =>  FILTER_SANITIZE_STRING,                
        'park'          =>  FILTER_SANITIZE_STRING,
        'instructor'    =>  FILTER_SANITIZE_STRING,
        'status'        =>  FILTER_SANITIZE_STRING,
        'istatus'        =>  FILTER_SANITIZE_STRING,
        'insurance'     =>  FILTER_SANITIZE_STRING,
        'order_idx'     =>  FILTER_SANITIZE_STRING,
        'twid'          =>  FILTER_SANITIZE_STRING,
    );//_v($_POST);
    $in = filter_var_array(array_merge($_REQUEST,$_POST), $filters);//_v($in);//exit();

    $ko = new ko();
    $parkInfo = $ko->getParkInfo();//_v($parkInfo);
    $in['park'] = 'all';
    $in['instructor'] = 'all';
    $in['status'] = Payment::PAYMENT_SUCCESS;

    $in['year']   = ($in['year']==0) ? '9999' : $in['year'];
    $in['cyear']  = ($in['cyear']==0) ? date('Y') : $in['cyear'];
    $in['month']  = ($in['month']==0) ? 'all' : $in['month'];
    $in['cmonth'] = ($in['cmonth']==0) ? date('m') : $in['cmonth'];
    $in['cday']   = ($in['cday']==0) ? 'all' : $in['cday'];
    $in['tdays']  = ($in['tdays']==0) ? '9999' : $in['tdays'];
    $in['order_idx']  = ($in['order_idx']=='') ? '' : $in['order_idx'];
    $in['twid']   = ($in['twid']=='') ? '' : $in['twid'];

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
    if(isset($_REQUEST['istatus'])){
      if($_REQUEST['istatus']==1) $query_arry['status']   ='collecting';
      if($_REQUEST['istatus']==2) $query_arry['status']   ='submit_request';
      if($_REQUEST['istatus']==3) $query_arry['status']   ='allow';
      if($_REQUEST['istatus']==4) $query_arry['status']   ='deny';            // 未能送核
      if($_REQUEST['istatus']==5) $query_arry['status']   ='queue';           // 未處理
      if($_REQUEST['istatus']==6) $query_arry['status']   ='order_canceled';  // 訂單取消
      if($_REQUEST['istatus']==7) $query_arry['status']   ='order_delay';  // 訂單延期
      if($_REQUEST['istatus']==8) $query_arry['status']   ='order_delay_allow';  // 訂單延期且已核保
      if($_REQUEST['istatus']=='all') $query_arry['status']   ='all';
    }else{
      $_REQUEST_REQUEST['istatus']=5; // default
      $query_arry['status']   ='queue';
    }
    //$query_arry['q_year']   =$_POST['year'];
    //$query_arry['q_month']  =$_POST['month'];    
    //$query_arry['type']     ='ALL';
    //$insuranceResult = $insuranceFUNC->get_list_by_query($query_arry);  

    if(!empty($_REQUEST['year']) ){
      $query_arry['type']     ='QUERY';
      /*
      $query_arry['q_year']   =$_POST['year'];
      $query_arry['q_cyear']  =$_POST['cyear'];
      $query_arry['q_month']  =$_POST['month'];
      $query_arry['q_cmonth'] =$_POST['cmonth'];
      $query_arry['q_cday']   =$_POST['cday'];      // 上課日期
      $query_arry['q_tdays']  =$_POST['tdays'];     // 上課總天數
      */

      $query_arry['q_year']   =$in['year'];
      $query_arry['q_cyear']  =$in['cyear'];
      $query_arry['q_month']  =$in['month'];
      $query_arry['q_cmonth'] =$in['cmonth'];
      $query_arry['q_cday']   =$in['cday'];      // 上課日期
      $query_arry['q_tdays']  =$in['tdays'];     // 上課總天數  
      $query_arry['order_idx']=$in['order_idx']; 
      $query_arry['twid']     =$in['twid']; 
      //var_dump($query_arry);     

      
      $insuranceResult = $insuranceFUNC->get_list_by_query($query_arry);   
    }else{  // default for search all
      //$query_arry['type']     ='ALL';
      //echo 'xxx';
      $in['year']   = 9999;
      $in['cyear']  = date('Y');
      $in['month']  = 9999;
      $in['cmonth'] = date('m');
      $in['cday']   = date('d');
      $in['tdays']  = 9999;

      $query_arry['status']   ='all';
      $query_arry['type']     ='QUERY';
      $query_arry['q_year']   =$in['year'];
      $query_arry['q_cyear']  =$in['cyear'];
      $query_arry['q_month']  =$in['month'];
      $query_arry['q_cmonth'] =$in['cmonth'];
      $query_arry['q_cday']   =$in['cday'];      // 上課日期
      $query_arry['q_tdays']  =$in['tdays'];     // 上課總天數 
      
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
        <?php require('menu.php');?>
    </header>



    <blockquote>
        <h5>保單匯出</h5>
    </blockquote>

    <!--form-->
    <form action="?" method="post" id="filter">
    <div class="row">
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


      <div class="input-field col s1">
        <select class="icons year" name="cyear" id="cyear">
          <option value="9999">不限</option>
          <?php for($y=date('Y')+1;$y>=2018;$y--){ ?>
            <option value="<?=$y?>" <?=($in['cyear']==$y)?'selected':''?>><?=$y?></option>
          <?php } ?>
        </select>
        <label><span></span>上課年份</label>
      </div>

      <div class="input-field col s1">
        <select class="icons year" name="cmonth" id="cmonth">
          <option value="9999">不限</option>
          <?php for($y=1;$y<=12;$y++){ ?>
            <option value="<?=$y?>" <?=($in['cmonth']==$y)?'selected':''?>><?=$y?></option>
          <?php } ?>
        </select>        
        <label>上課月份</label>
      </div>       
      <div class="input-field col s1">
        <select class="icons year" name="cday" id="cday">
          <option value="9999">不限</option>
          <?php for($y=1;$y<=31;$y++){ ?>
            <option value="<?=$y?>" <?=($in['cday']==$y)?'selected':''?>><?=$y?></option>
          <?php } ?>
        </select>        
        <label>上課日期</label>
      </div> 

      <div class="input-field col s1">
        <select class="icons year" name="tdays" id="tdays">
          <option value="9999">不限</option>
          <option value="3" <?=($in['tdays']==3)?'selected':''?> >3 天以上</option>
          <option value="5" <?=($in['tdays']==5)?'selected':''?> >5 天以上</option>
          <option value="8" <?=($in['tdays']==8)?'selected':''?> >8 天以上限</option>
        </select>        
        <label>上課天數</label>
      </div>       
   

      <div class="input-field col s3">
        <select name="istatus" id="istatus">
          <option <?=(isset($_REQUEST['istatus']) && $_REQUEST['istatus']=='all')?'selected':''?> value="all">🌀 不限</option>
          <option <?=(isset($_REQUEST['istatus']) && $_REQUEST['istatus']=='1')?'selected':''?> value="1" >資料不齊 (等待其他團員回傳..)</option>
          <option <?=(isset($_REQUEST['istatus']) && $_REQUEST['istatus']=='2')?'selected':''?> value="2" >資料齊全 (等待保險員批准....)</option>
          <option <?=(isset($_REQUEST['istatus']) && $_REQUEST['istatus']=='3')?'selected':''?> value="3" >送至核保</option>
          <option <?=(isset($_REQUEST['istatus']) && $_REQUEST['istatus']=='4')?'selected':''?> value="4" >未能送核</option>          
          <option <?=(isset($_REQUEST['istatus']) && $_REQUEST['istatus']=='5')?'selected':''?> value="5" >未處理</option>     
          <option <?=(isset($_REQUEST['istatus']) && $_REQUEST['istatus']=='6')?'selected':''?> value="6" >訂單取消</option>        
          <option <?=(isset($_REQUEST['istatus']) && $_REQUEST['istatus']=='7')?'selected':''?> value="7" >訂單延期</option>          
          <option <?=(isset($_REQUEST['istatus']) && $_REQUEST['istatus']=='8')?'selected':''?> value="8" >訂單延期且已經核保</option>          
        </select>
        <label><span></span>確認狀態</label>
      </div>
      <div class="input-field col s1" >
        <input type="text" id="order_idx" name="order_idx" value="<?=$in['order_idx'];?>" placeholder="不限">   
        <label><span></span>訂單編號</label>
      </div> 

      <div class="input-field col s1" >        
        <input type="text" id="twid" name="twid" value="<?=$in['twid'];?>" placeholder="查詢相關訂單">   
        <label><span></span>身份證</label>        
      </div>      

      <div class="input-field col s2">
        <button id="filterBtn" class="btn waves-effect waves-light" type="button">查詢</button>
        <button id="exportBtn" class="btn waves-effect waves-light" type="button">匯出</button>
      </div>
      

    </div>
    </form>
    
    <p class="left"><font size="3">       備註說明： <br>       手動修改最久僅能修改到七天前的紀錄！</font><font size="3" color="#f77cc6"><br>       粉紅色背景： 三天以上課程 | </font><font  size="3" color="#cceb34">亮綠色背景： 同一學員，十天以內訂了兩筆以上訂單</font></p>
    <!--form-->
    <table class="order" valign="top" width="100%">
    <thead>
      <tr bgcolor="#b8b6b6">
        <th width="4%"><p class="left">筆數</th>
        <th width="6%"><p class="left">填單時間</th>
        <th width="5%"><p class="left">訂單編號</th>
        <th width="5%"><p class="left">序號</th>
        <th width="6%"><p class="left">上課日期</th>
        <th width="4%"><p class="left">天數</th>
        <th width="5%"><p class="left">核保狀態</th>        
        <th width="5%"><p class="left">身分證號碼</th>  
        <th width="5%"><p class="left">護照名(中)</th>
        <th width="10%"><p class="left">地址</th>
        <th width="5%"><p class="left">電話</p></th>
        <th width="5%"><p class="left">生日</p></th>
        <th width="10%"><p class="left">Email</p></th>
        <th width="4%"><p class="left">法定代理人</th>
        <th width="4%"><p class="left">更新</th>
      </tr>
    </thead>
    <tbody>

<?php


    
    $expire_days =7;
    $utility_func = new UTILITY();
    $c=0;
    if(!empty($insuranceResult))
    foreach ($insuranceResult as $key => $value) {
      $LAST_QUERY = 'act=update&idx='.$value['idx'].'&year='.$in['year'].'&cyear='.$in['cyear'].'&month='.$in['month'].'&cmonth='.$in['cmonth'].'&cday='.$in['cday'].'&tdays='.$in['tdays'].'&istatus='.$in['istatus'];
      $Related_Order_Cnt = $insuranceFUNC->insurance_apply_over_check($value['twid'],10);
      $c++;
      //echo $c.'. '.$value['pcname'].','.$value['birthday'].','.$value['twid'].'<br>';
      $bgcolor='#ffffff';
      $hint_str='';
      if($value['class_days']>3) $bgcolor='#f77cc6';
      if($Related_Order_Cnt >1 ) {
        $bgcolor='#cceb34';
        $hint_str = $Related_Order_Cnt." 筆";
      }
      $dateArray = date_parse_from_format('Y-m-d', $value['birthday']);
      $tw_birthday = ($dateArray['year']-1911).'/'.$dateArray['month'].'/'.$dateArray['day'];
?>
      
  
  <tr bgcolor="<?=$bgcolor; ?>">
    <td><?=$c ?></td>
    <td><?=$value['createDateTime'] ?></td>
    <td><a onClick="oidq('<?=$value['oidx'] ?>')" href="#"><?=$value['oidx'] ?></a><br><?=($value['lock']=='sars')?'<font color="#ff0000">延期</font>':''?></td>
    <td><?=$value['transid'] ?>
<?php
    if(strtotime($value['class_date']) >= (strtotime(date('Y-m-d'))-(86400*$expire_days)) ){
?>    
    <input type="text" value="<?=$value['transid'] ?>" placeholder="更新交易序號" name="trid_<?=$value['idx']?>" id="trid_<?=$value['idx']?>">
<?php
    }
?>    
    </td>
    <td><?=$value['class_date'] ?></td>
    <td><?=$value['class_days'] ?></td>
    <td>
<?php
    //if(strtotime($value['class_date']) >= strtotime(date('Y-m-d'))){
    if(strtotime($value['class_date']) >= (strtotime(date('Y-m-d'))-(86400*$expire_days)) ){  
?>      
      <form action="?<?=$LAST_QUERY ?>" method="post" id="setForm_<?=$value['idx']?>">
      <?=$status_str[$value['status']]?>
      <input type="hidden" name="trid_update_<?=$value['idx']?>" id="trid_update_<?=$value['idx']?>" value="-99" placeholder="更新交易序號" >
      <select class="icons year" name="statusup_<?=$value['idx']?>" id="statusup_<?=$value['idx']?>">
          <option value="unchange"><?=$status_str[$value['status']]?></option>
          <option value="allow">送至核保</option>
          <option value="deny">未能送核</option>
        </select>
      </form>
<?php
    }else{
      echo $status_str[$value['status']];
    }
?>      
    </td>    
    <td><a onClick="idq('<?=$value['twid'] ?>')" href="#"><?=$value['twid'] ?></a> <?=$hint_str;?></td>
    <td><?=$value['pcname'] ?></td>
    <td><?=$value['address'] ?></td>
    <td><?=$value['phone'] ?></td>
    <td><?=$tw_birthday ?></td>
    <td><?=$value['email'] ?></td>
    <td><?=$value['emergencyName'] ?></td>
    <?php
      if($value['status']=='order_canceled' || strtotime($value['class_date']) < (strtotime(date('Y-m-d'))-(86400*$expire_days)) ){   
        echo '<td></td>'; 
      }else{      
    ?>
    <td><button class="btn btn-primary setBtn" oidx="<?=$value['idx']?>">更新</button></td>
    <?php 
      }
    ?>    
  </tr>
  


<?php
if(0){
      echo '<form action="?'.$LAST_QUERY.'" method="post" id="setForm_'.$value['idx'].'">';

      echo '<tr bgcolor="'.$bgcolor.'">';     
      echo '<td>'.$c.'</td>';
      echo '<td>'.$value['oidx'].'</td>';
      echo '<td>'.$value['transid'].'<input type="text" value="" placeholder="更新交易序號" name="trid_'.$value['idx'].'" id="trid_'.$value['idx'].'"></td>';
      echo '<td>'.$value['class_date'].'</td>';
      echo '<td>'.$value['class_days'].'</td>';
      echo '<td>'.$status_str[$value['status']];   
      echo '</td>';

      //echo '<td>'.$utility_func->mask($value['twid'],null,strlen($value['twid'])-5).'</td>';
      echo '<td>'.$value['twid'].'</td>';
      echo '<td>'.$value['pcname'].'</td>';
      echo '<td>'.$value['address'].'</td>';
      echo '<td>'.$value['phone'].'</td>';
      echo '<td>'.$value['birthday'].'</td>';
      echo '<td>'.$value['email'].'</td>';
      echo '<td>'.$value['emergencyName'].'</td>';
      if($value['status']!='order_canceled'){
        echo '<td><button class="btn btn-primary setBtn" oidx="'.$value['idx'].'">更新'.$value['status'].'</button></td>';      
      }else{
        echo '<td></td>';
      }
      echo '</tr>';
      echo '</form>';
}

    }


?>




    </tbody>
    </table>
    





      <!--JavaScript at end of body for optimized loading-->
      <script src="https://diy.ski/assets/js/materialize.min.js"></script>
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
        $('#filterBtn').on('click', function(){
          $('#filter').attr('action','export.php');
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
          }else{ // 非數字（拒保原因）
            //alert('提醒您:交易序號尚未設定');
            $('#trid_update_'+idx).val(trid);
            //alert('idx:'+idx+' , trid <0 :'+$('#trid_update_'+idx).val());
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
      function idq(id){
          $('#twid').val(id);
          $('#filter').attr('action','export.php');
          $('#filter').submit();        
      }
      function oidq(id){
          $('#order_idx').val(id);
          $('#filter').attr('action','export.php');
          $('#filter').submit();        
      }      
      </script> 


    </body>
</html>