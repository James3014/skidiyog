<?php
/**
 * SKidiyog - Zeabur MySQL Data Import Script v2.0
 *
 * 用途: 將 JSON 數據導入到 Zeabur MySQL 數據庫
 *
 * 使用方式:
 * 1. 本地運行 (設置環境變量)：
 *    export DB_HOST=mysql-xxx.zeabur.app
 *    export DB_USER=root
 *    export DB_PASS=your_password
 *    export DB_NAME=skidiyog
 *    php import-zeabur-mysql.php
 *
 * 2. 通過 URL 訪問 (Zeabur 部署後):
 *    https://skidiyog.zeabur.app/import-zeabur-mysql.php?token=your-secret-token
 *
 * Updated: 2025-11-06 - Added debug output
 */

// 安全令牌 - 更改為強密碼
define('IMPORT_TOKEN', 'skidiyog_import_2025');

// 檢查安全令牌（如果通過 HTTP 訪問）
if (!empty($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $provided_token = $_GET['token'] ?? '';
    if ($provided_token !== IMPORT_TOKEN) {
        http_response_code(403);
        die('❌ 無效的令牌。訪問被拒絕。');
    }
}

echo "=== SKidiyog MySQL 數據導入 ===\n\n";
echo "[DEBUG] Loading database configuration...\n";

// 使用應用程序的數據庫配置
require_once __DIR__ . '/includes/db.class.php';

echo "[DEBUG] Configuration loaded successfully\n";

// 從配置中獲取連接參數
$db_host = DB_HOST;
$db_user = DB_USER;
$db_pass = DB_PASS;
$db_name = DB_DB;
$db_port = (int)DB_PORT;

echo "[DEBUG] Constants extracted:\n";
echo "連接信息:\n";
echo "  主機: {$db_host}:{$db_port}\n";
echo "  用戶: {$db_user}\n";
echo "  數據庫: {$db_name}\n";
echo "  密碼長度: " . strlen($db_pass) . " 字符\n";
echo "  密碼前3字符: " . substr($db_pass, 0, 3) . "...\n";
echo "  密碼是否為空: " . (empty($db_pass) ? 'YES' : 'NO') . "\n";
echo "  (密碼已隱藏)\n\n";

// Step 1: 連接數據庫
echo "[1] 連接到 MySQL...\n";
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);

if (!$conn) {
    echo "❌ 連接失敗: " . mysqli_connect_error() . "\n\n";
    echo "故障排除:\n";
    echo "  - 確認 MySQL 服務在運行\n";
    echo "  - 確認環境變量設置正確\n";
    echo "  - 檢查防火牆設置\n";
    exit(1);
}

echo "✓ 已連接到 MySQL\n\n";

// Step 2: 創建表
echo "[2] 創建數據庫表...\n";

$tables = [
    'parks' => "CREATE TABLE IF NOT EXISTS `parks` (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

    'instructors' => "CREATE TABLE IF NOT EXISTS `instructors` (
        `idx` INT PRIMARY KEY,
        `name` VARCHAR(100),
        `cname` VARCHAR(100),
        `content` LONGTEXT,
        `photo` VARCHAR(255),
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

    'articles' => "CREATE TABLE IF NOT EXISTS `articles` (
        `idx` INT PRIMARY KEY,
        `title` VARCHAR(255),
        `tags` TEXT,
        `article` LONGTEXT,
        `keyword` TEXT,
        `timestamp` DATETIME,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
];

foreach ($tables as $table_name => $create_sql) {
    if (mysqli_query($conn, $create_sql)) {
        echo "  ✓ 表 '{$table_name}' 已準備\n";
    } else {
        echo "  ❌ 錯誤 (表 {$table_name}): " . mysqli_error($conn) . "\n";
    }
}

echo "\n[3] 導入數據...\n";

// 找到 JSON 文件
$json_dir = __DIR__ . '/database/';

