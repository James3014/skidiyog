# SKidiyog 獨立部署指南 (Standalone Deployment Guide)

**Project**: Legacy Ski Lesson Booking Platform (skidiyog)
**Status**: Backup for independent testing & migration
**Purpose**: Deploy as standalone system to verify environment configuration before production migration
**GitHub**: https://github.com/James3014/skidiyog

---

## 1. 系統需求 (System Requirements)

### 最小需求 (Minimum)
- **PHP**: 7.4+ (tested), 8.0+ (recommended)
- **MySQL**: 5.7+ or 8.0+
- **Web Server**: Apache 2.4+ (with mod_rewrite) or Nginx 1.14+
- **Node.js**: 14+ (optional, for build tools)

### 推薦環境 (Recommended)
- **PHP**: 8.1 LTS
- **MySQL**: 8.0 with UTF-8mb4 support
- **Apache**: 2.4 with ModSecurity
- **SSL**: Let's Encrypt certificate

### 依賴項 (Dependencies)
```
- MySQLi extension (PHP compiled with mysqli)
- JSON extension (default in modern PHP)
- cURL extension (for payment gateway)
- OpenSSL extension (for encryption)
- Multibyte String (mbstring) extension
```

---

## 2. 部署選項 (Deployment Options)

### 選項 A: 傳統虛擬主機 (Traditional Hosting)
**優點**: 低成本、簡單維護、直接 FTP 訪問
**缺點**: 性能較低、擴展性受限

**步驟**:
1. 購買支援 PHP 8.0+ 的虛擬主機
2. 創建MySQL資料庫
3. 將代碼上傳至 public_html/
4. 配置 .htaccess 重寫規則

**推薦提供商**:
- Bluehost (支援 PHP 8.1)
- SiteGround (性能優良)
- Kinsta (高端管理)

---

### 選項 B: Zeabur (推薦 - 與新 FAQ 系統一致)
**優點**: 與新 FAQ 系統相同平台、自動 HTTPS、簡化部署
**缺點**: 成本相對較高

**步驟** (見下方 "Zeabur 部署步驟")

---

### 選項 C: Docker + VPS (高級)
**優點**: 完全控制、可擴展、版本控制
**缺點**: 需要 DevOps 知識

**步驟** (見下方 "Docker 部署步驟")

---

## 3. Zeabur 部署步驟 (推薦)

### 3.1 準備 Zeabur 配置

創建 `zeabur.json` (項目根目錄):
```json
{
  "build": {
    "builder": "php",
    "php": {
      "version": "8.1"
    }
  },
  "runtime": {
    "variables": {
      "PHP_VERSION": "8.1"
    }
  }
}
```

### 3.2 創建環境配置

**文件**: `includes/config.example.php`
```php
<?php
// Database Configuration
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_NAME', getenv('DB_NAME') ?: 'skidiyog');

// Domain Configuration (auto-detect from HTTP_HOST)
define('DOMAIN_NAME', $_SERVER['HTTP_HOST'] ?? 'localhost');

// Security
define('SECRET_KEY', getenv('SECRET_KEY') ?: 'change-me-in-production');

// Payment Gateway (disabled for testing)
define('PAYMENT_ENABLED', false);
define('PAYMENT_API_KEY', getenv('PAYMENT_API_KEY') ?: '');
```

**在 Zeabur 部署前**:
1. 複製此文件為 `includes/config.php`
2. 在 Zeabur 儀表板設置環境變量

### 3.3 配置 Zeabur

**GitHub 連接步驟**:
1. 訪問 https://dash.zeabur.com
2. 新建專案 → 連接 GitHub
3. 選擇 `James3014/skidiyog` 倉庫
4. 選擇 main 分支

**添加 MySQL 服務**:
1. 專案 → 添加服務 → MySQL
2. 記下連接詳情 (主機、用戶名、密碼)
3. 創建數據庫: `skidiyog`

**設置環境變量** (Zeabur 儀表板):
```
DB_HOST = mysql服務IP或內部DNS
DB_USER = 用戶名
DB_PASS = 密碼
DB_NAME = skidiyog
SECRET_KEY = 生成一個強密鑰
DEPLOYMENT_ENV = production
```

**部署應用**:
1. 選擇 PHP 運行時
2. 自動部署開啟 (master branch)
3. 構建完成後訪問分配的域名

---

## 4. 傳統虛擬主機部署步驟

### 4.1 數據庫設置

**Step 1**: 通過 cPanel 創建數據庫
```
Database Name: skidiyog_db
Username: skidiyog_user
Password: [生成強密碼]
```

**Step 2**: 導入 SQL 文件 (如有備份)
```bash
mysql -u skidiyog_user -p skidiyog_db < backup.sql
```

**Step 3**: 驗證連接
```bash
mysql -h localhost -u skidiyog_user -p skidiyog_db -e "SHOW TABLES;"
```

### 4.2 代碼部署

