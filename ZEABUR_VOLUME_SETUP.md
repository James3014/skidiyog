# Zeabur Volume 設定指南

## 問題診斷

資料庫檔案 `/data/skidiyog.db` 目前位於 Zeabur 的 ephemeral filesystem（暫存檔案系統），導致檔案為**唯讀狀態**。

錯誤訊息：
```
SQLSTATE[HY000]: General error: 8 attempt to write a readonly database
```

## 解決方案

需要在 Zeabur 控制台掛載 **Volume（持久化儲存空間）** 到 `/data` 目錄。

### 設定步驟

1. **登入 Zeabur Dashboard**
   - 前往 https://zeabur.com
   - 選擇此專案的 Service

2. **掛載 Volume**
   - 點選服務頁面的 **"Volumes"** 分頁
   - 點擊 **"Mount Volumes"** 按鈕
   - 填寫兩個欄位：
     - **Volume ID**: `data` （識別名稱）
     - **Mount Directory**: `/data` （掛載路徑）
   - 點擊確認

3. **重要提醒**
   - ⚠️ **掛載後該目錄的資料會被清空**
   - ⚠️ 啟用 Volume 後，服務重啟時會有短暫停機時間（無法 zero-downtime restart）

4. **資料遷移**
   - 掛載 Volume 前，先下載當前資料庫備份：
     ```bash
     # 從 Zeabur 下載當前資料庫（如果有重要資料）
     curl https://skidiyog.zeabur.app/data/skidiyog.db -o backup_before_volume.db
     ```
   - 掛載 Volume 後，資料庫會自動重新建立（透過 `db.class.php` 的 `createTablesIfNotExist()`）

5. **驗證設定**
   - 掛載完成後，訪問測試頁面：
     - https://skidiyog.zeabur.app/debug_db.php
   - 確認輸出顯示：
     - `File writable: YES`
     - `Write SUCCESS: 1 rows affected ✅`

## 技術說明

### 為什麼需要 Volume？

Zeabur 預設使用 **stateless（無狀態）** 部署：
- 服務重啟時，檔案系統會重置為初始狀態
- 適合靜態網站、無狀態 API
- **不適合需要持久化資料的應用（如 SQLite 資料庫）**

使用 Volume 後：
- 資料會持久化儲存，重啟不會遺失
- 檔案具有寫入權限
- 適合資料庫檔案、上傳檔案等需求

### 程式碼已完成的準備工作

1. **資料庫類別自動修復權限** (`includes/db.class.php`)
   - 自動建立 `/data` 目錄
   - 嘗試設定目錄權限為 777
   - 嘗試設定檔案權限為 666
   - 啟用 WAL 模式提升併發效能

2. **自動建表機制**
   - 資料庫不存在時自動建立
   - 自動建立 parks、instructors、articles 資料表
   - 建立相容舊系統的 view (parkInfo, instructorInfo)

3. **Startup 腳本** (`.zeabur/startup.sh`)
   - 部署時自動執行權限修復
   - 確保資料目錄可寫

## 費用說明

Volume 使用會產生額外費用，詳見：
https://zeabur.com/docs/billing/pricing

## 參考文件

- Zeabur Volumes 官方文件：https://zeabur.com/docs/en-US/data-management/volumes
- SQLite 唯讀錯誤常見原因：https://matthewsetter.com/sqlite-attempt-to-write-readonly-database/

---

## 完成後測試清單

- [ ] Volume 已掛載到 `/data`
- [ ] debug_db.php 顯示 "File writable: YES"
- [ ] 登入後台：https://skidiyog.zeabur.app/bkAdmin/login.php
- [ ] 編輯雪場資料（例如：苗場 Naeba）
- [ ] 儲存後重新整理頁面，確認資料已更新
- [ ] 編輯文章資料並驗證
- [ ] 前台顯示更新後的內容

設定完成後，後台編輯功能將完全正常運作！
