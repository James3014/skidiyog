# SKidiyog 部署檢查表 (Deployment Checklist)

## 選擇部署平台 (Choose Deployment Platform)

- [ ] **Zeabur** (推薦 - Recommended) - 與新 FAQ 系統一致，自動部署
- [ ] **傳統虛擬主機** (Traditional Hosting) - 低成本但需手動設置
- [ ] **VPS + Docker** (高級 - Advanced) - 完全控制，需 DevOps 知識

---

## Zeabur 部署清單 (Zeabur Deployment)

### 準備階段 (Preparation)

- [ ] 已有 Zeabur 帳戶並登入 (https://dash.zeabur.com)
- [ ] GitHub 帳戶已連接到 Zeabur (Settings → GitHub)
- [ ] 確認 `https://github.com/James3014/skidiyog` 倉庫可訪問

### 創建專案 (Create Project)

- [ ] 新建 Zeabur 專案
- [ ] 項目名稱: `skidiyog` 或 `skidiyog-test`
- [ ] 選擇地區: 最接近用戶的區域

### 添加服務 (Add Services)

**GitHub 服務**:
- [ ] 連接 GitHub 倉庫
- [ ] 選擇 `James3014/skidiyog`
- [ ] 選擇分支: `main`
- [ ] 自動部署: 開啟

**MySQL 服務**:
- [ ] 添加 MySQL 8.0 服務
- [ ] 記下連接信息:
  - [ ] 主機: `________________`
  - [ ] 用戶名: `________________`
  - [ ] 密碼: `________________`
  - [ ] 數據庫: `skidiyog`

### 環境變量配置 (Environment Variables)

在 Zeabur 儀表板設置:

```
DB_HOST = [MySQL 主機]
DB_USER = [MySQL 用戶名]
DB_PASS = [MySQL 密碼]
DB_NAME = skidiyog
SECRET_KEY = [生成 32 字符隨機字符串]
DEPLOYMENT_ENV = staging
PHP_VERSION = 8.1
```

- [ ] 所有環境變量已設置
- [ ] SECRET_KEY 已生成並安全存儲

### PHP 運行時配置 (PHP Runtime)

- [ ] PHP 版本: 8.1 LTS
- [ ] 擴展已安裝: mysqli, json, curl, mbstring
- [ ] mod_rewrite 已啟用

### 初始部署 (Initial Deployment)

- [ ] 代碼已推送至 GitHub
- [ ] Zeabur 構建開始自動進行
- [ ] 構建日誌檢查無錯誤
- [ ] 部署完成 (綠色狀態)
- [ ] 分配的域名: `https://________________.zeabur.app`

---

## 部署後驗證 (Post-Deployment Verification)

### 訪問與功能測試 (Access & Functionality)

- [ ] 首頁可訪問: `https://your-domain.zeabur.app`
- [ ] 頁面加載時間 < 2 秒
- [ ] 所有度假村頁面正常顯示:
  - [ ] `/naeba.php`
  - [ ] `/appi.php`
  - [ ] `/nozawa.php`
  - [ ] 其他 5+ 個度假村
- [ ] 文章頁面功能正常: `/article.php?id=1`
- [ ] 導師信息頁面顯示: `/instructor.php`

### 數據庫驗證 (Database)

**使用 Zeabur MySQL 客戶端**:
- [ ] MySQL 連接成功
- [ ] 數據庫 `skidiyog` 存在
- [ ] 表結構正確:
  ```
  SHOW TABLES;
  ```
  應顯示所有必需的表格

- [ ] 數據導入完成:
  - [ ] Parks 表有數據 (至少 10 條度假村)
  - [ ] Articles 表有數據
  - [ ] Instructors 表有數據
  - [ ] Orders 表狀態正常

**驗證命令**:
```bash
# 通過 Zeabur 控制台或 MySQL 客戶端運行
SELECT COUNT(*) as parks FROM parks;  # 應 > 10
SELECT COUNT(*) as articles FROM articles;  # 應 > 5
```

- [ ] 上述查詢返回預期結果

### 後台訪問驗證 (Admin Panel)

- [ ] 訪問 `https://your-domain.zeabur.app/bkAdmin`
- [ ] 登入頁面正常顯示
- [ ] 使用測試帳號登入 (或默認帳號)
  - 用戶名: `________________`
  - 密碼: `________________`
- [ ] 進入後台主頁
- [ ] 編輯功能可用:
  - [ ] 可編輯度假村信息
  - [ ] 可管理文章
  - [ ] 可管理導師

### 性能指標 (Performance)

在瀏覽器開發工具測量:

- [ ] 首頁加載時間: `< 2 秒`
- [ ] 度假村頁面加載: `< 1.5 秒`
- [ ] 後台頁面加載: `< 2 秒`
- [ ] API 響應時間: `< 200 ms`

**測試工具**:
- Chrome DevTools Network 標籤
- Zeabur 儀表板 → Metrics

### 日誌檢查 (Log Verification)

在 Zeabur 日誌頁面:

- [ ] 無 PHP 致命錯誤
- [ ] 無數據庫連接錯誤
- [ ] 無 404 未找到 (除非故意)
- [ ] 只有預期的警告信息

**排查錯誤的日誌格式**:
```
ERROR: [timestamp] [error message]
```

---

## 環境配置驗證 (Configuration Verification)

### 檢查清單

- [ ] 運行 `verify-setup.php` 驗證 PHP 環境
  - 訪問: `https://your-domain.zeabur.app/verify-setup.php`
  - 檢查項目:
    - [ ] ✓ PHP 版本 >= 8.1
    - [ ] ✓ MySQLi 擴展已加載
    - [ ] ✓ MySQL 連接成功
    - [ ] ✓ 所有目錄權限正確

### 代碼驗證

- [ ] `.gitignore` 已正確配置 (config.php 已排除)
- [ ] `includes/config.php` 不在 GitHub 中
- [ ] `includes/config.example.php` 已提交
- [ ] 所有 PHP 文件使用 UTF-8 編碼

### 域名與 DNS

- [ ] Zeabur 分配的域名可訪問
- [ ] SSL 證書自動配置 (綠色小鎖)
- [ ] 重定向正常工作 (HTTP → HTTPS)

---

## FAQ 卡片集成部署 (FAQ Card Integration)

此步驟在環境驗證完成後進行。

### 代碼修改

- [ ] 修改 `park.php` 添加 FAQ 卡片:
  - [ ] 在 line 98 後添加 `renderFAQCards()` 函數調用
  - [ ] 確保不破壞現有內容渲染

- [ ] 修改 `bkAdmin/parks.php` 添加 FAQ 插入按鈕:
  - [ ] 在編輯區域添加 "[+] 插入 FAQ 卡片" 按鈕
  - [ ] 確保後台編輯功能仍正常

### 提交與部署

- [ ] 代碼修改本地測試完成
- [ ] 提交至 GitHub:
  ```bash
  git add park.php bkAdmin/parks.php
  git commit -m "feat: add FAQ card integration to resort pages"
  git push origin main
  ```

- [ ] 推送完成後觀察 Zeabur 自動構建:
  - [ ] 構建開始 (orange)
  - [ ] 構建完成 (green)
  - [ ] 新版本已發布

- [ ] 驗證新功能:
  - [ ] FAQ 卡片在度假村頁面顯示
  - [ ] 後台可插入 FAQ 卡片
  - [ ] 保存後頁面正常顯示

---

## 環境配置驗證完成 (Configuration Verified)

當所有上述檢查項都已完成且通過時:

- [ ] **環境配置驗證: ✓ 通過**
- [ ] **系統部署狀態: 生產就緒**
- [ ] **下一步: 數據遷移與最終測試**

### 記錄重要信息

記錄以下信息用於最終遷移:

```
Zeabur 部署詳情
================
專案名稱: ________________
Zeabur 域名: ________________
MySQL 主機: ________________
MySQL 用戶: ________________
數據庫名: ________________
部署日期: ________________
部署者: ________________
```

---

## 最終遷移檢查表 (Final Migration Checklist)

當環境驗證完成後，進行最終遷移:

- [ ] 所有環境變量已確認
- [ ] 備份原始 diy.ski 數據 (完整數據庫備份)
- [ ] DNS 更改計劃已制定
- [ ] 回滾計劃已準備
- [ ] 遷移時間窗口已確認 (非高峰期)
- [ ] 用戶通知已發送 (如有必要)
- [ ] 2FA/認證協調已完成
- [ ] 最終測試周期已通過

### 遷移執行

- [ ] DNS 指向新服務器
- [ ] 監控告警已配置
- [ ] 用戶反饋通道已開啟
- [ ] 監測首小時的錯誤率
- [ ] 驗證支付/訂單系統正常
- [ ] 遷移完成標記

---

## 故障回滾步驟 (Rollback Procedure)

如果遷移出現問題:

1. **立即停止**: 停止繼續修改
2. **監測**: 檢查錯誤日誌
3. **決策**: 評估是否需要回滾
4. **執行回滾**:
   - [ ] 將 DNS 指回原服務器
   - [ ] 驗證原系統恢復正常
   - [ ] 通知相關人員
5. **事後分析**: 確定根本原因

---

## 完成

**檢查表完成日期**: `________________`
**檢查人員**: `________________`
**審核人員**: `________________`

---

**參考文檔**: `DEPLOYMENT_GUIDE.md`
**GitHub 倉庫**: https://github.com/James3014/skidiyog
**支持**: 提交 GitHub Issues 或聯繫開發團隊
