# Zeabur 部署設置指南

**狀態**: 代碼已準備好，需要 Zeabur 環境變量配置
**部署 URL**: https://skidiyog.zeabur.app/
**GitHub**: https://github.com/James3014/skidiyog

---

## ❌ 檢測到的問題

Zeabur 部署顯示 config.php 缺失。這是**正常的**，因為：
- config.php 現在是動態生成的（讀取環境變量）
- 需要在 Zeabur 儀表板設置環境變量

---

## ✅ 解決方案 - 3 步驟設置

### 步驟 1️⃣: 進入 Zeabur 儀表板

**URL**: https://dash.zeabur.com

1. 登入您的 Zeabur 帳戶
2. 進入 `skidiyog` 專案
3. 點擊 "Settings" (設置)

### 步驟 2️⃣: 添加 MySQL 服務

如果還未添加：

1. 點擊 "Add Service" (添加服務)
2. 搜索 "MySQL"
3. 選擇 MySQL 8.0
4. 點擊 "Deploy"

**保存連接信息**:
- 記下 MySQL 服務的:
  - Host (主機名)
  - Port (埠號，通常 3306)
  - Username (用戶名)
  - Password (密碼)

### 步驟 3️⃣: 設置環境變量

在 Zeabur 儀表板：

1. 進入 `skidiyog` 應用（PHP 應用，不是 MySQL）
2. 點擊 "Settings"
3. 進入 "Environment Variables"

**添加以下環境變量**:

```
DB_HOST = [MySQL service host from Step 2]
DB_USER = [MySQL username]
DB_PASS = [MySQL password]
DB_NAME = skidiyog
DB_PORT = 3306
SECRET_KEY = skidiyog-secret-2025-$(date +%s)
```

**例子** (替換為您的實際值):
```
DB_HOST = mysql-abc123.internal.zeabur.app
DB_USER = root
DB_PASS = your_secure_password_here
DB_NAME = skidiyog
DB_PORT = 3306
SECRET_KEY = my-secure-random-key-12345
```

### 步驟 4️⃣: 觸發重新部署

在環境變量設置完成後：

1. 進入 "Deployments" 頁籤
2. 點擊最新部署旁的三個點 (...)
3. 選擇 "Redeploy" (重新部署)
4. 等待部署完成（通常 2-5 分鐘）

**部署狀態**:
- 🟠 Orange = 正在構建
- 🟢 Green = 部署完成
- 🔴 Red = 部署失敗

---

## 🔍 驗證部署成功

部署完成後，訪問以下 URL 驗證：

### 1. 驗證環境配置
```
https://skidiyog.zeabur.app/verify-setup.php
```

**預期結果**:
```
=== SKidiyog Environment Verification ===

=== PHP Configuration ===
PHP Version: 8.1.x
PHP SAPI: fpm-fcgi
OS: Linux ...

=== Required Extensions ===
Extension mysqli: ✓ Loaded
Extension json: ✓ Loaded
Extension curl: ✓ Loaded
Extension mbstring: ✓ Loaded

=== Database Connection Test ===
✓ MySQL Connected Successfully
  Host: [您的 MySQL Host]
  Database: skidiyog
  Server Version: 8.0.x
  Tables Found: [N 個表]

=== Environment Variables ===
DB_HOST = [已設置]
DB_USER = [已設置]
DB_PASS = ***
DB_NAME = skidiyog
SECRET_KEY = ***

=== File & Directory Permissions ===
✓ Directory: includes/
✓ Directory: database/
✓ Directory: assets/
✓ Directory: bkAdmin/

=== Critical Files ===
✓ File: includes/sdk.php
✓ File: includes/config.php
✓ File: database/parks.json
✓ File: database/articles.json
✓ File: .htaccess

=== Verification Summary ===
✓ Environment Configured Correctly
✓ All extensions loaded
✓ Database connection successful
✓ System ready for deployment
```

### 2. 測試首頁
```
https://skidiyog.zeabur.app/
```

