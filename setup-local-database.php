<?php
/**
 * SKidiyog Local Database Setup Script
 * Converts JSON files to MySQL tables
 * Run this once to set up your local database
 */

// MySQL Connection Settings - MODIFY THESE FOR YOUR LOCAL SETUP
$db_host = 'localhost';
$db_user = 'root';        // Change to your MySQL user
$db_pass = '';            // Change to your MySQL password (empty for local dev)
$db_name = 'skidiyog';
$db_port = 3306;          // Standard MySQL port

// Display settings
echo "=== SKidiyog Local Database Setup ===\n\n";
echo "Database: $db_name\n";
echo "Host: $db_host:$db_port\n";
echo "User: $db_user\n\n";

// Step 1: Connect to MySQL Server
echo "[1] Connecting to MySQL Server...\n";
$conn = @mysqli_connect($db_host, $db_user, $db_pass, '', $db_port);

if (!$conn) {
    echo "❌ Connection Failed: " . mysqli_connect_error() . "\n";
    echo "\nPlease modify the connection settings at the top of this script:\n";
    echo "  - \$db_host: '{$db_host}'\n";
    echo "  - \$db_user: '{$db_user}'\n";
    echo "  - \$db_pass: (your password)\n";
    echo "  - \$db_port: {$db_port}\n";
    exit(1);
}
echo "✓ Connected to MySQL Server\n\n";

// Step 2: Create Database
echo "[2] Creating Database '{$db_name}'...\n";
$sql = "CREATE DATABASE IF NOT EXISTS `{$db_name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
if (mysqli_query($conn, $sql)) {
    echo "✓ Database created successfully\n\n";
} else {
    echo "❌ Error creating database: " . mysqli_error($conn) . "\n";
    exit(1);
}

// Step 3: Select Database
echo "[3] Selecting Database...\n";
mysqli_select_db($conn, $db_name);
echo "✓ Database selected\n\n";

// Step 4: Create Tables
echo "[4] Creating Tables...\n";

// Parks Table
$sql_parks = "CREATE TABLE IF NOT EXISTS `parks` (
  `idx` INT PRIMARY KEY,
  `name` VARCHAR(100),
  `cname` VARCHAR(100),
  `description` TEXT,
  `location` VARCHAR(255),
  `photo` VARCHAR(255),
  `about` LONGTEXT,
  `photo_section` LONGTEXT,
  `location_section` LONGTEXT,
  `slope_section` LONGTEXT,
  `ticket_section` LONGTEXT,
  `time_section` LONGTEXT,
  `access_section` LONGTEXT,
  `live_section` LONGTEXT,
  `rental_section` LONGTEXT,
  `delivery_section` LONGTEXT,
  `luggage_section` LONGTEXT,
  `workout_section` LONGTEXT,
  `remind_section` LONGTEXT,
  `join_section` LONGTEXT,
  `event_section` LONGTEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if (mysqli_query($conn, $sql_parks)) {
    echo "  ✓ Table 'parks' created\n";
} else {
    echo "  ❌ Error: " . mysqli_error($conn) . "\n";
}

// Instructors Table
$sql_instructors = "CREATE TABLE IF NOT EXISTS `instructors` (
  `idx` INT PRIMARY KEY,
  `name` VARCHAR(100),
  `cname` VARCHAR(100),
  `content` LONGTEXT,
  `photo` VARCHAR(255),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if (mysqli_query($conn, $sql_instructors)) {
    echo "  ✓ Table 'instructors' created\n";
} else {
    echo "  ❌ Error: " . mysqli_error($conn) . "\n";
}

// Articles Table
$sql_articles = "CREATE TABLE IF NOT EXISTS `articles` (
  `idx` INT PRIMARY KEY,
  `title` VARCHAR(255),
  `tags` TEXT,
  `article` LONGTEXT,
  `keyword` TEXT,
  `timestamp` DATETIME,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if (mysqli_query($conn, $sql_articles)) {
    echo "  ✓ Table 'articles' created\n\n";
} else {
    echo "  ❌ Error: " . mysqli_error($conn) . "\n";
}

// Step 5: Import Data from JSON Files
echo "[5] Importing Data from JSON Files...\n";

$json_dir = __DIR__ . '/database/';

