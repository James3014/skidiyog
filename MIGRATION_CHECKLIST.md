# 網域搬家檢查清單（Linus 原則）

## 三個階段區分

### 🟢 **BEFORE 搬家前（現在就要做）**
這些**不涉及新網域**，純粹是準備工作。**可以立即開始！**

### 🟡 **DURING 搬家當天（搬家前 24 小時到搬家後 24 小時）**
需要協調新舊伺服器，有明確的時間窗口。

### 🔴 **AFTER 搬家後（搬家後 30-90 天）**
長期監控和維護，可以慢慢做。

---

## 🟢 BEFORE：搬家前現在就能做（0 成本、無風險）

### ✅ 立即可做（這週）
- [ ] **備份現有資料庫**
  ```bash
  # 在當前伺服器執行
  mysqldump -u user -p diyski > backup_$(date +%Y%m%d).sql
  sqlite3 diyski.db ".backup backup_$(date +%Y%m%d).db"
  ```

- [ ] **導出所有 URL**
  ```bash
  # 訪問並儲存 sitemap
  curl https://diy.ski/sitemap.xml.php > urls_current.txt
  ```

- [ ] **截圖現有流量基線**
  - Google Analytics：過去 30 天的流量、用戶數、跳出率
  - Google Search Console：排名、點擊率、曝光次數
  - PageSpeed Insights：行動/桌面評分
  - 這些是搬家後對比的基準

- [ ] **整理 URL 對應表**（如果搬家後 URL 結構要改變）
  ```
  舊 URL                          新 URL
  /park.php?name=naeba      ->    /parks/naeba/
  /article.php?idx=1        ->    /articles/1/

  用 Excel/CSV 記錄所有映射
  ```

### ✅ 搬家前 2 週內
- [ ] **購買新網域**（如：skidiy-new.com 或 skidiy.jp）
  - 保持簡單（符合 Linus 原則）
  - 確保 WHOIS 隱私設置正確

- [ ] **檢查 SSL 憑證計畫**
  - 新網域需要 HTTPS
  - 計畫用 Let's Encrypt（免費）還是付費 SSL

- [ ] **選擇新伺服器商**（Zeabur、Vercel、傳統 VPS）
  - 確認支援 PHP + MySQL/SQLite
  - 確認有 SSH 存取權限
  - 確認有 cron job 支持（如果需要定時任務）

- [ ] **準備遷移清單**
  ```
  需要複製的檔案：
  ✅ /park.php, /article.php, /parkList.php 等 PHP 檔案
  ✅ /includes/ 目錄（SDK、元件等）
  ✅ /photos/ 目錄（所有圖片）
  ✅ /assets/ 目錄（CSS、JS、字體）
  ✅ config.php 或 .env（環境變數）
  ✅ .htaccess（重定向規則）

  需要備份的資料：
  ✅ 資料庫（MySQL dump 或 SQLite 備份）
  ✅ 現有 robots.txt
  ✅ 現有 .htaccess
  ```

### ✅ 搬家前 1 週
- [ ] **驗證現有程式碼可以部署**
  ```bash
  # 在本地或測試伺服器試著 clone 和執行
  git clone https://github.com/James3014/skidiyog.git test-deploy
  cd test-deploy
  # 檢查是否有缺少的依賴或設定
  ```

- [ ] **記錄所有外部依賴和 API**
  - faq.diy.ski 的 API 端點
  - Google Analytics ID
  - Google Search Console 驗證方式
  - 任何第三方服務（如 payment gateway）

- [ ] **準備新伺服器的帳號資訊**
  - 取得 SSH 帳號密碼/金鑰
  - 取得 FTP 帳號（如果使用）
  - 取得 MySQL/資料庫 root 帳號

---

## 🟡 DURING：搬家當天（協調時間窗口）

### 搬家前 24 小時
- [ ] **通知相關人員**
  - 告知用戶可能短期無法訪問
  - 準備維護頁面內容

