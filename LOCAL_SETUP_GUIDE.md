# SKidiyog 本地架設指南 (Local Setup)

**目標**: 在你自己的電腦上使用本地 MySQL 數據庫運行舊專案

**所需時間**: 10-15 分鐘

---

## 📋 前置需求

你需要安裝以下軟件：

### 1. PHP (8.1+)
- **Mac 用戶**: 已內建 PHP，但可升級到 8.1+
  ```bash
  # 檢查版本
  php --version
  ```

- **Windows 用戶**: 下載 [PHP 8.1](https://www.php.net/downloads)

### 2. MySQL (5.7+)
- **Mac 用戶** (推薦方式):
  ```bash
  # 使用 Homebrew 安裝
  brew install mysql
  brew services start mysql
  ```

- **Mac 用戶** (簡單方式):
  下載 [MySQL Community Server](https://dev.mysql.com/downloads/mysql/)

- **Windows 用戶**:
  下載 [MySQL Community Server](https://dev.mysql.com/downloads/mysql/)

---

## 🚀 快速開始 (5 分鐘)

### 步驟 1: 確保 MySQL 正在運行

```bash
# Mac 用戶（使用 Homebrew）
brew services start mysql

# 或者驗證連接
mysql -u root -p
# 按 Enter（如果沒有密碼）
# 輸入 exit 退出
```

### 步驟 2: 運行數據庫設置腳本

```bash
# 進入項目目錄
cd /Users/jameschen/Downloads/diyski/crm/03_FAQ與知識庫/zeabur/skidiyog

# 運行設置腳本
php setup-local-database.php
```

**預期輸出**:
```
=== SKidiyog Local Database Setup ===

Database: skidiyog
Host: localhost:3306
User: root

[1] Connecting to MySQL Server...
✓ Connected to MySQL Server

[2] Creating Database 'skidiyog'...
✓ Database created successfully

[3] Selecting Database...
✓ Database selected

[4] Creating Tables...
  ✓ Table 'parks' created
  ✓ Table 'instructors' created
  ✓ Table 'articles' created

[5] Importing Data from JSON Files...

  Importing parks.json...
    ✓ Parks imported (XX records)

  Importing instructors.json...
    ✓ Instructors imported (XX records)

  Importing articles.json...
    ✓ Articles imported (XX records)

[6] Summary
========================================
✓ Database setup completed successfully!

Database Details:
  Host: localhost:3306
  User: root
  Password: (as configured)
  Database: skidiyog

Next Steps:
  1. Update includes/db.class.php with your database credentials
  2. Update the environment variables in includes/config.php
  3. Test the connection by visiting verify-setup.php

Local Testing:
  - PHP Server: php -S localhost:8000
  - Browser: http://localhost:8000
  - Verify Setup: http://localhost:8000/verify-setup.php

========================================
Setup complete!
```

### 步驟 3: 啟動本地 PHP 服務器

```bash
# 在項目目錄中運行
php -S localhost:8000
```

**輸出**:
```
[Wed Nov 06 16:30:00 2025] PHP 8.1.x Development Server (http://localhost:8000) started
```

### 步驟 4: 訪問應用

在瀏覽器中打開以下 URL：

| URL | 說明 |
|-----|------|
| http://localhost:8000/ | 首頁 |
| http://localhost:8000/park.php?name=naeba | Naeba 雪場介紹 |
| http://localhost:8000/bkAdmin/parks.php | 後台編輯雪場 |
| http://localhost:8000/bkAdmin/articles.php | 後台編輯文章 |
| http://localhost:8000/verify-setup.php | 環境驗證 |

---

## ⚙️ 故障排除

### 問題 1: MySQL 連接失敗

**症狀**:
```
❌ Connection Failed: Access denied for user 'root'@'localhost'
```

**解決方案**:

1. 檢查 MySQL 是否運行:
   ```bash
   # Mac
   brew services list | grep mysql

   # Windows
   # 在任務管理器中檢查 MySQL80 服務
   ```

2. 如果 root 有密碼，編輯 `setup-local-database.php`:
   ```php
   // 第 5 行左右，修改：
   $db_pass = 'your_mysql_password';  // 改為你的密碼
   ```

3. 或者編輯 `includes/db.class.php`:
   ```php
   define('DB_PASS', 'your_mysql_password');  // 改為你的密碼
   ```

### 問題 2: PHP 找不到命令

**症狀**:
```
command not found: php
```

**解決方案** (Mac):
```bash
# 使用完整路徑
/usr/bin/php --version

# 或安裝 Homebrew 版本
brew install php
```

### 問題 3: 無法訪問 http://localhost:8000/

**症狀**:
```
無法連接到伺服器或頁面空白
```

**解決方案**:

1. 確保 PHP 服務器仍在運行（終端沒有關閉）
2. 確保你在正確的項目目錄中啟動了服務器
3. 清除瀏覽器緩存（Ctrl+Shift+Delete 或 Cmd+Shift+Delete）
4. 嘗試訪問 http://127.0.0.1:8000/

### 問題 4: 表中沒有數據

**症狀**:
```
verify-setup.php 顯示: Tables Found: 3
但後台頁面顯示: 沒有雪場數據
```

**解決方案**:

重新運行設置腳本確保數據已導入：
```bash
php setup-local-database.php
```

或者手動驗證數據：
```bash
mysql -u root skidiyog -e "SELECT COUNT(*) FROM parks;"
mysql -u root skidiyog -e "SELECT COUNT(*) FROM articles;"
mysql -u root skidiyog -e "SELECT COUNT(*) FROM instructors;"
```

### 問題 5: 後台編輯不工作

**症狀**:
```
點擊編輯後頁面沒有反應或顯示錯誤
```

**解決方案**:

1. 檢查 verify-setup.php:
   ```
   http://localhost:8000/verify-setup.php
   ```

2. 查看瀏覽器開發工具的 Console 標籤 (F12)

3. 檢查 PHP 服務器的終端輸出是否有錯誤

---

## 📊 驗證設置

### 使用 verify-setup.php

訪問 http://localhost:8000/verify-setup.php

應該看到所有項目都是 ✓ (checkmark)：
```
=== PHP Configuration ===
PHP Version: 8.1.x ✓
PHP SAPI: cli-server

=== Required Extensions ===
Extension mysqli: ✓ Loaded
Extension json: ✓ Loaded
...

=== Database Connection Test ===
✓ MySQL Connected Successfully
  Host: localhost
  Database: skidiyog
  Server Version: 5.7.x
  Tables Found: 3

=== Environment Variables ===
DB_HOST: localhost
DB_USER: root
DB_PASS: ***
DB_NAME: skidiyog

=== File & Directory Permissions ===
✓ Directory: includes/
✓ Directory: database/
✓ Directory: bkAdmin/
...

=== Critical Files ===
✓ File: includes/config.php
✓ File: database/parks.json
...
```

### 使用命令行驗證

```bash
# 檢查 parks 表
mysql -u root skidiyog -e "SELECT idx, name, cname FROM parks LIMIT 5;"

# 檢查 articles 表
mysql -u root skidiyog -e "SELECT idx, title FROM articles LIMIT 5;"

# 檢查 instructors 表
mysql -u root skidiyog -e "SELECT idx, name, cname FROM instructors LIMIT 5;"
```

---

## 🔧 進階配置

### 修改 MySQL 密碼

如果你的 root 用戶有密碼，需要在兩個地方修改：

**1. setup-local-database.php (第 5 行)**:
```php
$db_pass = 'your_mysql_password';
```

**2. includes/db.class.php (第 19 行)**:
```php
define('DB_PASS', 'your_mysql_password');
```

### 使用不同的 MySQL 用戶

如果你想使用不同的用戶（推薦做法）：

```bash
# 創建新用戶
mysql -u root -p -e "
  CREATE USER 'skidiyog_user'@'localhost' IDENTIFIED BY 'your_password';
  GRANT ALL PRIVILEGES ON skidiyog.* TO 'skidiyog_user'@'localhost';
  FLUSH PRIVILEGES;
"
```

然後修改配置：

**setup-local-database.php**:
```php
$db_user = 'skidiyog_user';
$db_pass = 'your_password';
```

**includes/db.class.php**:
```php
define('DB_USER', 'skidiyog_user');
define('DB_PASS', 'your_password');
```

### 在不同的端口運行 PHP 服務器

```bash
# 使用 8080 端口而不是 8000
php -S localhost:8080

# 或指定特定 IP
php -S 127.0.0.1:3000
```

---

## 🌐 從本地轉到 Zeabur (可選)

當你確認本地運行正常後，可以部署到 Zeabur：

1. 確保所有編輯都已提交到 Git:
   ```bash
   git add -A
   git commit -m "feat: local database setup with MySQL support"
   git push origin main
   ```

2. 在 Zeabur 儀表板設置環境變量（使用遠端 AWS RDS 或 Zeabur MySQL）

3. Zeabur 會自動重新部署

---

## 📝 常見問題 FAQ

**Q: 可以同時在本地和 Zeabur 上運行嗎？**

A: 可以！db.class.php 會自動根據 hostname 選擇合適的數據庫：
- localhost → 本地 MySQL
- zeabur.app → 遠端數據庫

**Q: 我修改了後台數據，但沒有看到變化？**

A:
1. 確保編輯成功（檢查瀏覽器控制台）
2. 清除瀏覽器緩存
3. 檢查數據庫中的更新：
   ```bash
   mysql -u root skidiyog -e "SELECT * FROM parks WHERE idx=1\G"
   ```

**Q: 如何備份本地數據？**

A:
```bash
# 導出整個數據庫
mysqldump -u root skidiyog > skidiyog_backup.sql

# 導出特定表
mysqldump -u root skidiyog parks > parks_backup.sql
```

**Q: 如何恢復備份？**

A:
```bash
# 恢復整個數據庫
mysql -u root skidiyog < skidiyog_backup.sql

# 或使用 Setup 腳本重新導入（會覆蓋所有數據）
php setup-local-database.php
```

---

## 📞 技術支援

如果遇到問題：

1. **檢查 verify-setup.php**: http://localhost:8000/verify-setup.php
2. **查看 PHP 服務器日誌**: 服務器終端窗口會顯示錯誤
3. **查看瀏覽器開發工具**: F12 → Console 標籤查看 JavaScript 錯誤
4. **檢查 MySQL 日誌**: `/var/log/mysql/error.log` (Mac/Linux)

---

## 🎉 完成後的步驟

當你確認本地運行正常後：

1. **前台測試**:
   - [ ] http://localhost:8000/ - 首頁能正常加載
   - [ ] http://localhost:8000/park.php?name=naeba - 可見雪場信息
   - [ ] 多個雪場都能訪問

2. **後台測試**:
   - [ ] http://localhost:8000/bkAdmin/parks.php - 可編輯雪場
   - [ ] http://localhost:8000/bkAdmin/articles.php - 可編輯文章
   - [ ] 修改內容後能夠保存

3. **準備部署**:
   - [ ] 提交所有更改到 Git
   - [ ] 推送到 GitHub
   - [ ] 在 Zeabur 上配置環境變量
   - [ ] 監控 Zeabur 部署日誌

---

**祝你成功！🚀**
