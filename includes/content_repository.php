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
    // 硬編碼常見公園，其他公園從 API 動態取得
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
        // 預設 FAQ：適用於所有未特別定義的公園（包括新加的 12 個）
        '_default' => array(
            array(
                'q' => '如何預約中文教練？',
                'a' => '<p>透過 <a href="https://booking.diy.ski/schedule">SKIDIY 預訂系統</a> 就能直接挑選日期與教練，完成線上付款即可。</p>'
            ),
            array(
                'q' => '哪裡可以找到更多 FAQ？',
                'a' => '<p>可到 <a href="https://faq.diy.ski" target="_blank" rel="noopener">faq.diy.ski</a> 搜索所有常見問題，或使用頁面右下角的幫助入口。</p>'
            ),
            array(
                'q' => '有哪些交通方式前往滑雪場？',
                'a' => '<p>大多數日本滑雪場可透過新幹線或電車到達，詳細的交通資訊請查看本頁面的「交通」段落。</p>'
            ),
            array(
                'q' => '可以租借滑雪裝備嗎？',
                'a' => '<p>大多數滑雪場都提供滑雪板、靴子、雪杖等裝備租借服務，詳情請參閱本頁「租借」段落或 <a href="https://booking.diy.ski/schedule">預訂系統</a>。</p>'
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
     *
     * 策略：
     * 1. 優先返回硬編碼的公園特定 FAQ（naeba, karuizawa）
     * 2. 如果沒有硬編碼，嘗試從 FAQ API 取得公園相關 FAQ
     * 3. 如果 API 失敗或無結果，回傳預設 FAQ
     *
     * @param string $name Park name/slug
     * @return array Array of FAQ items with 'q' (question) and 'a' (answer) keys
     */
    public static function getParkFAQs($name)
    {
        $slug = strtolower($name);

        // 優先級 1：硬編碼的特定公園 FAQ
        if (isset(self::$parkFAQs[$slug])) {
            return self::$parkFAQs[$slug];
        }

        // 優先級 2：嘗試從 API 取得公園相關 FAQ（適用於新雪場）
        try {
            $apiFAQs = self::getAPIParkFAQs($name);
            if (!empty($apiFAQs)) {
                return $apiFAQs;
            }
        } catch (Exception $e) {
            error_log('[ContentRepository] Failed to fetch API FAQs for park ' . $name . ': ' . $e->getMessage());
        }

        // 優先級 3：預設 FAQ
        return self::$parkFAQs['_default'];
    }

    /**
     * 從 FAQ API 取得公園相關 FAQ
     * 根據公園名稱搜尋相關 FAQ 項目
     * @param string $parkName Park name/slug
     * @return array 相關 FAQ 項目（轉換為 ['q' => ..., 'a' => ...] 格式）
     */
    private static function getAPIParkFAQs($parkName)
    {
        try {
            // 取得 API 資料
            $faqData = self::fetchFAQData();
            if (empty($faqData) || empty($faqData['items'])) {
                return array();
            }

            $parkNameLower = strtolower($parkName);
            $relatedFAQs = array();

            // 按 crm_tags 搜尋相關 FAQ（針對特定公園的標籤）
            foreach ($faqData['items'] as $faq) {
                $faqTags = isset($faq['metadata']['crm_tags']) ? $faq['metadata']['crm_tags'] : array();

                // 檢查是否有公園名稱相關標籤
                foreach ($faqTags as $tag) {
                    $tagClean = strtolower(str_replace('#', '', $tag));
                    if (strpos($tagClean, $parkNameLower) !== false || strpos($parkNameLower, $tagClean) !== false) {
                        // 轉換為 ['q' => ..., 'a' => ...] 格式
                        $relatedFAQs[] = array(
                            'q' => isset($faq['content']['question']) ? $faq['content']['question'] : '',
                            'a' => '<p>' . (isset($faq['content']['answer']) ? $faq['content']['answer'] : '') . '</p>'
                        );
                        break;
                    }
                }

                // 最多 5 個相關 FAQ
                if (count($relatedFAQs) >= 5) {
                    break;
                }
            }

            return $relatedFAQs;

        } catch (Exception $e) {
            error_log('[ContentRepository] Error fetching API Park FAQs: ' . $e->getMessage());
            return array();
        }
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

    /**
     * Generate BreadcrumbList schema.org JSON-LD for navigation breadcrumbs
     * @param array $breadcrumbs Array of breadcrumb items with 'name' and 'url' keys
     * @return array Schema.org BreadcrumbList structure
     */
    public static function generateBreadcrumbSchema($breadcrumbs)
    {
        if (empty($breadcrumbs)) {
            return null;
        }

        $itemListElement = array();
        foreach ($breadcrumbs as $index => $crumb) {
            $itemListElement[] = array(
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $crumb['name'],
                'item' => $crumb['url']
            );
        }

        return array(
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $itemListElement
        );
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
        // Optimized for Google SERP: 120-155 characters
        $snippet = self::truncateText(strip_tags($content), 155);

        // 【新增】根據文章 tags 自動抓取相關 FAQ
        $relatedFAQs = array();
        if (!empty($article_data['tags'])) {
            $relatedFAQs = self::getRelatedFAQsByTags($article_data['tags']);
        }

        return array(
            'idx' => $idx,
            'title' => $article_data['title'],
            'content' => $content,
            'hero_image' => $heroImage,
            'related_faqs' => $relatedFAQs,  // 【新增】相關 FAQ 清單
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

    /**
     * 【新增】根據文章的 tags 查詢相關 FAQ
     *
     * @param string $tagsString 文章 tags（逗號或#分隔）
     * @return array 相關 FAQ 項目清單
     *
     * 實現邏輯：
     * 1. 解析文章 tags（支援 #tag 和 tag 格式）
     * 2. 呼叫 FAQ API 取得所有 FAQ
     * 3. 過濾：找出 crm_tags 與文章 tags 有交集的 FAQ
     * 4. 最多回傳 5 個相關 FAQ
     */
    private static function getRelatedFAQsByTags($tagsString)
    {
        // 解析文章 tags（支援多種格式）
        $articleTags = self::parseTags($tagsString);
        if (empty($articleTags)) {
            error_log('[ContentRepository] No article tags parsed from: ' . $tagsString);
            return array();
        }

        try {
            // 調用 FAQ API 取得所有 FAQ 資料
            $faqData = self::fetchFAQData();
            if (empty($faqData) || empty($faqData['items'])) {
                error_log('[ContentRepository] No FAQ data or items found');
                return array();
            }

            error_log('[ContentRepository] Parsed article tags: ' . json_encode($articleTags));

            $relatedFAQs = array();

            // 過濾：找出相關 FAQ
            $matchCount = 0;
            foreach ($faqData['items'] as $faq) {
                $faqTags = isset($faq['metadata']['crm_tags']) ? $faq['metadata']['crm_tags'] : array();

                // 檢查是否有標籤交集
                $hasMatch = false;
                foreach ($articleTags as $articleTag) {
                    foreach ($faqTags as $faqTag) {
                        // 移除 # 符號做比較
                        $articleTagClean = str_replace('#', '', $articleTag);
                        $faqTagClean = str_replace('#', '', $faqTag);

                        if (strcasecmp($articleTagClean, $faqTagClean) === 0) {
                            $hasMatch = true;
                            $matchCount++;
                            break 2;
                        }
                    }
                }

                if ($hasMatch) {
                    $relatedFAQs[] = array(
                        'id' => isset($faq['id']) ? $faq['id'] : '',
                        'question' => isset($faq['content']['question']) ? $faq['content']['question'] : '',
                        'answer_preview' => isset($faq['content']['answer']) ? self::truncateText($faq['content']['answer'], 150) : '',
                        'intent' => isset($faq['metadata']['intent']) ? $faq['metadata']['intent'] : '',
                        'tags' => isset($faq['metadata']['crm_tags']) ? $faq['metadata']['crm_tags'] : array()
                    );
                }

                // 最多 5 個相關 FAQ
                if (count($relatedFAQs) >= 5) {
                    break;
                }
            }

            error_log('[ContentRepository] Found ' . count($relatedFAQs) . ' related FAQs for tags: ' . json_encode($articleTags));
            return $relatedFAQs;

        } catch (Exception $e) {
            error_log('[ContentRepository] Failed to fetch related FAQs: ' . $e->getMessage());
            return array();
        }
    }

    /**
     * 【新增】解析文章 tags 字串
     *
     * 支援格式：
     * - "#tag1,#tag2,#tag3" (帶 # 符號)
     * - "tag1,tag2,tag3" (不帶 # 符號)
     * - "tag1 tag2 tag3" (空格分隔)
     *
     * @param string $tagsString 原始 tags 字串
     * @return array 清理後的 tags 陣列
     */
    private static function parseTags($tagsString)
    {
        if (empty($tagsString)) {
            return array();
        }

        // 支援多種分隔符：逗號、空格、句號
        $tags = preg_split('/[,\s\|]+/', $tagsString, -1, PREG_SPLIT_NO_EMPTY);

        // 清理每個 tag：移除空白、轉換為小寫、保留 #
        $cleanTags = array();
        foreach ($tags as $tag) {
            $tag = trim($tag);
            if (!empty($tag)) {
                // 確保都有 # 前綴（統一格式）
                if (strpos($tag, '#') !== 0) {
                    $tag = '#' . $tag;
                }
                $cleanTags[] = strtolower($tag);
            }
        }

        return array_unique($cleanTags);
    }

    /**
     * 【新增】從 FAQ API 取得所有 FAQ 資料（帶快取）
     *
     * 快取策略：5 分鐘內重複請求使用快取
     *
     * @return array FAQ 資料陣列
     */
    private static function fetchFAQData()
    {
        // 快取鍵名
        $cacheKey = 'faq_data_cache_zh';

        // 檢查是否有快取（5 分鐘）
        if (isset($GLOBALS[$cacheKey]) && isset($GLOBALS[$cacheKey . '_time'])) {
            if ((time() - $GLOBALS[$cacheKey . '_time']) < 300) {
                return $GLOBALS[$cacheKey];
            }
        }

        // 從 FAQ API 取得資料
        $apiUrl = 'https://faq-api-v1.zeabur.app/api/v1/faq/all?lang=zh';

        $options = array(
            'http' => array(
                'method' => 'GET',
                'timeout' => 5,  // 5 秒超時
                'user_agent' => 'SKIDIY Content Repository/1.0'
            )
        );

        $context = stream_context_create($options);
        $response = @file_get_contents($apiUrl, false, $context);

        if ($response === false) {
            throw new Exception('Failed to connect to FAQ API');
        }

        $jsonData = json_decode($response, true);
        if (empty($jsonData) || !isset($jsonData['data']['items'])) {
            throw new Exception('Invalid FAQ API response structure');
        }

        // 存入快取
        $GLOBALS[$cacheKey] = $jsonData['data'];
        $GLOBALS[$cacheKey . '_time'] = time();

        return $jsonData['data'];
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
        // Optimized for Google SERP: 120-155 characters (for CJK languages like Chinese)
        // Google typically shows 155-160 chars on desktop, 120 on mobile
        return self::truncateText($plain, 155);
    }

    /**
     * Truncate text to specified length while preserving word boundaries
     * Optimized for multi-byte characters (UTF-8, CJK)
     * @param string $text Text to truncate
     * @param int $limit Character limit (for CJK: each character counts as 1)
     * @return string Truncated text with ellipsis if exceeded
     */
    private static function truncateText($text, $limit)
    {
        $text = trim(preg_replace('/\s+/', ' ', $text));
        if (function_exists('mb_strlen')) {
            $length = mb_strlen($text);
            if ($length <= $limit) {
                return $text;
            }
            // Truncate and add ellipsis
            $truncated = mb_substr($text, 0, $limit);
            // Try to cut at word boundary (space) within last 10 chars
            $lastSpace = mb_strrpos($truncated, ' ');
            if ($lastSpace !== false && $lastSpace > $limit - 10) {
                $truncated = mb_substr($truncated, 0, $lastSpace);
            }
            return trim($truncated) . '…';
        } else {
            if (strlen($text) <= $limit) {
                return $text;
            }
            return substr($text, 0, $limit) . '…';
        }
    }
}