- [ ] **在舊伺服器設置維護頁面**
  ```php
  // 在 index.php 最上面
  if (date('Y-m-d H:i') >= '2025-01-15 10:00' && date('Y-m-d H:i') < '2025-01-15 14:00') {
    header('HTTP/1.1 503 Service Unavailable');
    echo "我們正在進行網域遷移，將於下午 2 時完成。感謝耐心等候！";
    exit();
  }
  ```

### 搬家當天執行（假設上午 10 時開始）
- [ ] **1. 停止舊伺服器對外寫入操作**
  - 如果有即時資料更新，先關閉或導到維護頁面

- [ ] **2. 執行最後一次資料庫備份**
  ```bash
  mysqldump -u user -p diyski > backup_final_$(date +%Y%m%d_%H%M%S).sql
  ```

- [ ] **3. 在新伺服器部署程式碼**
  ```bash
  # 登入新伺服器
  ssh user@new-server.com
  cd /var/www/html
  git clone https://github.com/James3014/skidiyog.git .

  # 或用 SCP
  scp -r local-copy/* user@new-server:/var/www/html/
  ```

- [ ] **4. 複製資料庫到新伺服器**
  ```bash
  # 在新伺服器建立資料庫
  mysql -u root -p < backup_final.sql
  ```

- [ ] **5. 複製 photos 和 assets**
  ```bash
  scp -r /local/photos user@new-server:/var/www/html/
  scp -r /local/assets user@new-server:/var/www/html/
  ```

- [ ] **6. 更新新伺服器的環境設定**
  ```php
  // config.php 更新
  define("domain_name", "skidiy-new.com");  // 新網域
  ```

- [ ] **7. 測試新伺服器（使用 hosts 檔案）**
  ```bash
  # 在您的電腦 /etc/hosts 添加
  [新伺服器IP] skidiy-new.com

  # 訪問新伺服器
  curl https://skidiy-new.com/
  # 應該看到首頁內容
  ```

- [ ] **8. 切換 DNS**
  - 登入網域管理商
  - 將 A 記錄指向新伺服器 IP
  - 設置 www CNAME
  - 預期 DNS 傳播時間：5 分鐘 - 48 小時

- [ ] **9. 舊伺服器設置 301 重定向**
  ```apache
  # 舊伺服器的 .htaccess
  RewriteEngine On
  RewriteRule ^(.*)$ https://skidiy-new.com/$1 [L,R=301]
  ```

- [ ] **10. 驗證新伺服器回應（等待 DNS 傳播）**
  ```bash
  # 檢查 DNS 是否已更新
  nslookup skidiy-new.com

  # 測試頁面
  curl https://skidiy-new.com/park.php?name=naeba
  ```

---

## 🔴 AFTER：搬家後（持續 30-90 天）

### 搬家後 1 小時內
- [ ] **驗證基本功能**
  - [ ] 首頁加載
  - [ ] 雪場列表可訪問
  - [ ] 文章頁面可訪問
  - [ ] 圖片正常載入
  - [ ] 預約連結正確

- [ ] **檢查資料庫連接**
  ```bash
  curl https://skidiy-new.com/parkList.php | grep -c "naeba"
  # 應該找到雪場名稱
  ```

### 搬家後 24 小時內
- [ ] **在 Google Search Console 添加新網域**
  - 驗證所有權（HTML 檔案或 DNS）

- [ ] **提交 Address Change（網址變更）**
  - Google Search Console > 設定 > 網址變更
  - 舊網址 → 新網址

- [ ] **提交新 sitemap**
  ```
  GSC > Sitemaps > 新增 sitemap
  URL: https://skidiy-new.com/sitemap.xml.php
  ```

- [ ] **在 Bing Webmaster Tools 做相同操作**