// Import parks.json
echo "\n  Importing parks.json...\n";
$parks_file = $json_dir . 'parks.json';
if (file_exists($parks_file)) {
    $parks_data = json_decode(file_get_contents($parks_file), true);
    if (is_array($parks_data)) {
        foreach ($parks_data as $park) {
            $idx = intval($park['idx'] ?? 0);
            $name = mysqli_real_escape_string($conn, $park['name'] ?? '');
            $cname = mysqli_real_escape_string($conn, $park['cname'] ?? '');
            $description = mysqli_real_escape_string($conn, $park['description'] ?? '');
            $location = mysqli_real_escape_string($conn, $park['location'] ?? '');

            $sql = "INSERT INTO parks (idx, name, cname, description, location)
                    VALUES ($idx, '$name', '$cname', '$description', '$location')
                    ON DUPLICATE KEY UPDATE idx=idx";

            if (!mysqli_query($conn, $sql)) {
                echo "    ⚠ Error inserting park {$idx}: " . mysqli_error($conn) . "\n";
            }
        }
        echo "    ✓ Parks imported (" . count($parks_data) . " records)\n";
    }
} else {
    echo "    ❌ File not found: {$parks_file}\n";
}

// Import instructors.json
echo "\n  Importing instructors.json...\n";
$instructors_file = $json_dir . 'instructors.json';
if (file_exists($instructors_file)) {
    $instructors_data = json_decode(file_get_contents($instructors_file), true);
    if (is_array($instructors_data)) {
        foreach ($instructors_data as $instructor) {
            $idx = intval($instructor['idx'] ?? 0);
            $name = mysqli_real_escape_string($conn, $instructor['name'] ?? '');
            $cname = mysqli_real_escape_string($conn, $instructor['cname'] ?? '');
            $content = mysqli_real_escape_string($conn, $instructor['content'] ?? '');

            $sql = "INSERT INTO instructors (idx, name, cname, content)
                    VALUES ($idx, '$name', '$cname', '$content')
                    ON DUPLICATE KEY UPDATE idx=idx";

            if (!mysqli_query($conn, $sql)) {
                echo "    ⚠ Error inserting instructor {$idx}: " . mysqli_error($conn) . "\n";
            }
        }
        echo "    ✓ Instructors imported (" . count($instructors_data) . " records)\n";
    }
} else {
    echo "    ❌ File not found: {$instructors_file}\n";
}

// Import articles.json
echo "\n  Importing articles.json...\n";
$articles_file = $json_dir . 'articles.json';
if (file_exists($articles_file)) {
    $articles_data = json_decode(file_get_contents($articles_file), true);
    if (is_array($articles_data)) {
        foreach ($articles_data as $article) {
            $idx = intval($article['idx'] ?? 0);
            $title = mysqli_real_escape_string($conn, $article['title'] ?? '');
            $tags = mysqli_real_escape_string($conn, $article['tags'] ?? '');
            $article_content = mysqli_real_escape_string($conn, $article['article'] ?? '');
            $keyword = mysqli_real_escape_string($conn, $article['keyword'] ?? '');
            $timestamp = $article['timestamp'] ?? date('Y-m-d H:i:s');

            $sql = "INSERT INTO articles (idx, title, tags, article, keyword, timestamp)
                    VALUES ($idx, '$title', '$tags', '$article_content', '$keyword', '$timestamp')
                    ON DUPLICATE KEY UPDATE idx=idx";

            if (!mysqli_query($conn, $sql)) {
                echo "    ⚠ Error inserting article {$idx}: " . mysqli_error($conn) . "\n";
            }
        }
        echo "    ✓ Articles imported (" . count($articles_data) . " records)\n";
    }
} else {
    echo "    ❌ File not found: {$articles_file}\n";
}

// Step 6: Summary
echo "\n[6] Summary\n";
echo "=".str_repeat("=", 40)."\n";
echo "✓ Database setup completed successfully!\n\n";
echo "Database Details:\n";
echo "  Host: {$db_host}:{$db_port}\n";
echo "  User: {$db_user}\n";
echo "  Password: (as configured)\n";
echo "  Database: {$db_name}\n\n";

echo "Next Steps:\n";
echo "  1. Update includes/db.class.php with your database credentials\n";
echo "  2. Update the environment variables in includes/config.php\n";
echo "  3. Test the connection by visiting verify-setup.php\n\n";

echo "Local Testing:\n";
echo "  - PHP Server: php -S localhost:8000\n";
echo "  - Browser: http://localhost:8000\n";
echo "  - Verify Setup: http://localhost:8000/verify-setup.php\n\n";

// Close connection
mysqli_close($conn);

echo "=".str_repeat("=", 40)."\n";
echo "Setup complete!\n";
?>
