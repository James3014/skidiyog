<?php
require('includes/sdk.php');

$ko = new ko();
$lesson = $ko->getGroupLessons($_REQUEST['gidx']);//_v($lesson);exit();
?>
  <!DOCTYPE html>
  <html>
    <head>
      <!--Import Google Icon Font-->
      <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
      <!--Import materialize.css-->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/css/materialize.min.css">

      <!--Let browser know website is optimized for mobile-->
      <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
      <style>
      </style>
    </head>

    <body>

      <div class="row">
      	<div class="col s2">
      		<img src="https://diy.ski/photos/<?=$lesson['instructor']?>/<?=$lesson['instructor']?>.jpg" style="width: 4rem;">
      	</div>
      	<div class="col s6">
      		<h4><?=$lesson['title']?></h4>
      		<div style="width:100px; word-wrap:break-word;"><pre><?=$lesson['content']?></pre></div>
      	</div>
      	<div class="col s4">
      		<?=$lesson['start']?> ～ <?=$lesson['end']?><br>
      		<?=$lesson['fee']?> <?=$lesson['currency']?><br><br><br>
      	</div>
      </div>
      


      <!--JavaScript at end of body for optimized loading-->
      <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/js/materialize.min.js"></script>
      <script src="skidiy.data.php"></script>
      <script src="skidiy.func.php"></script>
      <script>
      function _d(d){console.log(d)}
      function _a(a){alert(a)}

      $(document).ready(function(){
      });
      </script>
    </body>
  </html>