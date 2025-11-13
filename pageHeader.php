  <?php

    if(strstr($_SERVER['SCRIPT_NAME'],'article'))  $target = 'article';
    if(strstr($_SERVER['SCRIPT_NAME'],'schedule')) $target = 'schedule';
    if(strstr($_SERVER['SCRIPT_NAME'],'booking'))  $target = 'booking';
    if(strstr($_SERVER['SCRIPT_NAME'],'payment'))  $target = 'payment';
    if(empty($target)) $target = 'index';
    //$target = 'index';
    if(empty($SEO_OG_DESC)) $SEO_OG_DESC="最豐富的自助滑雪資訊，包含日本12個知名滑雪勝地的雪場資訊、交通方式、住宿地點等等⋯⋯。讓您輕輕鬆鬆日本滑雪去。" ;
    $metaTitleOverride = isset($SEO_TITLE) ? trim($SEO_TITLE) : '';
    $metaDescriptionOverride = isset($SEO_DESCRIPTION) ? trim($SEO_DESCRIPTION) : '';
    $metaImageOverride = isset($SEO_OG_IMAGE) ? trim($SEO_OG_IMAGE) : '';
  ?>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

<!--  add by mj -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover"/>
    <?php if(defined('SKID_PREVIEW_MODE') && SKID_PREVIEW_MODE){ ?>
    <meta name="robots" content="noindex, nofollow">
    <?php } ?>
    <!--Import Google Icon Font-->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!--Import materialize.css-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-rc.2/css/materialize.min.css">
    <!--Import custom.css-->
    <link rel="stylesheet" href="/assets/css/custom.min.css">
    <!--  add by mauji for over write some css style -->
    <link rel="stylesheet" href="/assets/css/workaround.css">
    <!--Import jQuery-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> 



    <meta name="B-verify" content="69017dc01d91abdba660cee1095ced1e042497f9" />
    <link rel="shortcut icon" href="/assets/images/favicon_skidiy.png" type="image/png" />

    <?php if(!empty($metaTitleOverride)){ ?>
        <title><?=htmlspecialchars($metaTitleOverride, ENT_QUOTES, 'UTF-8');?></title>
    <?php }elseif(isset($name) && $name=='virtual'){ ?>
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
        $defaultOgTitle = "SKIDIY 自助滑雪 - {$park_basic_info['cname']} (" . ucfirst($name) . ") - {$desc}";
        $resolvedOgTitle = !empty($metaTitleOverride) ? $metaTitleOverride : $defaultOgTitle;
        $defaultDescription = $about;
        $resolvedDescription = !empty($metaDescriptionOverride) ? $metaDescriptionOverride : $defaultDescription;
        $defaultImage = "https://diy.ski/photos/{$name}/{$name}.jpg";
        $resolvedImage = !empty($metaImageOverride) ? $metaImageOverride : $defaultImage;
        $canonicalUrl = "https://diy.ski/{$name}";
    ?>
        <link rel="canonical" href="<?=$canonicalUrl?>" />
        <meta property="og:url" content="<?=$canonicalUrl?>" />
        <meta property="og:title" content="<?=$resolvedOgTitle?>" />
        <meta property="og:image" content="<?=$resolvedImage?>" />
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:image:alt" content="<?=$park_basic_info['cname']?> 滑雪場">
        <meta property="og:description" content="<?=$resolvedDescription?>" />
        <meta property="og:type" content="website">
        <meta property="og:locale" content="zh_TW">
        <meta property="og:locale:alternate" content="en_US">
        <meta property="fb:app_id" content="1475301989434574" />
        <meta name="description" content="<?=$resolvedDescription?>" />
        <link rel="alternate" hreflang="zh-TW" href="https://diy.ski/<?=$name?>" />
        <link rel="alternate" hreflang="en" href="https://diy.ski/en/<?=$name?>" />
    <?php }else if($target=='instructor'){
        $instructor = new INSTRUCTORS();
        $about = strip_tags($instructor->info($name, 'about'));
        $resolvedDescription = !empty($metaDescriptionOverride) ? $metaDescriptionOverride : substr($about, 0, 360);
        $resolvedImage = !empty($metaImageOverride) ? $metaImageOverride : "https://diy.ski/photos/{$name}/{$name}.jpg";
        $resolvedOgTitle = !empty($metaTitleOverride) ? $metaTitleOverride : "SKIDIY 自助滑雪 - ".ucfirst($name)." 教練";
        $canonicalUrl = "https://diy.ski/{$name}";
    ?>
        <link rel="canonical" href="<?=$canonicalUrl?>" />
        <meta property="og:url" content="<?=$canonicalUrl?>" />
        <meta property="og:title" content="<?=$resolvedOgTitle?>" />
        <meta property="og:image" content="<?=$resolvedImage?>" />
        <meta property="og:image:width" content="300">
        <meta property="og:image:height" content="300">
        <meta property="og:image:alt" content="<?=ucfirst($name)?> - SKIDIY 滑雪教練">
        <meta property="og:description" content="<?=$resolvedDescription?>" />
        <meta property="og:type" content="website">
        <meta property="og:locale" content="zh_TW">
        <meta property="og:locale:alternate" content="en_US">
        <meta property="fb:app_id" content="1475301989434574" />
        <meta name="description" content="<?=$resolvedDescription?>" />
        <link rel="alternate" hreflang="zh-TW" href="https://diy.ski/<?=$name?>" />
        <link rel="alternate" hreflang="en" href="https://diy.ski/en/<?=$name?>" />
    <?php }else if($target=='index'){ ?>
        <link rel="canonical" href="https://diy.ski/" />
        <meta property="og:url" content="https://diy.ski/" />
        <meta property="og:title" content="<?=!empty($metaTitleOverride)?$metaTitleOverride:'SKIDIY 自助滑雪'?>" />
        <meta property="og:type" content="website"/>
        <meta property="og:image" content="<?=!empty($metaImageOverride)?$metaImageOverride:'https://diy.ski/assets/images/skidiy_logo_share.jpg?v200919'?>" />
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:image:alt" content="SKIDIY 自助滑雪">
        <meta property="og:description" content="<?=!empty($metaDescriptionOverride)?$metaDescriptionOverride:'最豐富的自助滑雪資訊，包含日本12個知名滑雪勝地的雪場資訊、交通方式、住宿地點等等⋯⋯。讓您輕輕鬆鬆日本滑雪去。'?>" />
        <meta property="og:locale" content="zh_TW">
        <meta property="og:locale:alternate" content="en_US">
        <meta property="fb:app_id" content="1475301989434574" />
        <meta name="description" content="<?=!empty($metaDescriptionOverride)?$metaDescriptionOverride:'最豐富的自助滑雪資訊，包含日本12個知名滑雪勝地的雪場資訊、交通方式、住宿地點等等⋯⋯。讓您輕輕鬆鬆日本滑雪去。'?>" />
        <link rel="alternate" hreflang="zh-TW" href="https://diy.ski/" />
        <link rel="alternate" hreflang="en" href="https://diy.ski/en/" />
    <?php }else if($target=='instructors'){ ?>
        <link rel="canonical" href="https://diy.ski/instructorList.php" />
        <meta property="og:url" content="https://diy.ski/instructorList.php" />
        <meta property="og:title" content="<?=!empty($metaTitleOverride)?$metaTitleOverride:'SKIDIY 自助滑雪 - 教練團隊'?>" />
        <meta property="og:type" content="website"/>
        <meta property="og:image" content="<?=!empty($metaImageOverride)?$metaImageOverride:'https://diy.ski/assets/img/logo_256.png'?>" />
        <meta property="og:image:width" content="256">
        <meta property="og:image:height" content="256">
        <meta property="og:image:alt" content="SKIDIY 滑雪教練團隊">
        <meta property="og:description" content="<?=!empty($metaDescriptionOverride)?$metaDescriptionOverride:'最專業的教練團隊，每位教練都具有國際CASI滑雪教練執照，有系統的上課方式讓學習滑雪更輕鬆有效率。'?>" />
        <meta property="og:locale" content="zh_TW">
        <meta property="og:locale:alternate" content="en_US">
        <meta property="fb:app_id" content="1475301989434574" />
        <meta name="description" content="<?=!empty($metaDescriptionOverride)?$metaDescriptionOverride:'最專業的教練團隊，每位教練都具有國際CASI滑雪教練執照，有系統的上課方式讓學習滑雪更輕鬆有效率。'?>" />
        <link rel="alternate" hreflang="zh-TW" href="https://diy.ski/instructorList.php" />
        <link rel="alternate" hreflang="en" href="https://diy.ski/en/instructorList.php" />
    <?php }else if($target=='article'){
        $canonicalUrl = !empty($article_id) ? "https://diy.ski/article.php?idx={$article_id}" : "https://diy.ski/articleList.php";
    ?>
        <link rel="canonical" href="<?=$canonicalUrl?>" />
        <meta property="og:url" content="<?=$canonicalUrl?>" />
        <meta property="og:title" content="<?=!empty($metaTitleOverride)?$metaTitleOverride:'SKIDIY 自助滑雪 - 相關文章'?>" />
        <meta property="og:type" content="article"/>
        <meta property="og:image" content="<?=!empty($metaImageOverride)?$metaImageOverride:'https://diy.ski/photos/naeba/3.jpg?v3'?>" />
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:image:alt" content="SKIDIY 滑雪相關文章">
        <meta property="og:description" content="<?=!empty($metaDescriptionOverride)?$metaDescriptionOverride:$SEO_OG_DESC;?>" />
        <meta property="og:locale" content="zh_TW">
        <meta property="og:locale:alternate" content="en_US">
        <meta property="fb:app_id" content="1475301989434574" />
        <meta name="description" content="<?=!empty($metaDescriptionOverride)?$metaDescriptionOverride:$SEO_OG_DESC;?>" />
        <link rel="alternate" hreflang="zh-TW" href="<?=$canonicalUrl?>" />
        <link rel="alternate" hreflang="en" href="https://diy.ski/en/article.php?idx=<?=!empty($article_id)?$article_id:''?>" /> 
    <?php }else if($target=='schedule'){ ?>
        <link rel="canonical" href="https://diy.ski/schedule.php" />
        <meta property="og:url" content="https://diy.ski/schedule.php" />
        <meta property="og:title" content="<?=!empty($metaTitleOverride)?$metaTitleOverride:'SKIDIY 自助滑雪 - 預定課程'?>" />
        <meta property="og:type" content="website"/>
        <meta property="og:image" content="<?=!empty($metaImageOverride)?$metaImageOverride:'https://diy.ski/assets/images/logo-skidiy.png'?>" />
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:image:alt" content="SKIDIY 預定課程">
        <meta property="og:description" content="<?=!empty($metaDescriptionOverride)?$metaDescriptionOverride:$SEO_OG_DESC;?>" />
        <meta property="og:locale" content="zh_TW">
        <meta property="og:locale:alternate" content="en_US">
        <meta property="fb:app_id" content="1475301989434574" />
        <meta name="description" content="<?=!empty($metaDescriptionOverride)?$metaDescriptionOverride:'最豐富的自助滑雪資訊，包含日本12個知名滑雪勝地的雪場資訊、交通方式、住宿地點等等⋯⋯。讓您輕輕鬆鬆日本滑雪去。'?>" />
        <link rel="alternate" hreflang="zh-TW" href="https://diy.ski/schedule.php" />
        <link rel="alternate" hreflang="en" href="https://diy.ski/en/schedule.php" />              
    <?php }else if($target=='booking'){ ?>
        <link rel="canonical" href="https://diy.ski/booking.php" />
        <meta property="og:url" content="https://diy.ski/booking.php" />
        <meta property="og:title" content="<?=!empty($metaTitleOverride)?$metaTitleOverride:'SKIDIY 自助滑雪 - 預定課程'?>" />
        <meta property="og:type" content="website"/>
        <meta property="og:image" content="<?=!empty($metaImageOverride)?$metaImageOverride:'https://diy.ski/assets/images/logo-skidiy.png'?>" />
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:image:alt" content="SKIDIY 預定課程">
        <meta property="og:description" content="<?=!empty($metaDescriptionOverride)?$metaDescriptionOverride:$SEO_OG_DESC;?>" />
        <meta property="og:locale" content="zh_TW">
        <meta property="og:locale:alternate" content="en_US">
        <meta property="fb:app_id" content="1475301989434574" />
        <meta name="description" content="<?=!empty($metaDescriptionOverride)?$metaDescriptionOverride:'最豐富的自助滑雪資訊，包含日本12個知名滑雪勝地的雪場資訊、交通方式、住宿地點等等⋯⋯。讓您輕輕鬆鬆日本滑雪去。'?>" />
        <link rel="alternate" hreflang="zh-TW" href="https://diy.ski/booking.php" />
        <link rel="alternate" hreflang="en" href="https://diy.ski/en/booking.php" /> 
    <?php }else if($target=='payment'){ ?>
        <link rel="canonical" href="https://diy.ski/payment.php" />
        <meta property="og:url" content="https://diy.ski/payment.php" />
        <meta property="og:title" content="<?=!empty($metaTitleOverride)?$metaTitleOverride:'SKIDIY 自助滑雪 - 付款'?>" />
        <meta property="og:type" content="website"/>
        <meta property="og:image" content="<?=!empty($metaImageOverride)?$metaImageOverride:'https://diy.ski/assets/images/logo-skidiy.png'?>" />
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:image:alt" content="SKIDIY 課程付款">
        <meta property="og:description" content="<?=!empty($metaDescriptionOverride)?$metaDescriptionOverride:$SEO_OG_DESC;?>" />
        <meta property="og:locale" content="zh_TW">
        <meta property="og:locale:alternate" content="en_US">
        <meta property="fb:app_id" content="1475301989434574" />
        <meta name="description" content="<?=!empty($metaDescriptionOverride)?$metaDescriptionOverride:'最豐富的自助滑雪資訊，包含日本12個知名滑雪勝地的雪場資訊、交通方式、住宿地點等等⋯⋯。讓您輕輕鬆鬆日本滑雪去。'?>" />
        <link rel="alternate" hreflang="zh-TW" href="https://diy.ski/payment.php" />
        <link rel="alternate" hreflang="en" href="https://diy.ski/en/payment.php" />                 
    <?php }else if($target=='reservation'){ ?>
        <link rel="canonical" href="https://diy.ski/reservation/" />
        <meta property="og:url" content="https://diy.ski/reservation/" />
        <meta property="og:title" content="<?=!empty($metaTitleOverride)?$metaTitleOverride:'SKIDIY 自助滑雪 - 預約教練'?>" />
        <meta property="og:type" content="website"/>
        <meta property="og:image" content="<?=!empty($metaImageOverride)?$metaImageOverride:'https://diy.ski/assets/img/logo_256.png'?>" />
        <meta property="og:image:width" content="256">
        <meta property="og:image:height" content="256">
        <meta property="og:image:alt" content="SKIDIY 預約教練">
        <meta property="og:description" content="<?=!empty($metaDescriptionOverride)?$metaDescriptionOverride:'最專業的教練團隊，每位教練都具有國際CASI滑雪教練執照，有系統的上課方式讓學習滑雪更輕鬆有效率。'?>" />
        <meta property="og:locale" content="zh_TW">
        <meta property="og:locale:alternate" content="en_US">
        <meta property="fb:app_id" content="1475301989434574" />
        <meta name="description" content="<?=!empty($metaDescriptionOverride)?$metaDescriptionOverride:'最專業的教練團隊，每位教練都具有國際CASI滑雪教練執照，有系統的上課方式讓學習滑雪更輕鬆有效率。'?>" />
        <link rel="alternate" hreflang="zh-TW" href="https://diy.ski/reservation/" />
        <link rel="alternate" hreflang="en" href="https://diy.ski/en/reservation/" />
    <?php } ?>



    <?php if(!defined('SKID_STATIC_RENDER_MODE') || !SKID_STATIC_RENDER_MODE){ ?>
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
    <?php } ?>
  
