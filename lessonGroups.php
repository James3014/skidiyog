<?php
require('includes/sdk.php');

$ko = new ko();
$groups = $ko->getGroupLessons();//_v($groups);
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

	<?php foreach ($groups as $n => $g) { ?>
      <div class="row">
      	<div class="col s2">
      		<img src="https://diy.ski/photos/<?=$g['instructor']?>/<?=$g['instructor']?>.jpg" style="width: 4rem;">
      	</div>
      	<div class="col s6">
      		<h4><?=$g['title']?></h4>
      		<pre><?=substr($g['content'], 0, 20)?></pre>
      	</div>
      	<div class="col s4">
      		<?=$g['start']?> ～ <?=$g['end']?><br>
      		<?=$g['fee']?> <?=$g['currency']?><br><br><br>
      		<a class="waves-effect waves-light btn" href="lessonGroupsDetail.php?gidx=<?=$g['gidx']?>">詳細內容</a>
      	</div>
      </div>
      <hr>
    <?php } ?>
      


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