<?php
/**
 * SQLite Database Seeding Script
 * Imports data from JSON files into SQLite database
 *
 * This script migrates data from the legacy JSON format to SQLite.
 * Run this once after deployment to Zeabur.
 */

require_once 'db.class.php';

$DB = new DB();

echo "=== Starting Database Seeding from JSON ===\n\n";

// Import articles from database/parks.json
if (file_exists(__DIR__ . '/../database/parks.json')) {
    echo "Loading articles from database/parks.json...\n";
    $json_content = file_get_contents(__DIR__ . '/../database/parks.json');
    $articles = json_decode($json_content, true);

    if ($articles && is_array($articles)) {
        $count = 0;
        foreach ($articles as $article) {
            if (!empty($article['idx']) && !empty($article['title'])) {
                $data = array(
                    'idx' => (int)$article['idx'],
                    'title' => $article['title'],
                    'tags' => isset($article['tags']) ? $article['tags'] : '',
                    'article' => isset($article['article']) ? $article['article'] : '',
                    'keyword' => isset($article['keyword']) ? $article['keyword'] : '',
                    'timestamp' => isset($article['timestamp']) ? $article['timestamp'] : date('Y-m-d H:i:s')
                );

                try {
                    $result = $DB->INSERT('articles', $data);
                    if ($result !== null) {
                        $count++;
                        echo "  ✓ Article: " . substr($article['title'], 0, 40) . "\n";
                    }
                } catch (Exception $e) {
                    // Silently skip duplicates
                }
            }
        }
        echo "Total articles imported: $count\n\n";
    }
}

// Import instructors from database/instructors.json
if (file_exists(__DIR__ . '/../database/instructors.json')) {
    echo "Loading instructors from database/instructors.json...\n";
    $json_content = file_get_contents(__DIR__ . '/../database/instructors.json');
    $instructors_data = json_decode($json_content, true);

    if ($instructors_data && is_array($instructors_data)) {
        $instructors_map = array();

        foreach ($instructors_data as $record) {
            $name = $record['name'] ?? '';
            $section = $record['section'] ?? '';
            $content = $record['content'] ?? '';

            if (!isset($instructors_map[$name])) {
                $instructors_map[$name] = array(
                    'name' => $name,
                    'cname' => $name,
                    'content' => ''
                );
            }

            if ($section === 'about') {
                $instructors_map[$name]['content'] = $content;
            }
        }

        $count = 0;
        $idx = 1;
        foreach ($instructors_map as $name => $instructor) {
            if (!empty($name)) {
                $data = array(
                    'idx' => $idx,
                    'name' => $instructor['name'],
                    'cname' => $instructor['cname'],
                    'content' => $instructor['content']
                );

                try {
                    $result = $DB->INSERT('instructors', $data);
                    if ($result !== null) {
                        $count++;
                        echo "  ✓ Instructor: " . $name . "\n";
                        $idx++;
                    }
                } catch (Exception $e) {
                    // Silently skip duplicates
                }
            }
        }
        echo "Total instructors imported: $count\n\n";
    }
}

// Seed parks table with known ski resorts
echo "Initializing parks table...\n";
$known_parks = array(
    1 => array('name' => 'Niseko', 'cname' => '二世古', 'location' => '北海道', 'description' => 'Hokkaido\'s largest ski resort'),
    2 => array('name' => 'Hakuba', 'cname' => '白馬', 'location' => '長野', 'description' => 'Famous Nagano ski resort'),
    3 => array('name' => 'Naeba', 'cname' => '苗場', 'location' => '新潟', 'description' => 'Popular Niigata resort'),
    4 => array('name' => 'Nozawa', 'cname' => '野澤溫泉', 'location' => '長野', 'description' => 'Hot spring ski resort'),
    5 => array('name' => 'Zao', 'cname' => '藏王溫泉', 'location' => '宮城', 'description' => 'Famous ice monsters'),
    6 => array('name' => 'Furano', 'cname' => '富良野', 'location' => '北海道', 'description' => 'Hokkaido powder paradise'),
);

$park_count = 0;
foreach ($known_parks as $idx => $park) {
    $data = array(
        'idx' => $idx,
        'name' => $park['name'],
        'cname' => $park['cname'],
        'location' => $park['location'],
        'description' => $park['description']
    );

    try {
        $result = $DB->INSERT('parks', $data);
        if ($result !== null) {
            $park_count++;
            echo "  ✓ Park: " . $park['cname'] . "\n";
        }
    } catch (Exception $e) {
        // Silently skip duplicates
    }
}
echo "Total parks initialized: $park_count\n\n";

echo "=== Seeding Complete ===\n";
echo "Database location: " . DB_FILE . "\n";
?>