**Step 1**: 克隆或下載代碼
```bash
cd /home/username/public_html
git clone https://github.com/James3014/skidiyog.git .
# 或解壓縮下載的 ZIP 文件
```

**Step 2**: 設置文件權限
```bash
chmod 755 includes/
chmod 644 includes/*.php
chmod 755 database/
chmod 755 uploads/ (如有上傳目錄)
chmod 755 cache/
chmod 755 tmp/
```

**Step 3**: 創建配置文件
```bash
cp includes/config.example.php includes/config.php
# 編輯 includes/config.php，設置正確的數據庫憑據
```

### 4.3 驗證 .htaccess

確保 `.htaccess` 在根目錄:
```apache
<IfModule mod_rewrite.c>
  RewriteEngine On
  RewriteBase /
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteRule ^(.*)$ index.php [L]
</IfModule>
```

---

## 5. Docker 部署步驟 (高級)

### 5.1 構建 Docker 鏡像

**文件**: `Dockerfile`
```dockerfile
FROM php:8.1-apache

# 安裝擴展
RUN docker-php-ext-install mysqli json curl mbstring
RUN docker-php-ext-enable mysqli

# 啟用 mod_rewrite
RUN a2enmod rewrite

# 複製應用代碼
COPY . /var/www/html

# 設置權限
RUN chown -R www-data:www-data /var/www/html

# 暴露端口
EXPOSE 80

CMD ["apache2-foreground"]
```

### 5.2 Docker Compose 配置

**文件**: `docker-compose.yml`
```yaml
version: '3.8'

services:
  php:
    build: .
    ports:
      - "8080:80"
    environment:
      DB_HOST: mysql
      DB_USER: skidiyog_user
      DB_PASS: skidiyog_pass
      DB_NAME: skidiyog
    depends_on:
      - mysql
    volumes:
      - ./includes/config.php:/var/www/html/includes/config.php

  mysql:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: root_pass
      MYSQL_DATABASE: skidiyog
      MYSQL_USER: skidiyog_user
      MYSQL_PASSWORD: skidiyog_pass
    ports:
      - "3306:3306"
    volumes:
      - mysql_data:/var/lib/mysql

volumes:
  mysql_data:
```

**運行**:
```bash
docker-compose up -d
# 訪問: http://localhost:8080
```

---

## 6. 部署後驗證清單 (Post-Deployment Checklist)

### 6.1 健康檢查
- [ ] 訪問首頁: http://your-domain.com
- [ ] 檢查所有度假村頁面加載正常
- [ ] 驗證文章頁面顯示正確
- [ ] 測試導師信息頁面

### 6.2 數據庫驗證
```php
<?php
// test-db.php
require 'includes/sdk.php';
$ko = new Ko();
$parks = $ko->getParks();
echo "Found " . count($parks) . " parks\n";
?>
```

訪問 http://your-domain.com/test-db.php (驗證後刪除)

### 6.3 後台驗證
- [ ] 訪問 http://your-domain.com/bkAdmin
- [ ] 用測試帳號登入
- [ ] 驗證所有編輯功能正常

### 6.4 性能檢查
```bash
curl -w "Time: %{time_total}s\n" http://your-domain.com/park.php?name=naeba
# 應在 < 1 秒內返回
```

### 6.5 日誌檢查
```bash
# 檢查 PHP 錯誤日誌
tail -f /var/log/php-errors.log
# 檢查 Apache 訪問日誌
tail -f /var/log/apache2/access.log
```

---

## 7. 環境配置驗證 (Configuration Verification)

### 7.1 PHP 配置驗證

**創建**: `verify-setup.php`
```php
<?php
echo "=== PHP Setup Verification ===\n\n";

// Check PHP Version
echo "PHP Version: " . phpversion() . "\n";
echo "PHP SAPI: " . php_sapi_name() . "\n\n";

// Check Extensions
$required_extensions = ['mysqli', 'json', 'curl', 'mbstring'];
foreach ($required_extensions as $ext) {
    echo "Extension $ext: " . (extension_loaded($ext) ? "✓ Loaded" : "✗ Missing") . "\n";
}

// Check Database Connection
echo "\n=== Database Connection ===\n";
try {
    require 'includes/config.php';
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn) {
        echo "✓ MySQL Connected\n";
        echo "Server: " . mysqli_get_server_info($conn) . "\n";
        mysqli_close($conn);
    } else {
        echo "✗ Connection Failed: " . mysqli_connect_error() . "\n";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

// Check File Permissions
echo "\n=== File Permissions ===\n";
$critical_dirs = ['includes/', 'database/', 'assets/', 'bkAdmin/'];
foreach ($critical_dirs as $dir) {
    $readable = is_readable($dir) ? "✓" : "✗";
    echo "$readable Directory: $dir\n";
}

echo "\n=== Verification Complete ===\n";
?>
```

**訪問**: http://your-domain.com/verify-setup.php

### 7.2 代碼更新驗證

