<?php

class ContentRepository
{
    private static $sectionLabels = array(
        'about'  => '介紹',
        'photo' => '照片',
        'location'  => '位置',
        'slope'  => '雪道',
        'ticket' => '雪票',
        'time' => '開放時間',
        'access' => '交通',
        'live'  => '住宿',
        'rental' => '租借',
        'delivery'  => '宅配',
        'luggage' => '行前裝備',
        'workout'  => '體能',
        'remind'  => '上課地點及事項',
        'join'  => '約伴及討論',
        'event'  => '優惠活動',
        'all'  => '完整閱讀'
    );

    private static $sectionFieldMapping = array(
        'about' => 'about',
        'photo' => 'photo_section',
        'location' => 'location_section',
        'slope' => 'slope_section',
        'ticket' => 'ticket_section',
        'time' => 'time_section',
        'access' => 'access_section',
        'live' => 'live_section',
        'rental' => 'rental_section',
        'delivery' => 'delivery_section',
        'luggage' => 'luggage_section',
        'workout' => 'workout_section',
        'remind' => 'remind_section',
        'join' => 'join_section',
        'event' => 'event_section'
    );

    private static $richRenderResorts = array('naeba', 'karuizawa', 'appi');
    private static $parkRedirects = array(
        'niseko' => 'https://diy.ski',
        'taipei' => 'https://diy.ski/iski'
    );
    private static $hiddenArticleIds = array(24, 25);

