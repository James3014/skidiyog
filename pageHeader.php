  <?php

    if(strstr($_SERVER['SCRIPT_NAME'],'article'))  $target = 'article';
    if(strstr($_SERVER['SCRIPT_NAME'],'schedule')) $target = 'schedule';
    if(strstr($_SERVER['SCRIPT_NAME'],'booking'))  $target = 'booking';
    if(strstr($_SERVER['SCRIPT_NAME'],'payment'))  $target = 'payment';
    if(empty($target)) $target = 'index';
    //$target = 'index';
    if(empty($SEO_OG_DESC)) $SEO_OG_DESC="最豐富的自助滑雪資訊，包含日本12個知名滑雪勝地的雪場資訊、交通方式、住宿地點等等⋯⋯。讓您輕輕鬆鬆日本滑雪去。" ;
  ?>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

<!--  add by mj -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover"/>
    <!--Import materialize.css-->
    <link rel="stylesheet" href="https://diy.ski/assets/css/materialize.min.css">
    <!--Import custom.css-->
    <link rel="stylesheet" href="https://diy.ski/assets/css/custom.min.css">
    <!--  add by mauji for over write some css style -->
    <link rel="stylesheet" href="https://diy.ski/assets/css/workaround.css"> 
    <!--Import jQuery-->    
    <script src="https://diy.ski/assets/js/jquery.min.js"></script> 



    <meta name="B-verify" content="69017dc01d91abdba660cee1095ced1e042497f9" />
    <link rel="shortcut icon" href="/assets/images/favicon_skidiy.png" type="image/png" />

    <?php if(isset($name) && $name=='virtual'){ ?>
        <title>SKIDIY 自助滑雪 - 預約教練</title>
    <?php }elseif($target=='article'){ ?>
        <title>SKIDIY 自助滑雪 - 相關文章</title>        
    <?php }else{ @$tName = ($name=='iski')?'iSKI 滑雪俱樂部':$name; ?>
        <title>SKIDIY 自助滑雪<?=empty($name)?null:' - '.ucfirst($tName)?></title>
    <?php } ?>





    <?php if($target=='park'){
        $park = new PARKS();
        $desc = $park->info($name, 'desc');
        //$keyword = strip_tags($park->info($name, 'keyword'));
        //$cname = $PARK_PROPERITY[$name]['cName'];
        $about = strip_tags($park->info($name, 'about'));
        $park_basic_info = $PARKS->getParkInfo_by_Name($name);
    ?>
        <meta property="og:url" content="https://diy.ski/<?=$name?>" />
        <meta property="og:title" content="SKIDIY 自助滑雪 - <?=$park_basic_info['cname']?>(<?=ucfirst($name)?>) - <?=$desc?>" />
        <meta property="og:image" content="https://diy.ski/photos/<?=$name?>/<?=$name?>.jpg" />
        <meta property="og:description" content="<?=$about?>" />
        <meta property="fb:app_id" content="1475301989434574" />
        <meta property="og:type" content="website">
        <meta name="description" content="<?=$about?>" />
    <?php }else if($target=='instructor'){
        $instructor = new INSTRUCTORS();
        $about = strip_tags($instructor->info($name, 'about'));
    ?>
        <meta property="og:url" content="https://diy.ski/<?=$name?>" />
        <meta property="og:title" content="SKIDIY 自助滑雪 - <?=ucfirst($name)?> 教練" />
        <meta property="og:image" content="https://diy.ski/photos/<?=$name?>/<?=$name?>.jpg" />
        <meta property="og:image:width" content="300">
        <meta property="og:image:height" content="300">
        <meta property="og:description" content="<?=substr($about, 0, 360)?>" />
        <meta property="fb:app_id" content="1475301989434574" />
        <meta property="og:type" content="website">
        <meta name="description" content="<?=substr($about, 0, 360)?>" />
    <?php }else if($target=='index'){ ?>
        <meta property="og:url" content="https://diy.ski/" />
        <meta property="og:title" content="SKIDIY 自助滑雪" />
        <meta property="og:type" content="article"/>
        <meta property="og:image" content="https://diy.ski/assets/images/skidiy_logo_share.jpg?v200919" />
        <meta property="og:description" content="最豐富的自助滑雪資訊，包含日本12個知名滑雪勝地的雪場資訊、交通方式、住宿地點等等⋯⋯。讓您輕輕鬆鬆日本滑雪去。" />
        <meta property="fb:app_id" content="1475301989434574" />
        <meta name="description" content="最豐富的自助滑雪資訊，包含日本12個知名滑雪勝地的雪場資訊、交通方式、住宿地點等等⋯⋯。讓您輕輕鬆鬆日本滑雪去。" />
    <?php }else if($target=='instructors'){ ?>
        <meta property="og:url" content="https://diy.ski/instructorList.php" />
        <meta property="og:title" content="SKIDIY 自助滑雪 - 教練團隊" />
        <meta property="og:type" content="article"/>
        <meta property="og:image" content="https://diy.ski/assets/img/logo_256.png" />
        <meta property="og:description" content="最專業的教練團隊，每位教練都具有國際CASI滑雪教練執照，有系統的上課方式讓學習滑雪更輕鬆有效率。" />
        <meta property="fb:app_id" content="1475301989434574" />
        <meta name="description" content="最專業的教練團隊，每位教練都具有國際CASI滑雪教練執照，有系統的上課方式讓學習滑雪更輕鬆有效率。" />
    <?php }else if($target=='article'){ ?>
        <!--<meta property="og:url" content="https://diy.ski/articleList.php" />-->
        <meta property="og:title" content="SKIDIY 自助滑雪 - 相關文章" />
        <meta property="og:image" content="https://diy.ski/photos/naeba/3.jpg?v3" />
        <meta property="og:description" content="<?=$SEO_OG_DESC;?>" />
        <meta property="fb:app_id" content="1475301989434574" />
        <meta name="description" content="<?=$SEO_OG_DESC;?>" /> 
    <?php }else if($target=='schedule'){ ?>
        <meta property="og:url" content="https://diy.ski/schedule.php" />
        <meta property="og:title" content="SKIDIY 自助滑雪 - 預定課程" />
        <meta property="og:image" content="https://diy.ski/assets/images/logo-skidiy.png" />
        <meta property="og:description" content="<?=$SEO_OG_DESC;?>" />
        <meta property="fb:app_id" content="1475301989434574" />
        <meta name="description" content="最豐富的自助滑雪資訊，包含日本12個知名滑雪勝地的雪場資訊、交通方式、住宿地點等等⋯⋯。讓您輕輕鬆鬆日本滑雪去。" />              
    <?php }else if($target=='booking'){ ?>
        <meta property="og:url" content="https://diy.ski/schedule.php" />
        <meta property="og:title" content="SKIDIY 自助滑雪 - 預定課程" />
        <meta property="og:image" content="https://diy.ski/assets/images/logo-skidiy.png" />
        <meta property="og:description" content="<?=$SEO_OG_DESC;?>" />
        <meta property="fb:app_id" content="1475301989434574" />
        <meta name="description" content="最豐富的自助滑雪資訊，包含日本12個知名滑雪勝地的雪場資訊、交通方式、住宿地點等等⋯⋯。讓您輕輕鬆鬆日本滑雪去。" /> 
    <?php }else if($target=='payment'){ ?>
        <meta property="og:url" content="https://diy.ski/schedule.php" />
        <meta property="og:title" content="SKIDIY 自助滑雪 - 預定課程" />
        <meta property="og:image" content="https://diy.ski/assets/images/logo-skidiy.png" />
        <meta property="og:description" content="<?=$SEO_OG_DESC;?>" />
        <meta property="fb:app_id" content="1475301989434574" />
        <meta name="description" content="最豐富的自助滑雪資訊，包含日本12個知名滑雪勝地的雪場資訊、交通方式、住宿地點等等⋯⋯。讓您輕輕鬆鬆日本滑雪去。" />                 
    <?php }else if($target=='reservation'){ ?>
        <meta property="og:url" content="https://diy.ski/reservation" />
        <meta property="og:title" content="SKIDIY 自助滑雪 - 預約教練" />
        <meta property="og:image" content="https://diy.ski/assets/img/logo_256.png" />
        <meta property="og:description" content="最專業的教練團隊，每位教練都具有國際CASI滑雪教練執照，有系統的上課方式讓學習滑雪更輕鬆有效率。" />
        <meta property="fb:app_id" content="1475301989434574" />
        <meta name="description" content="最專業的教練團隊，每位教練都具有國際CASI滑雪教練執照，有系統的上課方式讓學習滑雪更輕鬆有效率。" />
    <?php } ?>



    <!-- Google Analytics-->
    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

      ga('create', 'UA-67749023-1', 'auto');
      ga('require','displayfeatures');
      ga('send', 'pageview');
    </script>
  