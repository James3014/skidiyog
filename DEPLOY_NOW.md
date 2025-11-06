# 🚀 立即部署（選擇 1 - 原始 AWS RDS）

**預計時間**: 5 分鐘
**目標**: 快速上線前台 + 後台編輯功能

---

## 第 1 步: 進入 Zeabur 儀表板

**URL**: https://dash.zeabur.com

登入您的 Zeabur 帳戶

---

## 第 2 步: 進入 SKidiyog 專案設置

1. 點擊 "skidiyog" 專案
2. 進入專案後，點擊左側的 "skidiyog" 應用（PHP 應用）
3. 點擊 "Settings" (設置)

---

## 第 3 步: 添加環境變量

點擊 "Environment Variables" 頁籤

**複製粘貼以下 4 個環境變量**:

```
DB_HOST = skidiy-rds-master.cgseduwrbkzc.ap-northeast-1.rds.amazonaws.com
DB_USER = dba
DB_PASS = dba_Skidiy66
DB_NAME = skidiyog
```

**步驟**:
1. 點擊 "Add Variable" 按鈕
2. 輸入變量名（例: `DB_HOST`）
3. 輸入值（例: `skidiy-rds-master.cgseduwrbkzc.ap-northeast-1.rds.amazonaws.com`）
4. 點擊 "Add"
5. 重複 4 次（4 個變量）

或者，如果有 "Paste" 或 "Bulk" 選項，直接粘貼上面的 4 行。

---

## 第 4 步: 觸發重新部署

1. 點擊左側的 "Deployments" 頁籤
2. 找到最新的部署（最上面的一條）
3. 點擊右邊的 "..." (三個點)
4. 選擇 "Redeploy" (重新部署)

**狀態變化**:
- 🟠 Orange = 正在構建 (1-2 分鐘)
- 🟢 Green = 部署完成 ✓

---

## 第 5 步: 驗證部署成功（打開這些 URL）

等待部署完成後（看到 Green 綠色狀態），打開以下 URL：

### 前台 (公開訪問)
```
https://skidiyog.zeabur.app/
```
應顯示: 首頁正常

```
https://skidiyog.zeabur.app/park.php?name=naeba
```
應顯示: Naeba 雪場的介紹頁面

### 後台 (無需登入)
```
https://skidiyog.zeabur.app/bkAdmin/parks.php
```
應顯示: 雪場編輯頁面（有下拉菜單和編輯框）

```
https://skidiyog.zeabur.app/bkAdmin/articles.php
```
應顯示: 文章編輯頁面

### 診斷
```
https://skidiyog.zeabur.app/verify-setup.php
```
應顯示: 環境檢查報告，全部 ✓

---

## ✅ 完成標誌

當您看到以下狀態時，部署成功：

- ✓ Deployments 顯示 Green (綠色) 狀態
- ✓ 首頁 https://skidiyog.zeabur.app/ 正常加載
- ✓ 後台 https://skidiyog.zeabur.app/bkAdmin/parks.php 可訪問
- ✓ verify-setup.php 顯示所有 ✓

---

## 📝 編輯內容

部署完成後，您可以：

### 編輯度假村介紹
1. 訪問 https://skidiyog.zeabur.app/bkAdmin/parks.php
2. 在下拉菜單選擇要編輯的度假村（例: naeba）
3. 選擇要編輯的章節（例: about - 介紹）
4. 編輯文字內容
5. 點擊提交按鈕

### 編輯文章
1. 訪問 https://skidiyog.zeabur.app/bkAdmin/articles.php
2. 選擇要編輯的文章
3. 編輯標題、內容
4. 點擊提交

---

## ❌ 如果出現問題

### 部署失敗 (紅色狀態)
1. 點擊部署查看錯誤日誌
2. 檢查環境變量是否都設置正確
3. 重新部署 (Redeploy)

### 頁面無法訪問 (404 或 502)
1. 檢查部署狀態是否是 Green (綠色)
2. 等待 2-3 分鐘後重試
3. 清除瀏覽器緩存 (Ctrl+Shift+Delete)

### verify-setup.php 顯示紅色 ✗
1. 檢查環境變量是否設置正確
2. 檢查 AWS RDS 是否在線
3. 查看錯誤信息

---

## 🎉 完成！

當所有 URL 都可以正常訪問時，您的系統已經上線！

**可以做的事**:
- 前台用戶可以瀏覽所有雪場信息
- 後台可以編輯雪場介紹和文章
- 無需登入，直接訪問

**下一步** (可選):
- 整合 FAQ 卡片: 參考 FAQ_INTEGRATION_GUIDE.md
- 準備最終遷移: 參考 DEPLOYMENT_CHECKLIST.md

---

**預計完成時間**: 5-10 分鐘

現在就開始吧！👍
