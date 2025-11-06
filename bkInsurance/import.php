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

<?php
    require('../includes/sdk.php');
    $filters = array(
        'year'          =>  FILTER_SANITIZE_STRING,
        'month'         =>  FILTER_SANITIZE_STRING,        
        'park'          =>  FILTER_SANITIZE_STRING,
        'instructor'    =>  FILTER_SANITIZE_STRING,
        'status'        =>  FILTER_SANITIZE_STRING,
        'insurance'     =>  FILTER_SANITIZE_STRING,
    );//_v($_POST);
    $in = filter_var_array(array_merge($_REQUEST,$_POST), $filters);//_v($in);//exit();

    $insuranceFUNC = new INSURANCE();
    $orderFUNC = new ORDER();


    function ToUTF8($contents) {
      //echo mb_detect_encoding($contents, "auto").'<br>';
      $encoding = mb_detect_encoding($contents, array('ASCII','EUC-CN','BIG-5','UTF-8'));
      if ($encoding != false) {
        //echo 'yes'.$encoding;
       $contents = iconv($encoding, 'UTF-8', $contents);
      } else {
       $contents = mb_convert_encoding($contents, 'UTF-8','Unicode');
      }
      return $contents;
    }

    function input_csv($handle) 
    { 
      $out = array (); 
      $n = 0; 
      while ($data = fgetcsv($handle, 10000)) 
      { 
        $num = count($data); 
        for ($i = 0; $i < $num; $i++) 
        { 
          $out[$n][$i] = $data[$i]; 
        } 
        $n++; 
      } 
      return $out; 
    }


    if(isset($_REQUEST['action']) && $_REQUEST['action']=="import" ){
        $filename = $_FILES['file']['tmp_name'];
        $save_to  = 'files/' . date('Y-m-d-H_i_s').'-'.$_FILES['file']['name'];
        if(empty($filename)) 
        { 
          echo '請選擇要匯入的CSV檔案！'; 
          exit; 
        } 

        $handle = fopen($filename, 'r'); 
        if(!$handle){
          echo "file open fail";
        }else{          
          $result = input_csv($handle); //解析csv 
          $len_result = count($result); 
          if($len_result==0) 
          { 
          echo '沒有任何資料！'; 
          exit; 
          } 
          
          //echo 'total record:'.$len_result.'<br>'; // 行數
          $update_cnt=0;
          $first_column_idx = 0;
          $twid_column_idx  = 6;
          $transid_column_idx = 14; // 交易序號
          $order_column_dbidx = 15;
          $order_column_idx   = 16;
          $status_column_idx  = 17;

          for($i = 0; $i < $len_result; $i++) //迴圈獲取各欄位值 
          { 
            /*
            echo 'name:'.$result[$i][0].'<br>';
            $name = iconv('Windows-1252', 'utf-8', $result[$i][0]); //中文轉碼 
            echo 'name:'.$name.'<br>';
            */
            //echo 'name:'.ToUTF8($result[$i][0]);
            //if($i==1) echo '>>'.$result[$i][2];
            if($i==0) continue; //ingore the 1st head line
            
            //_v($result[$i]);
            if($result[$i][$status_column_idx]=='allow' || $result[$i][$status_column_idx]=='deny' || $result[$i][$status_column_idx]=='Y' || $result[$i][$status_column_idx]=='N'){
              /*
              $data_values['pcname']  = $result[$i][0]; //被保險人姓名
              $data_values['pnumber'] = $result[$i][1]; //護照號碼
              $data_values['pename']  = $result[$i][2]; //同護照之英文名稱
              $data_values['birthday']= ($result[$i][3]+1911).'-'.$result[$i][4].'-'.$result[$i][5];
              $data_values['twid']    = $result[$i][6];             
              $data_values['phone']   = $result[$i][7];
              $data_values['email']   = $result[$i][8];
              */         
              /*
              $data_values['']   = $result[$i][$first_column_idx];    // 是否受有監護宣告
              $data_values['']   = $result[$i][$first_column_idx];    // 受益人關係
              $data_values['']   = $result[$i][$first_column_idx];    // 受益人姓名
              $data_values['']   = $result[$i][$first_column_idx];    // 受益人備註
              $data_values['']   = $result[$i][$first_column_idx];    // 
              */
              // (refer to fexport.php line 33）
              $data_values['oidx']    = $result[$i][$order_column_idx];   // 訂單編號
              $data_values['status']  = $result[$i][$status_column_idx];  // 保單狀態       
              $data_values['transid'] = $result[$i][$transid_column_idx]; // 交易序號
              $data_values['transid'] =  ToUTF8($data_values['transid']); // 匯入的檔案 可能已經被存成不同的編碼了
              
              $data_values['twid']    = $result[$i][$twid_column_idx];    // 身分證字號
              if($result[$i][$status_column_idx]=='Y') $data_values['status'] = 'allow';
              if($result[$i][$status_column_idx]=='N') $data_values['status'] = 'deny';              
              

              $where['idx']           = $result[$i][$order_column_dbidx]; // DB 索引編號
              $where['oidx']          = $data_values['oidx'];
              // 因 twid 2020.02.01 後可被修改，暫不當作 where 條件
              //$where['twid']          = $result[$i][6]; // 預設同一筆訂單不會同時出現兩筆一樣的身分證字號
              if(empty($data_values['twid']) && !empty($data_values['pnumber'])){
                $where['pnumber']     = $result[$i][1]; // 非本國人使用 護照當 index
              }
              $insuranceFUNC->update_import($data_values,$where); 


              if($data_values['status'] == 'allow'){
                $update_data['insuranceChecked']=1;
                $orderFUNC->update($data_values['oidx'],$update_data);
              }

              $update_cnt++;
              //echo $i.' write INDEX='.$result[$i][$order_column_dbidx] .' ,oidx='.$result[$i][$order_column_idx] .', st='.$result[$i][$status_column_idx].' ,trid='. $result[$i][14].'<br>';
            }
            
            //_v($data_values);//_v($where);
          }           
          fclose($handle); //關閉指標 
          if($update_cnt>=0){
            move_uploaded_file($filename, $save_to);
          }
          _go('export.php?c='.$update_cnt);
        }
    }





  //_d($query_arry['type']   );