// 導入 parks.json
echo "\n  正在導入 parks...\n";
$parks_file = $json_dir . 'parks.json';
if (file_exists($parks_file)) {
    $parks_data = json_decode(file_get_contents($parks_file), true);
    if (is_array($parks_data)) {
        $count = 0;
        foreach ($parks_data as $park) {
            $idx = intval($park['idx'] ?? 0);
            if ($idx <= 0) continue;

            $name = mysqli_real_escape_string($conn, $park['name'] ?? '');
            $cname = mysqli_real_escape_string($conn, $park['cname'] ?? '');
            $description = mysqli_real_escape_string($conn, $park['description'] ?? '');
            $location = mysqli_real_escape_string($conn, $park['location'] ?? '');

            $sql = "INSERT IGNORE INTO parks (idx, name, cname, description, location)
                    VALUES ($idx, '$name', '$cname', '$description', '$location')";

            if (mysqli_query($conn, $sql)) {
                $count++;
            }
        }
        echo "    ✓ 已導入 {$count} 個雪場\n";
    } else {
        echo "    ⚠ JSON 格式錯誤\n";
    }
} else {
    echo "    ⚠ 文件不存在: {$parks_file}\n";
}

// 導入 instructors.json
echo "\n  正在導入 instructors...\n";
$instructors_file = $json_dir . 'instructors.json';
if (file_exists($instructors_file)) {
    $instructors_data = json_decode(file_get_contents($instructors_file), true);
    if (is_array($instructors_data)) {
        $count = 0;
        foreach ($instructors_data as $instructor) {
            $idx = intval($instructor['idx'] ?? 0);
            if ($idx <= 0) continue;

            $name = mysqli_real_escape_string($conn, $instructor['name'] ?? '');
            $cname = mysqli_real_escape_string($conn, $instructor['cname'] ?? '');
            $content = mysqli_real_escape_string($conn, $instructor['content'] ?? '');

            $sql = "INSERT IGNORE INTO instructors (idx, name, cname, content)
                    VALUES ($idx, '$name', '$cname', '$content')";

            if (mysqli_query($conn, $sql)) {
                $count++;
            }
        }
        echo "    ✓ 已導入 {$count} 位教練\n";
    } else {
        echo "    ⚠ JSON 格式錯誤\n";
    }
} else {
    echo "    ⚠ 文件不存在: {$instructors_file}\n";
}

// 導入 articles.json
echo "\n  正在導入 articles...\n";
$articles_file = $json_dir . 'articles.json';
if (file_exists($articles_file)) {
    $articles_data = json_decode(file_get_contents($articles_file), true);
    if (is_array($articles_data)) {
        $count = 0;
        foreach ($articles_data as $article) {
            $idx = intval($article['idx'] ?? 0);
            if ($idx <= 0) continue;

            $title = mysqli_real_escape_string($conn, $article['title'] ?? '');
            $tags = mysqli_real_escape_string($conn, $article['tags'] ?? '');
            $article_content = mysqli_real_escape_string($conn, $article['article'] ?? '');
            $keyword = mysqli_real_escape_string($conn, $article['keyword'] ?? '');
            $timestamp = $article['timestamp'] ?? date('Y-m-d H:i:s');

            $sql = "INSERT IGNORE INTO articles (idx, title, tags, article, keyword, timestamp)
                    VALUES ($idx, '$title', '$tags', '$article_content', '$keyword', '$timestamp')";

            if (mysqli_query($conn, $sql)) {
                $count++;
            }
        }
        echo "    ✓ 已導入 {$count} 篇文章\n";
    } else {
        echo "    ⚠ JSON 格式錯誤\n";
    }
} else {
    echo "    ⚠ 文件不存在: {$articles_file}\n";
}

// Step 3: 驗證
echo "\n[4] 驗證導入結果...\n";

$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM parks");
$parks_count = mysqli_fetch_assoc($result)['count'] ?? 0;

$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM instructors");
$instructors_count = mysqli_fetch_assoc($result)['count'] ?? 0;

$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM articles");
$articles_count = mysqli_fetch_assoc($result)['count'] ?? 0;

echo "  總記錄數:\n";
echo "    - Parks: {$parks_count}\n";
echo "    - Instructors: {$instructors_count}\n";
echo "    - Articles: {$articles_count}\n";

mysqli_close($conn);

echo "\n" . str_repeat("=", 50) . "\n";
echo "✓ 導入完成！\n";
echo str_repeat("=", 50) . "\n\n";

echo "接下來的步驟:\n";
echo "  1. 部署 PHP 應用到 Zeabur\n";
echo "  2. 訪問 https://skidiyog.zeabur.app/verify-setup.php 驗證\n";
echo "  3. 訪問 https://skidiyog.zeabur.app/ 查看前台\n";
echo "  4. 訪問 https://skidiyog.zeabur.app/bkAdmin/parks.php 編輯後台\n";
echo "\n";
?>
