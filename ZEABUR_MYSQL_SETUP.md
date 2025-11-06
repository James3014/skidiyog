# Zeabur MySQL 架設指南

**目標**: 在 Zeabur 上使用 Zeabur 原生 MySQL 服務替代 AWS RDS

**優勢**:
- ✅ 更便宜（包含在 Zeabur 配額內）
- ✅ 更快部署
- ✅ 無需管理外部 AWS 帳戶
- ✅ 自動備份和監控

**所需時間**: 15-20 分鐘

---

## 📋 步驟 1: 在 Zeabur 儀表板添加 MySQL 服務

### 1.1 打開 Zeabur 儀表板
進入: https://dash.zeabur.com

### 1.2 進入你的 skidiyog 專案
- 點擊 **skidiyog** 專案卡片

### 1.3 添加 MySQL 服務

1. 點擊左側 **+** 按鈕或頂部 "Add Service"
2. 搜索並選擇 **MySQL**
3. 選擇版本（推薦 8.0）
4. 點擊 **Deploy**

等待 MySQL 容器啟動（通常 30-60 秒）

### 1.4 獲取 MySQL 連接信息

部署完成後，點擊 MySQL 服務卡片，你會看到：

```
Variables:
├── MYSQL_HOST: mysql-xxx.zeabur.app (或 internal-mysql.xxx.internal)
├── MYSQL_PORT: 3306
├── MYSQL_USERNAME: root
├── MYSQL_PASSWORD: xxxxxxxxxxxxxxxx
├── MYSQL_DATABASE: zeabur (默認數據庫)
```

**記下這些信息！**

---

## 🔧 步驟 2: 配置 PHP 應用環境變量

### 2.1 在 Zeabur 儀表板設置環境變量

1. 返回 skidiyog PHP 應用
2. 點擊 **Settings** → **Environment Variables**
3. 添加以下變量：

```
DB_HOST = <MYSQL_HOST>
DB_USER = root
DB_PASS = <MYSQL_PASSWORD>
DB_NAME = skidiyog
DB_PORT = 3306
```

**例如**:
```
DB_HOST = mysql-xyzabc.zeabur.app
DB_USER = root
DB_PASS = abc123def456ghi789jkl
DB_NAME = skidiyog
DB_PORT = 3306
```

### 2.2 驗證環境變量已設置

完成後應該看到 5 個環境變量在列表中。

---

## 📥 步驟 3: 導入數據到 Zeabur MySQL

### 選項 A: 使用遠程導入腳本（推薦）

#### 方案 A1: 從本地運行

在你的本地電腦上運行一次性導入腳本：

1. **創建導入腳本** (`import-to-zeabur.php`):

