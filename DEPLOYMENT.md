# SKidiyog 部署說明

## 系統概述

本系統是 SKIDIY 自助滑雪網站的 SQLite 版本，保留了原有的完整 UI/UX 和所有文章內容。

## 主要改變

### 資料庫遷移
- **舊系統**: MySQL 資料庫（需要外部伺服器）
- **新系統**: SQLite 本地檔案資料庫（無需外部依賴）

### 代碼修改
- ✅ `includes/db.class.php` - 改用 SQLite PDO
- ✅ `index.php` - 重定向到 articleList.php（網站首頁）
- ✅ 所有其他代碼保持不變

## 部署步驟

### 步驟 1：自動部署
```
GitHub 推送後，Zeabur 會自動部署
等待 1-2 分鐘部署完成
```

### 步驟 2：資料匯入（必須執行）
```
訪問以下網址執行資料匯入：
https://skidiyog.zeabur.app/import-all-data.php

這會將所有文章、教練、雪場資料從 JSON 檔案匯入到 SQLite
```

### 步驟 3：驗證
```
前台首頁: https://skidiyog.zeabur.app/
應該顯示和 https://diy.ski/ 一樣的文章列表

後台管理: https://skidiyog.zeabur.app/bkAdmin/
可以管理文章、教練、雪場資料
```

## 資料來源

### 文章資料
- **來源**: `database/parks.json`
- **表格**: `articles`
- **數量**: 數十篇滑雪相關文章
- **格式**: 包含標題、內容、標籤、關鍵詞等

### 教練資料
- **來源**: `database/instructors.json`
- **表格**: `instructors`
- **數量**: 多位滑雪教練
- **格式**: 包含姓名、簡介、證照等

### 雪場資料
- **來源**: 內建清單
- **表格**: `parks`
- **數量**: 8 個日本知名雪場
- **格式**: 包含中英文名稱、位置等

## 系統架構

```
前端
├─ index.php (重定向)
├─ articleList.php (首頁 - 文章列表)
├─ article.php (單篇文章)
├─ instructorList.php (教練列表)
└─ instructor.php (單一教練)

後端
├─ bkAdmin/articles.php (文章管理)
├─ bkAdmin/instructors.php (教練管理)
└─ bkAdmin/parks.php (雪場管理)

資料庫
└─ data/skidiyog.db (SQLite)
    ├─ articles 表
    ├─ instructors 表
    └─ parks 表
```

## 主要頁面說明

### 前台頁面

#### articleList.php (首頁)
- URL: `/` 或 `/articleList.php`
- 功能: 顯示所有滑雪文章的縮圖和標題
- 外觀: 和 diy.ski 首頁一樣
- 使用框架: Materialize CSS

#### article.php (文章內頁)
- URL: `/article.php?idx={文章ID}`
- 功能: 顯示單篇文章的完整內容
- 包含: 標題、內容、相關文章等

#### instructorList.php (教練列表)
- URL: `/instructorList.php`
- 功能: 顯示所有教練的列表

### 後台頁面

#### bkAdmin/articles.php
- 功能: 新增、編輯、刪除文章
- 權限: 管理員

#### bkAdmin/instructors.php
- 功能: 新增、編輯、刪除教練資料
- 權限: 管理員

## 常見問題

### Q: 為什麼首頁沒有顯示文章？
A: 請先執行資料匯入腳本：`https://skidiyog.zeabur.app/import-all-data.php`

### Q: 文章圖片無法顯示？
A: 文章圖片位於 `/photos/articles/{idx}/{idx}.jpg`，請確保圖片檔案存在

### Q: 後台如何登入？
A: 原系統的帳號登入功能保留，使用原有的帳號密碼

### Q: 如何新增文章？
A: 訪問 `/bkAdmin/articles.php`，點擊「新增文章」按鈕

### Q: 資料庫檔案在哪裡？
A: 位於 `data/skidiyog.db`，這個檔案會自動生成，不需要手動創建

## 技術細節

### SQLite 資料庫
```
位置: data/skidiyog.db
格式: SQLite 3
字元編碼: UTF-8
自動建表: 是（首次連接時）
```

### 資料表結構

#### articles 表
```sql
idx INTEGER PRIMARY KEY
title TEXT                 -- 文章標題
tags TEXT                  -- 標籤（逗號分隔）
article TEXT               -- 文章內容（HTML）
keyword TEXT               -- 關鍵詞
timestamp DATETIME         -- 建立時間
created_at DATETIME        -- 系統建立時間
updated_at DATETIME        -- 更新時間
```

#### instructors 表
```sql
idx INTEGER PRIMARY KEY
name TEXT                  -- 英文名稱
cname TEXT                 -- 中文名稱
content TEXT               -- 教練簡介
photo TEXT                 -- 照片路徑
created_at DATETIME
updated_at DATETIME
```

#### parks 表
```sql
idx INTEGER PRIMARY KEY
name TEXT                  -- 英文名稱
cname TEXT                 -- 中文名稱
location TEXT              -- 位置
description TEXT           -- 描述
...（更多欄位）
```

## 維護建議

1. **定期備份資料庫**
   ```bash
   cp data/skidiyog.db data/skidiyog.db.backup.$(date +%Y%m%d)
   ```

2. **監控資料庫大小**
   - SQLite 適合中小型資料量
   - 如果文章數量超過 10,000 篇，考慮優化

3. **圖片管理**
   - 文章圖片應存放在 `/photos/articles/{idx}/` 目錄
   - 縮圖命名: `{idx}.jpg`

4. **日誌檢查**
   - 檢查 Zeabur 部署日誌
   - 檢查 PHP 錯誤日誌

## 支援

如有問題，請檢查：
1. Zeabur 部署日誌
2. 資料庫是否已匯入資料
3. PHP 版本是否相容（需要 PHP 7.4+）
4. SQLite 擴展是否啟用
