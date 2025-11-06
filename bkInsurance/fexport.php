<?php
//require('../includes/sdk.php'); --> config.php 不知為啥 會產生 /x0a  造成多一行
require('../includes/db.class.php');
require('../includes/ko.class.php');
require('../includes/mj.class.php');
require('../includes/crypto.class.php');
function _go($url){echo "<script> window.location.replace('".$url."') </script>";}

	$insuranceFUNC = new INSURANCE();
    // 取得保單資料
    if(isset($_POST['istatus'])){
      if($_POST['istatus']==1) $query_arry['status']   ='collecting';
      if($_POST['istatus']==2) $query_arry['status']   ='submit_request';
      if($_POST['istatus']==3) $query_arry['status']   ='allow';
      if($_POST['istatus']==4) $query_arry['status']   ='deny';
      if($_POST['istatus']==5) $query_arry['status']   ='queue';  // 未處理
      if($_POST['istatus']==6) $query_arry['status']   ='order_canceled';  // 訂單取消
      if($_POST['istatus']=='all') $query_arry['status']   ='all';
    }  
    if(!empty($_REQUEST['year'])  ){
      $query_arry['type']     ='QUERY';
      $query_arry['q_year']   =$_POST['year'];
      $query_arry['q_month']  =$_POST['month'];
      $query_arry['q_cyear']  =$_POST['cyear'];
      $query_arry['q_cmonth'] =$_POST['cmonth'];
      $query_arry['q_cday']   =$_POST['cday'];      
      $query_arry['q_tdays']  =$_POST['tdays'];     // 上課總天數

      $query_arry['order_idx']=$_POST['order_idx']; 
      $query_arry['twid']     =$_POST['twid']; 

      
    }else{  // default for search all
      $query_arry['type']     ='all'; 
    }

    //$query_arry['type']     ='QUERY';   

    //_v($query_arry);
    $insuranceResult = $insuranceFUNC->get_list_by_query($query_arry); 
    //_v($insuranceResult);
    $c=0;
    $csv_arr = array();   
    $csv_arr[] = array(
      '被保險人姓名'                // 0
      ,'護照號碼'
      ,'同護照之英文名稱'
      ,'出生民國年'
      ,'出生月'
      ,'出生日'                    // 5
      ,'身分證字號'
      ,'手機號碼'
      ,'電子信箱'
      ,'是否受有監護宣告'
      ,'受益人關係'                // 10
      ,'受益人姓名'
      ,'受益人備註'
      ,'受益人身份證字號'
      //,'地址'
      //,'法定代理人'
      ,'交易序號'
      ,'索引編號'                 // 15
      ,'訂單編號'
      ,'保單狀態');               // 17 
    $csv_header =$csv_arr;

    
    if($insuranceResult==NULL){        
        //_d('no data');
        _go('export.php?msg=nodata');
    }else{
        foreach ($insuranceResult as $key => $val) {
          $c++;
    	    $date = $val['birthday'];
    	    $dateArray = date_parse_from_format('Y-m-d', $date);
          $csv_arr[] = array(
            $val['pcname']              // 0
            ,''//$val['pnumber']
            ,''//$val['pename']
            ,$dateArray['year']-1911
            ,$dateArray['month']
            ,$dateArray['day']          // 5
            ,$val['twid']
            ,$val['phone']
            ,$val['email']
            ,'N'
            ,'法定繼承'                 // 10
            ,'法定繼承人'
            ,''
            ,''
            //,$val['address']
            //,$val['emergencyName']  // 法定代理人
            ,$val['transid']          // 14
            ,$val['idx']              // 15
            ,$val['oidx']             // 16
            ,$val['status']           // 17
          ); 

        }
        $insurance_export = new UTILITY();
        $insurance_export->toCSV($csv_arr,'Insurance_Export_'.date('Y-m-d_His').'.csv');
        
        /*
      if(1){

      //header('Pragma: no-cache');
      //header('Expires: 0');
      header('Content-Encoding: UTF-8');
      header('Content-Type: text/csv; charset=UTF-8');
      header('Content-Disposition: attachment;filename="' . 'Insurance_Export_'.date('Y-m-d_His').'.csv' . '";');
      }     

      $content='';
      for ($j = 0; $j < count($csv_arr); $j++) {
          if ($j == 0) {
              //檔案標頭如果沒補上 UTF-8 BOM 資訊的話，Excel 會解讀錯誤，偏向輸出給程式觀看的檔案
              //echo "\xEF\xBB\xBF"; // UTF-8 BOM             
          }
          //輸出符合規範的 CSV 字串以及斷行
          $content.=$insurance_export->csvstr($csv_arr[$j]) . PHP_EOL;
          //echo $this->csvstr($csv_arr[$j]) . PHP_EOL;
      }

      $out = $content;

      //$out = mb_convert_encoding($content, 'UTF-16LE', 'UTF-8');
      //echo "\xEF\xBB\xBF";
      echo $out;

        //
        //export_csv($csv_arr,'mj.csv');
        //$insurance_export->writeCSV('Insurance_Export.csv','',$csv_arr);
      */

    }


?>