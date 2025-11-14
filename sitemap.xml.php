<?php
/**
 * Production Sitemap Generator
 *
 * Generates sitemap.xml with proper priority and changefreq for SEO
 * URL: /sitemap.xml.php (accessible as /sitemap.xml with mod_rewrite)
 *
 * Priority levels:
 * - Homepage: 1.0
 * - List pages: 0.9
 * - Park pages: 0.8
 * - Article pages: 0.7
 * - Other pages: 0.5
 *
 * Change frequency:
 * - Homepage: weekly
 * - List pages: weekly
 * - Park pages: monthly
 * - Article pages: monthly
 * - Other pages: yearly
 */

require_once __DIR__ . '/includes/sdk.php';

header('Content-Type: application/xml; charset=utf-8');

$baseUrl = 'https://' . domain_name;
$lastMod = date('Y-m-d');
$urls = array();

// 1. Homepage - Highest Priority
$urls[] = array(
    'loc' => $baseUrl . '/',
    'lastmod' => $lastMod,
    'changefreq' => 'weekly',
    'priority' => 1.0
);

// 2. List Pages - High Priority
$listPages = array(
    '/parkList.php' => 0.9,
    '/articleList.php' => 0.9,
    '/instructorList.php' => 0.6,  // Lower priority for non-core pages
    '/schedule.php' => 0.5,         // User action page, lower priority
);
foreach ($listPages as $path => $priority) {
    $urls[] = array(
        'loc' => $baseUrl . $path,
        'lastmod' => $lastMod,
        'changefreq' => 'weekly',
        'priority' => $priority
    );
}

// 3. Park Pages - Medium Priority (most important content)
$PARKS = new PARKS();
$parkList = $PARKS->listing();
foreach ($parkList as $park) {
    if (empty($park['name'])) { continue; }

    // Skip hidden redirects
    $redirect = ContentRepository::getParkRedirect($park['name']);
    if ($redirect) { continue; }

    $urls[] = array(
        'loc' => $baseUrl . '/park.php?name=' . urlencode($park['name']),
        'lastmod' => $lastMod,
        'changefreq' => 'monthly',
        'priority' => 0.8
    );
}

// 4. Article Pages - Medium-Low Priority
$ARTICLE = new ARTICLE();
$articleList = $ARTICLE->listing();
foreach ($articleList as $article) {
    if (empty($article['idx'])) { continue; }

    // Skip hidden articles
    if (ContentRepository::shouldHideArticle($article['idx'])) { continue; }

    $urls[] = array(
        'loc' => $baseUrl . '/article.php?idx=' . intval($article['idx']),
        'lastmod' => !empty($article['timestamp']) ? date('Y-m-d', strtotime($article['timestamp'])) : $lastMod,
        'changefreq' => 'monthly',
        'priority' => 0.7
    );
}

// Output XML
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
echo "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";

foreach ($urls as $urlData) {
    $escaped = htmlspecialchars($urlData['loc'], ENT_QUOTES, 'UTF-8');
    echo "  <url>\n";
    echo "    <loc>{$escaped}</loc>\n";
    echo "    <lastmod>{$urlData['lastmod']}</lastmod>\n";
    echo "    <changefreq>{$urlData['changefreq']}</changefreq>\n";
    echo "    <priority>{$urlData['priority']}</priority>\n";
    echo "  </url>\n";
}

echo "</urlset>";
?>
