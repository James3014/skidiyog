# Zeabur MySQL 快速開始 (10 分鐘)

## 🎯 簡化版本 - 3 個步驟就完成！

---

## 步驟 1: 在 Zeabur 添加 MySQL 服務 (2 分鐘)

### 1.1 打開 Zeabur 儀表板
```
https://dash.zeabur.com → skidiyog 專案
```

### 1.2 添加 MySQL 服務
1. 點擊 **+ 按鈕** 或 "Add Service"
2. 搜尋並選擇 **MySQL**
3. 點擊 **Deploy**
4. 等待 30-60 秒（直到綠色）

### 1.3 複製連接信息
點擊 MySQL 服務卡片，記下這些信息：

```
MYSQL_HOST: mysql-xxx.zeabur.app
MYSQL_PORT: 3306
MYSQL_USERNAME: root
MYSQL_PASSWORD: your-password-here
MYSQL_DATABASE: zeabur
```

---

## 步驟 2: 設置 PHP 應用環境變量 (2 分鐘)

### 2.1 進入 PHP 應用設置
1. 點擊 **skidiyog** (PHP 應用)
2. 點擊 **Settings** → **Environment Variables**

### 2.2 添加這 5 個環境變量

```
DB_HOST = mysql-xxx.zeabur.app
DB_USER = root
DB_PASS = your-password-here
DB_NAME = skidiyog
DB_PORT = 3306
```

✓ 全部填完後，應該看到 5 個變數在列表中

---

## 步驟 3: 導入數據並部署 (6 分鐘)

### 方案 A: 使用本地運行（推薦）

#### 3.1 在本地運行導入腳本

```bash
# 進入項目目錄
cd /Users/jameschen/Downloads/diyski/crm/03_FAQ與知識庫/zeabur/skidiyog

# 設置環境變量（使用從 Zeabur 複製的值）
export DB_HOST=mysql-xxx.zeabur.app
export DB_USER=root
export DB_PASS=your-password-here
export DB_NAME=skidiyog
export DB_PORT=3306

# 運行導入腳本
php import-zeabur-mysql.php
```

**預期輸出**:
```
=== SKidiyog MySQL 數據導入 ===

[1] 連接到 MySQL...
✓ 已連接到 MySQL

[2] 創建數據庫表...
  ✓ 表 'parks' 已準備
  ✓ 表 'instructors' 已準備
  ✓ 表 'articles' 已準備

[3] 導入數據...
  正在導入 parks...
    ✓ 已導入 XX 個雪場
  正在導入 instructors...
    ✓ 已導入 XX 位教練
  正在導入 articles...
    ✓ 已導入 XX 篇文章

[4] 驗證導入結果...
  總記錄數:
    - Parks: XX
    - Instructors: XX
    - Articles: XX

==================================================
✓ 導入完成！
==================================================
```

#### 3.2 推送代碼並重新部署

```bash
# 確保代碼已提交
git status

# 推送到 GitHub
git push origin main
```

Zeabur 會自動偵測並重新部署。

### 方案 B: 使用 Zeabur 內部運行（無需本地環境）

#### 3.1 重新部署應用

在 Zeabur 儀表板：
1. 進入 **skidiyog** PHP 應用
2. 點擊 **Deployments**
3. 點擊最新部署的 **...** → **Redeploy**

#### 3.2 通過 URL 導入數據

部署完成後（變為綠色），訪問：

```
https://skidiyog.zeabur.app/import-zeabur-mysql.php?token=skidiyog_import_2025
```

應該看到導入成功的訊息。

---

## ✅ 驗證部署成功

### 檢查清單

訪問這些 URL，都應該正常工作：

| URL | 預期結果 | 狀態 |
|-----|---------|------|
| https://skidiyog.zeabur.app/ | 首頁顯示雪場卡片 | ✓/✗ |
| https://skidiyog.zeabur.app/park.php?name=naeba | 顯示 Naeba 雪場介紹 | ✓/✗ |
| https://skidiyog.zeabur.app/bkAdmin/parks.php | 後台編輯雪場頁面 | ✓/✗ |
| https://skidiyog.zeabur.app/bkAdmin/articles.php | 後台編輯文章頁面 | ✓/✗ |
| https://skidiyog.zeabur.app/verify-setup.php | 顯示 ✓ MySQL Connected | ✓/✗ |

---

## 🎉 完成！

如果所有 URL 都正常工作，恭喜你！系統已在 Zeabur 上線。

### 現在你可以：

1. **編輯雪場信息**
   - 訪問: https://skidiyog.zeabur.app/bkAdmin/parks.php
   - 選擇雪場 → 選擇章節 → 編輯內容 → 提交

2. **編輯文章**
   - 訪問: https://skidiyog.zeabur.app/bkAdmin/articles.php
   - 選擇文章 → 編輯 → 提交

3. **分享你的系統**
   - 域名: https://skidiyog.zeabur.app
   - 可自定義域名（見後續步驟）

---

## ⚠️ 常見問題

### Q: 導入時說「連接失敗」？

A:
1. 檢查 MySQL 服務是否在 Zeabur 上運行（應為綠色）
2. 確認複製的主機名和密碼是否正確
3. 等待 MySQL 容器完全啟動（1-2 分鐘）
4. 嘗試重新運行腳本

### Q: verify-setup.php 顯示「找不到數據」？

A:
1. 確認導入腳本已成功運行
2. 檢查輸出是否顯示「已導入 XX 個雪場」
3. 如未運行，執行方案 B（通過 URL 導入）

### Q: 後台頁面無法編輯？

A:
1. 檢查 verify-setup.php 顯示 ✓ 連接成功
2. 查看瀏覽器 F12 → Console 是否有錯誤
3. 檢查 Zeabur 日誌中是否有 PHP 錯誤

### Q: 數據沒有顯示？

A:
1. 確認導入成功（看 import 腳本的輸出）
2. 清除瀏覽器緩存（Ctrl+Shift+Delete）
3. 訪問 verify-setup.php 查看表中記錄數

---

## 📚 詳細文檔

如需更多信息：

- **本地開發**: 參考 `LOCAL_SETUP_GUIDE.md`
- **Zeabur MySQL 詳細**: 參考 `ZEABUR_MYSQL_SETUP.md`
- **整合 FAQ**: 參考 `FAQ_INTEGRATION_GUIDE.md`

---

## 🚀 下一步 (可選)

### 自定義域名

在 Zeabur 儀表板：
1. 進入 **skidiyog** 專案
2. 點擊 **Domains**
3. 添加你的自定義域名
4. 按照 DNS 設置指示配置

### 自動備份

Zeabur MySQL 自動每日備份。如需手動導出：

```bash
# 導出所有數據
mysqldump -h mysql-xxx.zeabur.app -u root -p skidiyog > backup.sql
```

### 監控系統健康

定期訪問 verify-setup.php 確保系統正常運行。

---

**祝你使用愉快！🎉**

有問題嗎？檢查 `ZEABUR_MYSQL_SETUP.md` 的故障排除部分。
