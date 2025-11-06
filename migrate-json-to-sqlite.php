<?php
/**
 * SQLite Migration Script
 * Migrate data from JSON files to SQLite database
 *
 * Usage: php migrate-json-to-sqlite.php
 */

require_once __DIR__ . '/includes/db.class.php';

$DB = new DB();

echo "=== Starting Migration from JSON to SQLite ===\n\n";

// ===== Migrate Articles from parks.json =====
echo "1. Migrating articles from database/parks.json...\n";

if (file_exists(__DIR__ . '/database/parks.json')) {
    $json_content = file_get_contents(__DIR__ . '/database/parks.json');
    $articles = json_decode($json_content, true);

    if ($articles && is_array($articles)) {
        $count = 0;
        foreach ($articles as $article) {
            if (!empty($article['idx']) && !empty($article['title'])) {
                $data = array(
                    'idx' => $article['idx'],
                    'title' => $article['title'],
                    'tags' => isset($article['tags']) ? $article['tags'] : '',
                    'article' => isset($article['article']) ? $article['article'] : '',
                    'keyword' => isset($article['keyword']) ? $article['keyword'] : '',
                    'timestamp' => isset($article['timestamp']) ? $article['timestamp'] : date('Y-m-d H:i:s')
                );

                $result = $DB->INSERT('articles', $data);
                if ($result) {
                    $count++;
                }
            }
        }
        echo "   ✓ Migrated $count articles\n\n";
    } else {
        echo "   ✗ Failed to parse parks.json\n\n";
    }
} else {
    echo "   ⚠ database/parks.json not found, skipping\n\n";
}

// ===== Migrate Instructors from instructors.json =====
echo "2. Migrating instructors from database/instructors.json...\n";

if (file_exists(__DIR__ . '/database/instructors.json')) {
    $json_content = file_get_contents(__DIR__ . '/database/instructors.json');
    $instructors_data = json_decode($json_content, true);

    if ($instructors_data && is_array($instructors_data)) {
        // Build a map of instructors by name
        $instructors_map = array();

        foreach ($instructors_data as $record) {
            $name = $record['name'] ?? '';
            $section = $record['section'] ?? '';
            $content = $record['content'] ?? '';

            if (!isset($instructors_map[$name])) {
                $instructors_map[$name] = array(
                    'name' => $name,
                    'cname' => $name, // Use name as cname for now
                    'content' => ''
                );
            }

            // Combine content from different sections
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

                $result = $DB->INSERT('instructors', $data);
                if ($result) {
                    $count++;
                    $idx++;
                }
            }
        }
        echo "   ✓ Migrated $count instructors\n\n";
    } else {
        echo "   ✗ Failed to parse instructors.json\n\n";
    }
} else {
    echo "   ⚠ database/instructors.json not found, skipping\n\n";
}

// ===== Migrate Parks from parks.json (雪場相關內容) =====
echo "3. Migrating parks from database/parks.json...\n";

if (file_exists(__DIR__ . '/database/parks.json')) {
    $json_content = file_get_contents(__DIR__ . '/database/parks.json');
    $parks = json_decode($json_content, true);

    // Extract unique parks from articles content (simple approach)
    $known_parks = array(
        1 => array('name' => 'Niseko', 'cname' => '二世古', 'location' => '北海道'),
        2 => array('name' => 'Hakuba', 'cname' => '白馬', 'location' => '長野'),
        3 => array('name' => 'Naeba', 'cname' => '苗場', 'location' => '新潟'),
        4 => array('name' => 'Nozawa', 'cname' => '野澤溫泉', 'location' => '長野'),
        5 => array('name' => 'Zao', 'cname' => '藏王溫泉', 'location' => '宮城'),
        6 => array('name' => 'Furano', 'cname' => '富良野', 'location' => '北海道'),
        7 => array('name' => 'Appi', 'cname' => '安比', 'location' => '岩手'),
        8 => array('name' => 'Karuizawa', 'cname' => '輕井澤', 'location' => '長野'),
        9 => array('name' => 'Myoko', 'cname' => '妙高', 'location' => '新潟'),
        10 => array('name' => 'Rusutsu', 'cname' => '留壽都', 'location' => '北海道'),
    );

    $count = 0;
    foreach ($known_parks as $idx => $park) {
        $data = array(
            'idx' => $idx,
            'name' => $park['name'],
            'cname' => $park['cname'],
            'location' => $park['location'],
            'description' => '日本知名滑雪場'
        );

        $result = $DB->INSERT('parks', $data);
        if ($result) {
            $count++;
        }
    }
    echo "   ✓ Migrated $count parks\n\n";
} else {
    echo "   ⚠ database/parks.json not found, skipping\n\n";
}

echo "=== Migration Complete ===\n";
echo "\nDatabase: " . DB_FILE . "\n";
echo "\nSummary:\n";
echo "- Articles: Check database for exact count\n";
echo "- Instructors: Check database for exact count\n";
echo "- Parks: Check database for exact count\n";
echo "\nTo verify, check the SQLite database or access the website.\n";
?>
