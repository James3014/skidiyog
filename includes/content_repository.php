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

    public static function getParkRedirect($name)
    {
        $slug = strtolower($name);
        return isset(self::$parkRedirects[$slug]) ? self::$parkRedirects[$slug] : null;
    }

    public static function shouldHideArticle($idx)
    {
        return in_array(intval($idx), self::$hiddenArticleIds, true);
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