```php
<?php
// 從遠程 Zeabur MySQL 導入數據
$db_host = $_ENV['DB_HOST'] ?? 'mysql-xxx.zeabur.app';
$db_user = $_ENV['DB_USER'] ?? 'root';
$db_pass = $_ENV['DB_PASS'] ?? '';
$db_name = $_ENV['DB_NAME'] ?? 'skidiyog';
$db_port = $_ENV['DB_PORT'] ?? 3306;

echo "連接到 Zeabur MySQL...\n";
echo "Host: $db_host:$db_port\n";
echo "Database: $db_name\n\n";

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);

if (!$conn) {
    echo "❌ 連接失敗: " . mysqli_connect_error() . "\n";
    exit(1);
}

echo "✓ 已連接到 Zeabur MySQL\n\n";

// 創建表
echo "[1] 創建表...\n";

$tables = [
    "parks" => "CREATE TABLE IF NOT EXISTS `parks` (
        `idx` INT PRIMARY KEY,
        `name` VARCHAR(100),
        `cname` VARCHAR(100),
        `description` TEXT,
        `location` VARCHAR(255),
        `photo` VARCHAR(255),
        `about` LONGTEXT,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

    "instructors" => "CREATE TABLE IF NOT EXISTS `instructors` (
        `idx` INT PRIMARY KEY,
        `name` VARCHAR(100),
        `cname` VARCHAR(100),
        `content` LONGTEXT,
        `photo` VARCHAR(255),
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

    "articles" => "CREATE TABLE IF NOT EXISTS `articles` (
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

foreach ($tables as $name => $sql) {
    if (mysqli_query($conn, $sql)) {
        echo "  ✓ 表 '{$name}' 已創建\n";
    } else {
        echo "  ❌ 錯誤: " . mysqli_error($conn) . "\n";
    }
}

echo "\n[2] 導入數據...\n";

$json_dir = __DIR__ . '/database/';

// 導入 parks
if (file_exists($json_dir . 'parks.json')) {
    $data = json_decode(file_get_contents($json_dir . 'parks.json'), true);
    foreach ($data as $row) {
        $idx = intval($row['idx'] ?? 0);
        $name = mysqli_real_escape_string($conn, $row['name'] ?? '');
        $cname = mysqli_real_escape_string($conn, $row['cname'] ?? '');
        $sql = "INSERT IGNORE INTO parks (idx, name, cname) VALUES ($idx, '$name', '$cname')";
        mysqli_query($conn, $sql);
    }
    echo "  ✓ Parks 已導入 (" . count($data) . " 條)\n";
}

// 導入 instructors
if (file_exists($json_dir . 'instructors.json')) {
    $data = json_decode(file_get_contents($json_dir . 'instructors.json'), true);
    foreach ($data as $row) {
        $idx = intval($row['idx'] ?? 0);
        $name = mysqli_real_escape_string($conn, $row['name'] ?? '');
        $cname = mysqli_real_escape_string($conn, $row['cname'] ?? '');
        $sql = "INSERT IGNORE INTO instructors (idx, name, cname) VALUES ($idx, '$name', '$cname')";
        mysqli_query($conn, $sql);
    }
    echo "  ✓ Instructors 已導入 (" . count($data) . " 條)\n";
}

// 導入 articles
if (file_exists($json_dir . 'articles.json')) {
    $data = json_decode(file_get_contents($json_dir . 'articles.json'), true);
    foreach ($data as $row) {
        $idx = intval($row['idx'] ?? 0);
        $title = mysqli_real_escape_string($conn, $row['title'] ?? '');
        $sql = "INSERT IGNORE INTO articles (idx, title) VALUES ($idx, '$title')";
        mysqli_query($conn, $sql);
    }
    echo "  ✓ Articles 已導入 (" . count($data) . " 條)\n";
}

echo "\n✓ 數據導入完成！\n";
mysqli_close($conn);
?>
```

2. **在本地運行** (前提是你已取得 Zeabur MySQL 的連接信息):

```bash
# 設置環境變量（使用從 Zeabur 取得的值）
export DB_HOST=mysql-xxx.zeabur.app
export DB_USER=root
export DB_PASS=abc123def456ghi789jkl
export DB_NAME=skidiyog
export DB_PORT=3306

# 運行導入腳本
php import-to-zeabur.php
```

#### 方案 A2: 從 Zeabur 應用內部運行

創建一個臨時的 HTTP 端點來導入數據：

1. **創建** `data-import-endpoint.php`:

```php
<?php
// 只在本地或通過安全 token 允許訪問
$allowed_token = 'your-secret-token-here';
$provided_token = $_GET['token'] ?? '';

if ($provided_token !== $allowed_token) {
    http_response_code(403);
    die('Forbidden');
}

require 'includes/db.class.php';

$db = new DB('skidiyog');
echo "資料庫連接成功\n";
echo "現在可以訪問後台編輯頁面\n";
?>
```

2. **訪問** (部署後):
```
https://skidiyog.zeabur.app/data-import-endpoint.php?token=your-secret-token-here
```

### 選項 B: 使用 mysqldump（如果你有遠程訪問）

```bash
# 從本地導出
mysqldump -u root -p localhost skidiyog > backup.sql

# 導入到 Zeabur
mysql -h mysql-xxx.zeabur.app -u root -p skidiyog < backup.sql
```

---

## 🔄 步驟 4: 更新應用配置

### 4.1 確保 db.class.php 支持遠程連接

檢查 `includes/db.class.php` 已更新為支持環境變量模式（已完成）。

### 4.2 重新部署應用