### 搬家後 1 週
- [ ] **監控 Google Search Console**
  - 新網域的「已探索」和「已編入索引」數量
  - 檢查爬蟲錯誤（404、重定向鏈等）
  - 預期：70-80% 的頁面在一週內被重新索引

- [ ] **監控 Analytics**
  - 新網域的流量
  - 用戶行為是否正常
  - 與搬家前基線對比

- [ ] **檢查排名波動**
  - 搬家後 1-3 週是正常波動期
  - 預期排名會暫時下降 1-5 位，然後逐漸恢復

### 搬家後 1 個月
- [ ] **評估搬家成功指標**
  ```
  ✅ 流量恢復到 80-100%（相對搬家前）
  ✅ 索引數達 95%+
  ✅ 無重定向鏈或 404
  ✅ 排名開始恢復
  ```

- [ ] **如果需要，保留舊網域 3-6 個月**
  - 繼續 301 重定向
  - 保留 SSL 憑證有效

### 搬家後 3 個月
- [ ] **完整評估**
  ```
  最終成功指標：
  ✅ 流量回到 100%
  ✅ 排名基本恢復原位
  ✅ 沒有持續的索引問題
  ```

- [ ] **決定是否關閉舊伺服器**
  - 如果流量完全遷移，可關閉舊伺服器
  - 建議保留舊網域至少 1 年，做 301 重定向

---

## 📋 搬家前最重要的 5 件事（優先順序）

1. **備份資料庫和檔案** ⭐⭐⭐⭐⭐
   - 無法恢復 = 遺失所有資料
   - 今天就做！

2. **記錄現有流量基線** ⭐⭐⭐⭐
   - 用來判斷搬家是否成功
   - 需要 Analytics 截圖

3. **準備 URL 重定向規則** ⭐⭐⭐⭐
   - 如果 URL 結構改變，需要 301 對應表
   - 搬家當天快速執行

4. **測試程式碼可部署性** ⭐⭐⭐
   - 確保 git clone 可正常執行
   - 確保沒有缺少的依賴

5. **購買新網域 + 選伺服器** ⭐⭐⭐
   - 需要時間等待 DNS 傳播
   - 提前 2 週準備

---

## ✅ 您現在可以開始做的（無需等待）

```
這週：
☐ 備份資料庫
☐ 導出現有 sitemap
☐ 截圖 Analytics 基線
☐ 清點需要遷移的檔案

下週：
☐ 購買新網域
☐ 評估新伺服器商
☐ 準備 URL 重定向對應表
☐ 檢查程式碼可部署性

搬家前 1 週：
☐ 獲取新伺服器帳號資訊
☐ 測試新伺服器環境
☐ 準備 config 和環境變數
☐ 設置維護頁面
```

---

## 💡 Linus 原則應用

| 原則 | 搬家前做什麼 |
|------|-----------|
| **Keep It Simple** | 不要改 URL 結構，保持原樣搬 |
| **資料優先** | 先備份，再任何其他操作 |
| **測試充分** | 本地測試成功，再搬家 |
| **可回滾** | 保留舊伺服器 3-6 個月 |
| **監控是防守** | 記錄基線，搬家後才知道有沒有問題 |

---

## 🎯 搬家前最少清單（MUST DO）

如果時間緊張，**至少要做這些**（按優先順序）：

1. ☐ 備份資料庫（`mysqldump`）
2. ☐ 備份 photos 和 assets 目錄
3. ☐ 截圖 Google Analytics（過去 30 天）
4. ☐ 截圖 Google Search Console 排名
5. ☐ 購買新網域
6. ☐ 選定新伺服器商

**搬家當天**：
7. ☐ 在新伺服器部署程式碼
8. ☐ 恢復資料庫
9. ☐ 切換 DNS
10. ☐ 在舊伺服器設置 301 重定向
11. ☐ 在 GSC 提交 Address Change

**搬家後監控**：
12. ☐ 每天檢查 GSC（一週）
13. ☐ 監控 Analytics（一個月）
