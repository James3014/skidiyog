<?php
/**
 * Dynamic Sitemap Generator
 *
 * Generates XML sitemap for all public pages
 * URL: https://skidiyog.zeabur.app/sitemap.php
 *
 * Pages included:
 * - Homepage
 * - Park pages (from database)
 * - Instructor pages (from database)
 * - Article pages (from database)
 * - Static pages (parkList, instructorList, articleList)
 */

require_once 'includes/sdk.php';

header('Content-Type: application/xml; charset=utf-8');

$base_url = 'https://skidiyog.zeabur.app';
$last_mod = date('Y-m-d');

// Start XML
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

// Homepage
echo "  <url>\n";
echo "    <loc>{$base_url}/</loc>\n";
echo "    <lastmod>{$last_mod}</lastmod>\n";
echo "    <changefreq>weekly</changefreq>\n";
echo "    <priority>1.0</priority>\n";
echo "  </url>\n";

// Static pages
$static_pages = [
    ['url' => '/parkList.php', 'priority' => '0.9', 'changefreq' => 'weekly'],
    ['url' => '/instructorList.php', 'priority' => '0.9', 'changefreq' => 'weekly'],
    ['url' => '/articleList.php', 'priority' => '0.8', 'changefreq' => 'weekly'],
];

foreach ($static_pages as $page) {
    echo "  <url>\n";
    echo "    <loc>{$base_url}{$page['url']}</loc>\n";
    echo "    <lastmod>{$last_mod}</lastmod>\n";
    echo "    <changefreq>{$page['changefreq']}</changefreq>\n";
    echo "    <priority>{$page['priority']}</priority>\n";
    echo "  </url>\n";
}

// Parks
try {
    $DB = new db();

    // Get all parks
    $sql = "SELECT DISTINCT name FROM parks ORDER BY name";
    $parks = $DB->QUERY('SELECT', $sql);

    if ($parks) {
        foreach ($parks as $park) {
            $park_name = htmlspecialchars($park['name']);
            echo "  <url>\n";
            echo "    <loc>{$base_url}/park.php?name={$park_name}</loc>\n";
            echo "    <lastmod>{$last_mod}</lastmod>\n";
            echo "    <changefreq>monthly</changefreq>\n";
            echo "    <priority>0.8</priority>\n";
            echo "  </url>\n";
        }
    }

    // Get all instructors
    $sql = "SELECT DISTINCT name FROM instructors ORDER BY name";
    $instructors = $DB->QUERY('SELECT', $sql);

    if ($instructors) {
        foreach ($instructors as $instructor) {
            $instructor_name = htmlspecialchars($instructor['name']);
            echo "  <url>\n";
            echo "    <loc>{$base_url}/instructor.php?name={$instructor_name}</loc>\n";
            echo "    <lastmod>{$last_mod}</lastmod>\n";
            echo "    <changefreq>monthly</changefreq>\n";
            echo "    <priority>0.7</priority>\n";
            echo "  </url>\n";
        }
    }

    // Get all articles
    $ARTICLE = new ARTICLE();
    $articles_sql = "SELECT idx, title FROM articles ORDER BY idx";
    $articles = $DB->QUERY('SELECT', $articles_sql);

    if ($articles) {
        foreach ($articles as $article) {
            $article_idx = htmlspecialchars($article['idx']);
            echo "  <url>\n";
            echo "    <loc>{$base_url}/article.php?idx={$article_idx}</loc>\n";
            echo "    <lastmod>{$last_mod}</lastmod>\n";
            echo "    <changefreq>monthly</changefreq>\n";
            echo "    <priority>0.6</priority>\n";
            echo "  </url>\n";
        }
    }

} catch (Exception $e) {
    // Log error but continue
    error_log('Sitemap generation error: ' . $e->getMessage());
}

// End XML
echo '</urlset>';