應顯示完整的首頁，無 PHP 錯誤

### 3. 測試度假村頁面
```
https://skidiyog.zeabur.app/park.php?name=naeba
```

應顯示 Naeba 度假村信息

### 4. 測試後台
```
https://skidiyog.zeabur.app/bkAdmin/
```

應顯示登入頁面

---

## ❌ 常見問題

### 問題 1: MySQL 連接失敗

**症狀**: verify-setup.php 顯示
```
✗ Connection Failed
  Error: Access denied for user
```

**解決方案**:
1. 檢查 DB_USER 和 DB_PASS 是否正確
2. 確認 DB_HOST 是 MySQL 服務的內部地址（不是 localhost）
3. 確認 DB_NAME = skidiyog
4. 驗證 MySQL 服務狀態 (應為 Running)

### 問題 2: config.php 仍然缺失

**症狀**: 頁面顯示
```
Failed to open stream: No such file or directory in /var/www/includes/sdk.php
```

**解決方案**:
1. 檢查您是否添加了所有環境變量
2. 驗證 Zeabur 已完成重新部署 (檢查 Deployments 頁籤)
3. 清除瀏覽器緩存 (Ctrl+Shift+Delete)
4. 等待 2-5 分鐘後重試

### 問題 3: 部署失敗 (紅色狀態)

**解決方案**:
1. 進入 "Logs" 頁籤查看錯誤訊息
2. 搜尋關鍵字: ERROR, FATAL
3. 複製錯誤訊息用於診斷
4. 嘗試重新部署

---

## 📋 完整設置清單

部署前：
- [ ] 已讀本文檔
- [ ] 已擁有 Zeabur 帳戶
- [ ] GitHub 倉庫已連接 (https://github.com/James3014/skidiyog)

部署中：
- [ ] MySQL 服務已添加
- [ ] 已記下 MySQL 連接信息
- [ ] 已在 Zeabur 儀表板設置所有環境變量
- [ ] 已觸發重新部署
- [ ] 已等待部署完成 (2-5 分鐘)

部署後驗證：
- [ ] verify-setup.php 全部通過 ✓
- [ ] 首頁正常加載
- [ ] 度假村頁面顯示內容
- [ ] 後台登入頁面顯示

---

## 🔑 環境變量詳解

| 變數 | 說明 | 例子 | 必填 |
|-----|------|------|-----|
| DB_HOST | MySQL 服務主機 | mysql-xyz.internal.zeabur.app | ✅ |
| DB_USER | MySQL 用戶名 | root | ✅ |
| DB_PASS | MySQL 密碼 | strong_password_123 | ✅ |
| DB_NAME | 數據庫名稱 | skidiyog | ✅ |
| DB_PORT | MySQL 埠號 | 3306 | ⚠️ (默認 3306) |
| SECRET_KEY | 加密密鑰 | any-random-string | ⚠️ (可選) |
| ZEABUR | 標記為 Zeabur 環境 | 1 或 true | ⚠️ (自動) |

---

## 📞 支援

如果遇到問題：

1. **檢查 verify-setup.php 輸出** - 最直接的診斷方法
2. **查看 Zeabur Logs** - 儀表板 → Logs 頁籤
3. **重新部署** - Deployments → Redeploy
4. **清除緩存** - 瀏覽器 Ctrl+Shift+Delete

---

## 下一步

環境驗證完成後：

1. **整合 FAQ 卡片** (推薦)
   - 參考: FAQ_INTEGRATION_GUIDE.md
   - 時間: 2-3 小時

2. **準備最終遷移**
   - 參考: DEPLOYMENT_CHECKLIST.md
   - 時間: 1-2 小時

---

**文檔版本**: 1.0
**最後更新**: 2025-11-06
**相關文檔**: DEPLOYMENT_GUIDE.md, ZEABUR_DEPLOYMENT_STATUS.md, FAQ_INTEGRATION_GUIDE.md