?>





    <body>

    <header>
        <?php require('menu.php');?>
    </header>



    <blockquote>
        <h5>保單匯入</h5>
    </blockquote>

<form id="addform" action="?action=import" method="post" enctype="multipart/form-data"> 
<p>請選擇要匯入的CSV檔案：
<input type="file" name="file"> <input type="submit" class="btn" value="匯入CSV"> 
</p> 
</form><hr> 
注意： 僅針對有被批准或是拒絕之紀錄會被匯回系統．<br>
保單狀態代碼：<br>
已核准：allow<br>
未核准：deny<br>


      <!--JavaScript at end of body for optimized loading-->
      <script src="https://diy.ski/assets/js/materialize.min.js"></script>
      <!--custom js-->
      <script src="https://diy.ski/assets/js/custom.js"></script>

      <script>
      function _d(d){console.log(d)}
      function _a(a){alert(a)}
      <?php
      // if(!empty($_REQUEST['msg'])){
      //   if(isset($SYSMSG[$_REQUEST['msg']])){
      //     echo "alert('{$SYSMSG[$_REQUEST['msg']]}');";
      //   }else{
      //     echo "alert('{$_REQUEST['msg']}');"; 
      //   }
      // }
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

        $('.confirmSelect').on('change',function(){
          var oidx = $(this).attr('oidx');//alert(oidx);
          $('#configForm'+oidx).submit();
        });

        $('.setBtn').on('click',function(e){
          e.preventDefault();//_d('send notify mail');
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