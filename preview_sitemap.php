<?php
require_once __DIR__ . '/includes/sdk.php';

header('Content-Type: application/xml; charset=utf-8');

$baseUrl = 'https://' . domain_name;
$lastMod = date('Y-m-d');

$urls = array();
$staticPaths = array(
    '/',
    '/parkList.php',
    '/articleList.php',
    '/instructorList.php',
    '/schedule.php',
    '/reservation.php'
);
foreach ($staticPaths as $path) {
    $urls[] = $baseUrl . $path;
}

$PARKS = new PARKS();
$parkList = $PARKS->listing();
foreach ($parkList as $park) {
    if (empty($park['name'])) { continue; }
    $urls[] = $baseUrl . '/park.php?name=' . urlencode($park['name']);
}

$ARTICLE = new ARTICLE();
$articleList = $ARTICLE->listing();
foreach ($articleList as $article) {
    if (empty($article['idx'])) { continue; }
    $urls[] = $baseUrl . '/article.php?idx=' . intval($article['idx']);
}

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
echo "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
foreach ($urls as $url) {
    $escaped = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
    echo "  <url>\n";
    echo "    <loc>{$escaped}</loc>\n";
    echo "    <lastmod>{$lastMod}</lastmod>\n";
    echo "    <changefreq>weekly</changefreq>\n";
    echo "  </url>\n";
}
echo "</urlset>";