    // FAQ Data Layer: Centralized FAQ definitions by park
    private static $parkFAQs = array(
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
        ),
        '_default' => array(
            array(
                'q' => '如何預約中文教練？',
                'a' => '<p>透過 <a href="https://booking.diy.ski/schedule">SKIDIY 預訂系統</a> 就能直接挑選日期與教練，完成線上付款即可。</p>'
            ),
            array(
                'q' => '哪裡可以找到更多 FAQ？',
                'a' => '<p>可到 <a href="https://faq.diy.ski" target="_blank" rel="noopener">faq.diy.ski</a> 搜索所有常見問題，或使用頁面右下角的幫助入口。</p>'
            )
        )
    );

    public static function getParkRedirect($name)
    {
        $slug = strtolower($name);
        return isset(self::$parkRedirects[$slug]) ? self::$parkRedirects[$slug] : null;
    }

    public static function shouldHideArticle($idx)
    {
        return in_array(intval($idx), self::$hiddenArticleIds, true);
    }

    /**
     * Get FAQs for a specific park
     * Returns park-specific FAQs or default FAQs if park not found
     * @param string $name Park name/slug
     * @return array Array of FAQ items with 'q' (question) and 'a' (answer) keys
     */
    public static function getParkFAQs($name)
    {
        $slug = strtolower($name);
        if (isset(self::$parkFAQs[$slug])) {
            return self::$parkFAQs[$slug];
        }
        return self::$parkFAQs['_default'];
    }

    /**
     * Generate FAQPage schema.org JSON-LD for a park
     * @param string $parkName Park display name
     * @param string $parkUrl Canonical URL of the park page
     * @param array $faqs Array of FAQ items
     * @return string JSON-LD script tag content
     */
    public static function generateFAQSchema($parkName, $parkUrl, $faqs)
    {
        if (empty($faqs)) {
            return '';
        }

        $mainEntity = array();
        foreach ($faqs as $faq) {
            $mainEntity[] = array(
                '@type' => 'Question',
                'name' => strip_tags($faq['q']),
                'acceptedAnswer' => array(
                    '@type' => 'Answer',
                    'text' => strip_tags($faq['a'])
                )
            );
        }

        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $mainEntity
        );

        return json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    public static function getParkSectionsDefinition()
    {
        return self::$sectionLabels;
    }

    public static function getParkData($name)
    {
        $redirect = self::getParkRedirect($name);
        if ($redirect) {
            return array('redirect_to' => $redirect);
        }

        $PARKS = new PARKS();
        $park_info = $PARKS->getParkInfo_by_Name($name);
        if (empty($park_info)) {
            return null;
        }

        $displayName = !empty($park_info['cname']) ? $park_info['cname'] : ucfirst($name);
        $heroImage = self::resolveHeroImage($name, $park_info);

        $sections = array();
        foreach (self::$sectionLabels as $key => $label) {
            if ($key === 'all') {
                continue;
            }
            $field = isset(self::$sectionFieldMapping[$key]) ? self::$sectionFieldMapping[$key] : $key;
            $content = isset($park_info[$field]) ? trim($park_info[$field]) : '';
            if ($content === '') {
                continue;
            }
            $sections[] = array(
                'key' => $key,
                'title' => $label,
                'content' => $content,
                'render_mode' => in_array($name, self::$richRenderResorts) ? 'rich' : 'pre'
            );
        }

        $seoSnippet = self::buildSeoSnippet($park_info, $sections);

        return array(
            'slug' => $name,
            'display_name' => $displayName,
            'description' => isset($park_info['description']) ? $park_info['description'] : '',
            'hero_image' => $heroImage,
            'hero_pill' => 'Snow Resort Guide',
            'cta' => array(
                'label' => '現在就預訂',
                'target' => "schedule.php?f=p&p={$name}"
            ),
            'sections' => $sections,
            'seo' => array(
                'title' => $displayName . ' 滑雪場攻略 - SKIDIY',
                'description' => $seoSnippet,
                'image' => $heroImage
            ),
            'faq_keyword' => $displayName,
            'related_links' => array(
                'faq_url' => 'https://faq.diy.ski/?q=' . urlencode($displayName),
                'booking_url' => 'https://booking.diy.ski/schedule?park=' . urlencode($name)
            ),
            'raw' => $park_info
        );
    }

    public static function getArticleData($idx)
    {
        $ARTICLE = new ARTICLE();
        $article_data = $ARTICLE->readByIdx($idx);
        if (empty($article_data)) {
            return null;
        }

        $content = normalize_rich_text($article_data['article']);
        $heroImage = self::resolveArticleHero($idx, $article_data);
        $snippet = self::truncateText(strip_tags($content), 180);

        return array(
            'idx' => $idx,
            'title' => $article_data['title'],
            'content' => $content,
            'hero_image' => $heroImage,
            'seo' => array(
                'title' => $article_data['title'] . ' - SKIDIY 滑雪攻略',
                'description' => $snippet,
                'image' => $heroImage
            ),
            'cta' => array(
                'label' => '現在就預訂',
                'target' => '../schedule.php?f=a'
            ),
            'raw' => $article_data
        );
    }

    private static function resolveHeroImage($name, $park_info)
    {
        if (!empty($park_info['photo'])) {
            return $park_info['photo'];
        }

        $overrides = array(
            'karuizawa' => 'https://diy.ski/photos/karuizawa/course1.jpg',
            'naeba' => 'https://diy.ski/photos/naeba/3.jpg?v3',
            'appi' => 'https://diy.ski/photos/appi/appi.jpg'
        );

        if (isset($overrides[$name])) {
            return $overrides[$name];
        }

        return 'https://diy.ski/photos/' . $name . '/3.jpg';
    }

    private static function resolveArticleHero($idx, $article_data)
    {
        if (!empty($article_data['hero_image'])) {
            return $article_data['hero_image'];
        }
        if (!empty($idx)) {
            return "https://diy.ski/photos/articles/{$idx}/{$idx}.jpg?v221008";
        }
        return 'https://diy.ski/assets/images/header_index_main_img.png';
    }

    private static function buildSeoSnippet($park_info, $sections)
    {
        $source = '';
        if (!empty($park_info['about'])) {
            $source = $park_info['about'];
        } elseif (!empty($park_info['description'])) {
            $source = $park_info['description'];
        } elseif (!empty($sections)) {
            $source = $sections[0]['content'];
        }

        $plain = strip_tags(normalize_rich_text($source));
        return self::truncateText($plain, 140);
    }

    private static function truncateText($text, $limit)
    {
        $text = trim(preg_replace('/\s+/', ' ', $text));
        if (function_exists('mb_strlen')) {
            if (mb_strlen($text) <= $limit) {
                return $text;
            }
            return mb_substr($text, 0, $limit) . '…';
        } else {
            if (strlen($text) <= $limit) {
                return $text;
            }
            return substr($text, 0, $limit) . '…';
        }
    }
}
