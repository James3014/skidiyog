<?php
require_once __DIR__ . '/includes/sdk.php';

header('Content-Type: application/xml; charset=utf-8');

$token = defined('SKID_PREVIEW_TOKEN') ? SKID_PREVIEW_TOKEN : '';
$renderParam = defined('SKID_PREVIEW_RENDER_PARAM') ? SKID_PREVIEW_RENDER_PARAM : 'render';
$renderValue = defined('SKID_PREVIEW_RENDER_VALUE') ? SKID_PREVIEW_RENDER_VALUE : 'static';
$baseUrl = 'https://' . domain_name;
$lastMod = date('Y-m-d');

if (empty($token)) {
    http_response_code(500);
    echo 'Preview token missing';
    exit;
}

$buildUrl = function (string $path, array $params = []) use ($token, $renderParam, $renderValue, $baseUrl): string {
    $path = ltrim($path, '/');
    $params = array_merge($params, [
        'preview_token' => $token,
        $renderParam => $renderValue,
    ]);
    return "{$baseUrl}/{$path}?" . http_build_query($params);
};

$escapeXml = fn ($value) => htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');

$urls = [];

$staticPages = [
    ['path' => 'index.php', 'priority' => '1.0'],
    ['path' => 'front.php', 'priority' => '0.9'],
    ['path' => 'parkList.php', 'priority' => '0.9'],
    ['path' => 'instructorList.php', 'priority' => '0.9'],
    ['path' => 'articleList.php', 'priority' => '0.9'],
    ['path' => 'course.php', 'priority' => '0.8'],
    ['path' => 'schedule.php', 'priority' => '0.8'],
    ['path' => 'booking.php', 'priority' => '0.8'],
    ['path' => 'reservation.php', 'priority' => '0.7'],
    ['path' => 'reg.php', 'priority' => '0.6'],
    ['path' => 'account_login.php', 'priority' => '0.6'],
    ['path' => 'account_info.php', 'priority' => '0.6'],
    ['path' => 'account_pw_reset.php', 'priority' => '0.6'],
];

foreach ($staticPages as $page) {
    $urls[] = [
        'loc' => $buildUrl($page['path']),
        'priority' => $page['priority'],
        'changefreq' => 'weekly',
    ];
}

$DB = new DB();

$parks = $DB->QUERY('SELECT', "SELECT DISTINCT name FROM parks ORDER BY name");
foreach ($parks ?: [] as $park) {
    if (!empty($park['name'])) {
        $urls[] = [
            'loc' => $buildUrl('park.php', ['name' => $park['name']]),
            'priority' => '0.8',
            'changefreq' => 'monthly',
        ];
    }
}

$instructors = $DB->QUERY('SELECT', "SELECT DISTINCT name FROM instructors ORDER BY name");
foreach ($instructors ?: [] as $instructor) {
    if (!empty($instructor['name'])) {
        $urls[] = [
            'loc' => $buildUrl('instructor.php', ['name' => $instructor['name']]),
            'priority' => '0.7',
            'changefreq' => 'monthly',
        ];
    }
}

$articles = $DB->QUERY('SELECT', "SELECT idx FROM articles ORDER BY idx");
foreach ($articles ?: [] as $article) {
    if (!empty($article['idx'])) {
        $urls[] = [
            'loc' => $buildUrl('article.php', ['idx' => $article['idx']]),
            'priority' => '0.6',
            'changefreq' => 'monthly',
        ];
    }
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

foreach ($urls as $entry) {
    echo "  <url>\n";
    echo "    <loc>{$escapeXml($entry['loc'])}</loc>\n";
    echo "    <lastmod>{$lastMod}</lastmod>\n";
    echo "    <changefreq>{$entry['changefreq']}</changefreq>\n";
    echo "    <priority>{$entry['priority']}</priority>\n";
    echo "  </url>\n";
}

echo '</urlset>';