部署完成後驗證關鍵文件:
```bash
# 檢查 domain_name 是否正確自適應
grep -n "domain_name" includes/sdk.php

# 驗證數據文件存在
ls -la database/parks.json database/articles.json database/instructors.json
```

---

## 8. FAQ 卡片集成部署 (FAQ Card Integration)

### 8.1 部署步驟

完成上述部署後，進行 FAQ 整合:

**Step 1**: 修改 `park.php` 添加 FAQ 卡片
```php
// 在 line 98 後添加:
if(!empty($section) && $section != 'all'){
    // ... existing content rendering ...

    // 添加 FAQ 卡片
    echo renderFAQCards($name, $section);
}
```

**Step 2**: 在 `bkAdmin/parks.php` 添加 FAQ 插入按鈕
```html
<!-- 在 textarea 下方添加: -->
<button type="button" onclick="showFAQInsertModal()">
    [+] 插入 FAQ 卡片
</button>
```

**Step 3**: 提交 FAQ 集成代碼至 GitHub
```bash
git add park.php bkAdmin/parks.php
git commit -m "feat: add FAQ card integration to resort pages"
git push origin main
```

Zeabur 自動部署新版本。

---

## 9. 環境變量參考 (Environment Variables)

### 必需變量 (Required)
```
DB_HOST = MySQL 伺服器地址
DB_USER = MySQL 用戶名
DB_PASS = MySQL 密碼
DB_NAME = 數據庫名
SECRET_KEY = 加密密鑰
```

### 可選變量 (Optional)
```
DEPLOYMENT_ENV = production|staging|development
PAYMENT_ENABLED = true|false
PAYMENT_API_KEY = 支付網關 API 金鑰
DEBUG_MODE = true|false
```

---

## 10. 故障排除 (Troubleshooting)

### 問題 1: 白屏 (Blank Page)
**解決**:
1. 檢查 PHP 錯誤日誌
2. 確保 `includes/config.php` 存在且正確
3. 驗證數據庫連接

```bash
php -r "require 'includes/sdk.php'; echo 'SDK loaded';"
```

### 問題 2: 404 錯誤 (URLs Not Working)
**解決**:
1. 驗證 `.htaccess` 存在且 mod_rewrite 已啟用
2. 檢查 RewriteBase 是否正確

```bash
apache2ctl -M | grep rewrite
```

### 問題 3: 數據庫連接失敗
**解決**:
```bash
mysql -h DB_HOST -u DB_USER -p -e "USE DB_NAME; SHOW TABLES;"
```

### 問題 4: 性能緩慢
**解決**:
1. 檢查是否有數據庫查詢優化
2. 啟用 PHP OPcache
3. 檢查文件 I/O 操作

---

## 11. 部署完成後 (Post-Deployment)

### 11.1 監測
- 設置監測服務 (Uptime Robot, Pingdom)
- 配置日誌聚合 (ELK, Datadog)
- 設置告警規則

### 11.2 備份
```bash
# 定期數據庫備份
mysqldump -u user -p database > backup_$(date +%Y%m%d).sql

# Git 提交後自動備份
git push origin main
```

### 11.3 安全加固
- [ ] 修改默認後台路徑 (`/bkAdmin/`)
- [ ] 更新 `includes/config.php` 中的 SECRET_KEY
- [ ] 啟用 HTTPS
- [ ] 設置 firewall 規則

---

## 12. 版本遷移路徑 (Migration Path)

**階段 1**: 當前 (獨立部署測試環境)
```
GitHub (skidiyog) → Zeabur (test domain) → Verified
```

**階段 2**: 驗證環境配置
```
test-domain.diy.ski → 運行完整測試 → 通過
```

**階段 3**: 數據遷移與配置驗證
```
驗證舊系統數據導入 → 確認所有功能正常 → 確認環境設定可行
```

**階段 4**: 生產遷移
```
完全替換 diy.ski 現有系統 → 新增 FAQ 卡片 → 上線
```

---

## 13. 聯繫與支持 (Support)

**GitHub Issues**: https://github.com/James3014/skidiyog/issues

**部署問題**:
- Zeabur 文檔: https://docs.zeabur.com
- PHP 文檔: https://www.php.net/docs.php
- MySQL 文檔: https://dev.mysql.com/doc/

---

## 快速開始 (Quick Start)

### Zeabur (推薦)
```bash
# 1. Fork 或克隆倉庫
git clone https://github.com/James3014/skidiyog.git
cd skidiyog

# 2. 創建配置文件
cp includes/config.example.php includes/config.php

# 3. 在 Zeabur 儀表板連接 GitHub
# 4. 添加 MySQL 服務
# 5. 設置環境變量
# 6. 部署

# 驗證部署
curl https://your-zeabur-domain.com/health.php
```

### 本地 Docker
```bash
docker-compose up -d
curl http://localhost:8080
```

---

**部署日期**: 2025-11-06
**版本**: 1.0
**維護**: James Chen (GitHub: James3014)
