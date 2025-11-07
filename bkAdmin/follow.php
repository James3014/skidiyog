<?php
require('../includes/sdk.php');
    $filters = array(
        'year'          =>  FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        'month'         =>  FILTER_SANITIZE_FULL_SPECIAL_CHARS,        
        'park'          =>  FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        'instructor'    =>  FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        'status'        =>  FILTER_SANITIZE_FULL_SPECIAL_CHARS,
    );//_v($_POST);
    $in = filter_var_array(array_merge($_REQUEST,$_POST), $filters);//_v($in);//exit();

    $ko = new ko();
    $parkInfo = $ko->getParkInfo();//_v($parkInfo);
    $db = new DB();
    $sql = "SELECT `date`,`park`,`expertise`,`instructors`,`createDateTime`, `name`,`email` 
            FROM `follow` AS f LEFT JOIN `members_v2` AS m ON f.`student`=m.`idx` 
            WHERE f.`deleted`=0 
            ORDER BY `createDateTime` DESC";//_d($sql);
    $info = $db->query('SELECT', $sql);//_v($info);
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
    cR{color:red;}
    cB{color:blue;}
    </style>
    </head>
    <body>
    <main> 
    <?php require('menu.php'); ?>

    <blockquote>
        <h5>追蹤資訊</h5>
    </blockquote>
      <table>
        <thead><th></th><th style="text-align:center;">日期</th><th>雪場</th><th>課程</th><th>教練</th><th>學生名稱</th></thead>
          <?php $cnt=0; foreach ($info as $i) { $cnt++;
            $startDate = date('m/d', strtotime('-3 days', strtotime($i['date'])));
            $endDate = date('m/d', strtotime('+3 days', strtotime($i['date'])));
            $inst = implode(', ', json_decode($i['instructors'], true));
            $inst = ($inst=='any') ? '不限' : $inst;
            $park = ($i['park']=='any'?'不限':$parkInfo[$i['park']]['cname']);
          ?>
            <tr>
              <td><?=$cnt?>.</td>
              <td style="text-align:center;"><?=$startDate?><br>~<br><?=$endDate?></td>
              <td><?=$park?></td>
              <td><?=strtoupper($i['expertise'])?></td>
              <td><?=$inst?></td>
              <td><b><?=$i['name']?></b><br><sup><?=$i['email']?></sup><br><sup><?=$i['createDateTime']?></sup></td>
            </tr>
          <?php }//foreach ?>
      </table>
</main>
      <!--JavaScript at end of body for optimized loading-->
      <script src="https://diy.ski/assets/js/materialize.min.js"></script>
      <!--custom js-->
      <script src="https://<?=domain_name?>/assets/js/custom.js"></script>      

      <script>
      function _d(d){console.log(d)}
      function _a(a){alert(a)}      
      </script>


      <script>
      $(document).ready(function(){
      });
      </script> 


    </body>
</html>