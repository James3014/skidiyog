<?php
function getParkFAQs($parkName){
    $key = strtolower($parkName);
    $faqs = array(
        'naeba' => array(
            array(
                'q' => '苗場適合初學者嗎？',
                'a' => '<p>苗場擁有專門的 beginner zone 以及中文教練團隊，初學者可以用 <a href="https://booking.diy.ski/schedule?park=naeba">線上課表</a> 直接預約。</p>'
            ),
            array(
                'q' => '如何前往苗場？',
                'a' => '<p>最方便的方式是搭乘上越新幹線到越後湯澤站，再轉飯店巴士。交通細節都整理在 <a href="/park.php?name=naeba#access">交通段落</a>。</p>'
            )
        ),
        'karuizawa' => array(
            array(
                'q' => '輕井澤是否適合親子？',
                'a' => '<p>輕井澤擁有 Prince Snow Resorts 的家庭區域，課程與租借裝備都很友善，課程可在 <a href="https://booking.diy.ski/schedule?park=karuizawa">課表</a> 預約。</p>'
            ),
            array(
                'q' => '輕井澤有哪些租借選項？',
                'a' => '<p>官方租借中心提供全套裝備，也能在 <a href="https://diy.ski/articles.php">文章專區</a> 看裝備攻略。</p>'
            )
        )
    );
    if(isset($faqs[$key])){
        return $faqs[$key];
    }
    return array(
        array(
            'q' => '如何預約中文教練？',
            'a' => '<p>透過 <a href="https://booking.diy.ski/schedule">SKIDIY 預訂系統</a> 就能直接挑選日期與教練，完成線上付款即可。</p>'
        ),
        array(
            'q' => '哪裡可以找到更多 FAQ？',
            'a' => '<p>可到 <a href="https://faq.diy.ski" target="_blank" rel="noopener">faq.diy.ski</a> 搜索所有常見問題，或使用頁面右下角的幫助入口。</p>'
        )
    );
}
