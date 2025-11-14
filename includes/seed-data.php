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

// Seed parks table from database/parks.json
echo "Initializing parks table from parks.json...\n";
if (file_exists(__DIR__ . '/../database/parks.json')) {
    $parks_json = file_get_contents(__DIR__ . '/../database/parks.json');
    $parks_data = json_decode($parks_json, true);

    if ($parks_data && is_array($parks_data)) {
        // Group parks data by name (each park has multiple sections)
        $parks_by_name = array();
        foreach ($parks_data as $record) {
            $name = $record['name'] ?? '';
            if ($name && !isset($parks_by_name[$name])) {
                $parks_by_name[$name] = array(
                    'idx' => $record['idx'] ?? 0,
                    'name' => $name,
                    'cname' => $record['cname'] ?? $name,
                    'description' => '',
                    'location' => ''
                );
            }
        }

        $park_count = 0;
        foreach ($parks_by_name as $park) {
            if (!empty($park['name'])) {
                $data = array(
                    'idx' => (int)$park['idx'],
                    'name' => $park['name'],
                    'cname' => $park['cname'],
                    'location' => $park['location'],
                    'description' => $park['description']
                );

                try {
                    $result = $DB->INSERT('parks', $data);
                    if ($result !== null) {
                        $park_count++;
                        echo "  ✓ Park: " . $park['cname'] . " ({$park['name']})\n";
                    }
                } catch (Exception $e) {
                    // Silently skip duplicates or errors
                    // echo "    ⚠ " . $park['name'] . ": " . $e->getMessage() . "\n";
                }
            }
        }
        echo "Total parks initialized: $park_count\n\n";
    } else {
        echo "  ❌ Failed to decode parks.json\n\n";
    }
} else {
    echo "  ❌ parks.json not found\n\n";
}

echo "=== Seeding Complete ===\n";
echo "Database location: " . DB_FILE . "\n";
?>

