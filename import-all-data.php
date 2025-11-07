<?php
/**
 * Complete Data Import Script
 * Imports ALL data from JSON files to SQLite
 *
 * This is the main migration script that should be run once after deploying to Zeabur
 */

require_once __DIR__ . '/includes/db.class.php';

set_time_limit(300);

$DB = new DB();

echo "=== SKidiyog Complete Data Import ===\n";
echo "Database: " . DB_FILE . "\n\n";

// ===== 1. Import Parks Data =====
echo "1️⃣  Importing Parks...\n";

if (file_exists(__DIR__ . '/database/parks.json')) {
    $json = file_get_contents(__DIR__ . '/database/parks.json');
    $parks_sections = json_decode($json, true);

    // Section mapping to database fields
    $section_map = array(
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

    // Build parks data structure
    $parks_map = array();

    foreach ($parks_sections as $record) {
        $name = $record['name'] ?? '';
        $section_type = $record['section'] ?? '';
        $content = $record['content'] ?? '';  // content field contains the section data

        if (empty($name)) continue;

        // Initialize park if not exists
        if (!isset($parks_map[$name])) {
            $parks_map[$name] = array(
                'name' => $name,
                'cname' => '',
                'description' => '',
                'location' => '',
                'photo' => '',
                'about' => '',
                'photo_section' => '',
                'location_section' => '',
                'slope_section' => '',
                'ticket_section' => '',
                'time_section' => '',
                'access_section' => '',
                'live_section' => '',
                'rental_section' => '',
                'delivery_section' => '',
                'luggage_section' => '',
                'workout_section' => '',
                'remind_section' => '',
                'join_section' => '',
                'event_section' => ''
            );
        }

        // Map sections to fields
        if ($section_type === 'cname' && !empty($record['content'])) {
            $parks_map[$name]['cname'] = $record['content'];
        } elseif ($section_type === 'desc' && !empty($record['content'])) {
            $parks_map[$name]['description'] = $record['content'];
        } elseif (isset($section_map[$section_type]) && !empty($content)) {
            $field = $section_map[$section_type];
            $parks_map[$name][$field] = $content;
        }
    }

    $count = 0;
    $idx = 1;
    foreach ($parks_map as $name => $park) {
        if (!empty($park['cname'])) {  // Only import parks with Chinese name
            $data = array(
                'idx' => $idx,
                'name' => $park['name'],
                'cname' => $park['cname'],
                'description' => $park['description'],
                'location' => $park['location'],
                'photo' => $park['photo'],
                'about' => $park['about'],
                'photo_section' => $park['photo_section'],
                'location_section' => $park['location_section'],
                'slope_section' => $park['slope_section'],
                'ticket_section' => $park['ticket_section'],
                'time_section' => $park['time_section'],
                'access_section' => $park['access_section'],
                'live_section' => $park['live_section'],
                'rental_section' => $park['rental_section'],
                'delivery_section' => $park['delivery_section'],
                'luggage_section' => $park['luggage_section'],
                'workout_section' => $park['workout_section'],
                'remind_section' => $park['remind_section'],
                'join_section' => $park['join_section'],
                'event_section' => $park['event_section']
            );

            try {
                $DB->INSERT('parks', $data);
                $count++;
                echo "  ✓ " . $park['cname'] . " (" . $park['name'] . ")\n";
                $idx++;
            } catch (Exception $e) {
                // Skip duplicates
            }
        }
    }
    echo "  Imported $count parks with sections\n\n";
} else {
    echo "  ⚠ parks.json not found\n\n";
}

// ===== 2. Import Articles Data =====
echo "2️⃣  Importing Articles (from articles.json)...\n";

if (file_exists(__DIR__ . '/database/articles.json')) {
    $json = file_get_contents(__DIR__ . '/database/articles.json');
    $articles = json_decode($json, true);

    $count = 0;
    if (is_array($articles)) {
        foreach ($articles as $article) {
            // Now articles.json contains proper article data with 'title' field
            if (isset($article['idx']) && isset($article['title']) && !empty($article['title'])) {
                $data = array(
                    'idx' => (int)$article['idx'],
                    'title' => $article['title'],
                    'tags' => isset($article['tags']) ? $article['tags'] : '',
                    'article' => isset($article['article']) ? $article['article'] : '',
                    'keyword' => isset($article['keyword']) ? $article['keyword'] : '',
                    'timestamp' => isset($article['timestamp']) ? $article['timestamp'] : date('Y-m-d H:i:s')
                );

                try {
                    $DB->INSERT('articles', $data);
                    $count++;
                    echo "  ✓ " . mb_substr($article['title'], 0, 40) . "...\n";
                } catch (Exception $e) {
                    // Skip duplicates
                }
            }
        }
        echo "  Total: $count articles imported\n\n";
    }
} else {
    echo "  ⚠ articles.json not found\n\n";
}

// ===== 3. Import Instructors Data =====
echo "3️⃣  Importing Instructors...\n";

if (file_exists(__DIR__ . '/database/instructors.json')) {
    $json = file_get_contents(__DIR__ . '/database/instructors.json');
    $all_records = json_decode($json, true);

    // Build instructor map from multiple sections
    $instructors_map = array();

    if (is_array($all_records)) {
        foreach ($all_records as $record) {
            $name = $record['name'] ?? '';
            $section = $record['section'] ?? '';
            $content = $record['content'] ?? '';

            if (empty($name)) continue;

            if (!isset($instructors_map[$name])) {
                $instructors_map[$name] = array(
                    'name' => $name,
                    'cname' => $name,
                    'content' => ''
                );
            }

            // Accumulate content from 'about' section
            if ($section === 'about') {
                $instructors_map[$name]['content'] = $content;
            }
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
                $DB->INSERT('instructors', $data);
                $count++;
                echo "  ✓ " . $name . "\n";
                $idx++;
            } catch (Exception $e) {
                // Skip duplicates
            }
        }
    }
    echo "  Total: $count instructors imported\n\n";
} else {
    echo "  ⚠ instructors.json not found\n\n";
}

echo "✅ Import Complete!\n";
echo "\nDatabase Summary:\n";

// Query counts
try {
    $parks_result = $DB->QUERY('SELECT', "SELECT COUNT(*) as cnt FROM parks");
    $parks_count = $parks_result[0]['cnt'] ?? 0;

    $articles_result = $DB->QUERY('SELECT', "SELECT COUNT(*) as cnt FROM articles");
    $articles_count = $articles_result[0]['cnt'] ?? 0;

    $instructors_result = $DB->QUERY('SELECT', "SELECT COUNT(*) as cnt FROM instructors");
    $instructors_count = $instructors_result[0]['cnt'] ?? 0;

    echo "  Parks: $parks_count\n";
    echo "  Articles: $articles_count\n";
    echo "  Instructors: $instructors_count\n";
} catch (Exception $e) {
    echo "  (Could not verify counts)\n";
}

echo "\nNext Steps:\n";
echo "1. Visit: https://skidiyog.zeabur.app/\n";
echo "2. Check the frontend displays parks and articles\n";
echo "3. Visit admin: https://skidiyog.zeabur.app/bkAdmin/\n";
?>
