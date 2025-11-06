# Zeabur AI 助手部署指南

**目的**: 使用 Zeabur 內建 AI 助手自動配置環境變量和部署應用

---

## 🤖 步驟 1: 打開 Zeabur 儀表板

進入: https://dash.zeabur.com

---

## 步驟 2: 尋找 AI 助手按鈕

在 Zeabur 儀表板右下角或左下角，你會看到：

```
💬 AI Chat / Ask AI 按鈕
或
🤖 AI Assistant 按鈕
```

**點擊它開啟 AI 助手對話框**

---

## 步驟 3: 告訴 AI 你要做什麼

在 AI 助手的對話框中輸入：

```
我有一個 PHP MySQL 應用，需要：
1. 設置 MySQL 環境變量
2. 導入 JSON 數據到數據庫
3. 重新部署應用

MySQL 服務已經在運行
應用叫 skidiyog
請幫我自動配置
```

或更簡單的：

```
幫我配置 skidiyog PHP 應用連接到 MySQL 數據庫並部署
```

---

## 步驟 4: AI 會自動幫你

AI 助手會：

1. ✅ 讀取你的 MySQL 服務配置
2. ✅ 自動設置環境變量：
   - DB_HOST
   - DB_USER
   - DB_PASS
   - DB_NAME
   - DB_PORT

3. ✅ 建議重新部署
4. ✅ 幫你驗證配置

---

## 📋 如果 AI 不確定，告訴它：

```
MySQL 服務信息：
- Host: mysql-xxx.zeabur.app
- Port: 3306
- Username: root
- Password: your_password_here
- Database: skidiyog

請設置這些環境變量到 skidiyog PHP 應用
```

---

## ✅ 完成後

AI 會提示你：

1. 環境變量已設置 ✓
2. 應用已重新部署 ✓
3. 訪問 verify-setup.php 驗證

然後訪問：
```
https://skidiyog.zeabur.app/verify-setup.php
```

應該看到：
```
✓ MySQL Connected Successfully
✓ Tables Found: 3
```

---

## 💡 常見提示詞

### 提示詞 1: 完整設置
```
我需要在 Zeabur 上完全設置 skidiyog 應用：
1. PHP 應用連接到 MySQL 服務
2. 設置所有必要的環境變量
3. 重新部署應用
請自動幫我完成
```

### 提示詞 2: 故障排除
```
我的 skidiyog 應用返回 503 錯誤
MySQL 服務已在運行
環境變量需要設置
請幫我診斷並修復
```

### 提示詞 3: 驗證部署
```
幫我驗證 skidiyog 應用是否正確連接到 MySQL
並告訴我接下來要做什麼
```

---

## 🔍 如果 AI 找不到你的服務

告訴 AI：

```
我的專案是 skidiyog
PHP 應用服務名稱: skidiyog (或你看到的確切名稱)
MySQL 服務已部署
請連接它們並設置環境變量
```

---

## ⚠️ 如果 AI 建議有誤

你可以手動設置：

1. 進入 **skidiyog** PHP 應用
2. 點擊 **Settings** → **Environment Variables**
3. 手動添加這 5 個變量：

```
DB_HOST = (from MySQL service)
DB_USER = root
DB_PASS = (from MySQL service)
DB_NAME = skidiyog
DB_PORT = 3306
```

4. 點擊 **Redeploy**

---

## 🎯 最終驗證

部署完成後，訪問這些 URL：

| URL | 預期結果 |
|-----|---------|
| https://skidiyog.zeabur.app/verify-setup.php | ✓ MySQL Connected |
| https://skidiyog.zeabur.app/ | 首頁顯示雪場 |
| https://skidiyog.zeabur.app/bkAdmin/parks.php | 後台編輯頁面 |

---

**祝你使用 Zeabur AI 助手順利！** 🚀