1. 在 Zeabur 儀表板進入 PHP 應用
2. 點擊 **Deployments** 標籤
3. 找到最新部署，點擊 **...** 選擇 **Redeploy**
4. 等待部署完成（應該轉為綠色）

---

## ✅ 驗證部署

### 測試 MySQL 連接

部署完成後，訪問驗證頁面：

```
https://skidiyog.zeabur.app/verify-setup.php
```

應該看到：
```
=== Database Connection Test ===
✓ MySQL Connected Successfully
  Host: mysql-xxx.zeabur.app
  Database: skidiyog
  Tables Found: 3
```

### 測試前台和後台

| URL | 預期結果 |
|-----|---------|
| https://skidiyog.zeabur.app/ | 首頁正常加載 |
| https://skidiyog.zeabur.app/park.php?name=naeba | 顯示雪場信息 |
| https://skidiyog.zeabur.app/bkAdmin/parks.php | 後台編輯頁面 |
| https://skidiyog.zeabur.app/bkAdmin/articles.php | 編輯文章頁面 |

---

## 🛠️ 故障排除

### 問題 1: 連接超時

**症狀**: verify-setup.php 無法連接到 MySQL

**解決方案**:

1. 檢查 MySQL 服務是否在運行：
   - 去 Zeabur 儀表板查看 MySQL 服務狀態
   - 應該顯示 green 綠色狀態

2. 檢查環境變量是否正確：
   ```bash
   # 在 Zeabur 儀表板 → PHP 應用 → Logs → Runtime Logs
   # 查看 PHP 是否正確讀取了環境變量
   ```

3. 驗證主機名：
   - 使用 `mysql-xxx.zeabur.app` (外部訪問)
   - 或 `internal-mysql-xxx.internal` (內部訪問，更快)

### 問題 2: 數據未導入

**症狀**: verify-setup.php 顯示 "Tables Found: 0"

**解決方案**:

1. 確保 MySQL 服務已完全啟動（等待 60 秒）
2. 重新運行導入腳本
3. 檢查本地 JSON 文件是否完整：
   ```bash
   ls -lh database/
   ```

### 問題 3: 後台頁面無法編輯

**症狀**: 編輯後數據未保存

**解決方案**:

1. 檢查 Zeabur 日誌查看 PHP 錯誤
2. 確認表結構是否正確：
   ```bash
   # 通過 MySQL 客戶端連接
   mysql -h mysql-xxx.zeabur.app -u root -p skidiyog
   > DESCRIBE parks;
   > SELECT COUNT(*) FROM parks;
   ```

---

## 📊 Zeabur MySQL 監控

### 查看數據庫大小

在 Zeabur 儀表板 → MySQL 服務 → Info 標籤

### 備份數據

Zeabur 會自動進行每日備份。如需手動備份：

```bash
mysqldump -h mysql-xxx.zeabur.app -u root -p skidiyog > manual_backup.sql
```

---

## 🚀 部署後的檢查清單

- [ ] MySQL 服務在 Zeabur 上運行（綠色狀態）
- [ ] PHP 應用環境變量已設置 (5 個)
- [ ] 應用已重新部署
- [ ] verify-setup.php 顯示數據庫已連接
- [ ] 首頁能加載且顯示雪場信息
- [ ] 後台 parks.php 可訪問並有數據
- [ ] 後台 articles.php 可訪問並有數據
- [ ] 嘗試編輯雪場信息是否能保存

---

## 💡 備選方案

### 如果你想繼續使用 AWS RDS

保持現有設置：

```
DB_HOST = skidiy-rds-master.cgseduwrbkzc.ap-northeast-1.rds.amazonaws.com
DB_USER = dba
DB_PASS = dba_Skidiy66
DB_NAME = skidiyog
DB_PORT = 33668
```

但需要確保 Zeabur 有網絡訪問 AWS RDS（通常沒有問題）。

### 如果你想使用本地 MySQL（開發用）

使用之前創建的 `LOCAL_SETUP_GUIDE.md`

---

**祝你部署順利！🚀**

如遇問題，請提供以下信息：
1. 錯誤信息完整內容
2. verify-setup.php 的輸出
3. Zeabur 日誌的相關部分
