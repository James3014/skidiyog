# SKidiyog - Legacy Ski Lesson System

**Status**: Backup for independent testing & migration
**Repository**: https://github.com/James3014/skidiyog
**Deployment**: Ready for Zeabur or traditional hosting
**Last Updated**: 2025-11-06

---

## 快速開始 (Quick Start)

### 1️⃣ 選擇部署方式

**推薦**: Zeabur (與新 FAQ 系統一致，自動部署)
- 查看 `DEPLOYMENT_GUIDE.md` → 第 3 章 "Zeabur 部署步驟"

**備選**: 傳統虛擬主機 (低成本，需手動設置)
- 查看 `DEPLOYMENT_GUIDE.md` → 第 4 章 "傳統虛擬主機部署"

**高級**: Docker + VPS (完全控制)
- 查看 `DEPLOYMENT_GUIDE.md` → 第 5 章 "Docker 部署"

### 2️⃣ 執行部署檢查表

按照 `DEPLOYMENT_CHECKLIST.md` 的步驟逐一驗證:
- [ ] 環境準備
- [ ] 服務部署
- [ ] 部署後驗證
- [ ] 環境配置驗證

### 3️⃣ 整合 FAQ 卡片 (可選，部署後執行)

完成環境驗證後，參考 `FAQ_INTEGRATION_GUIDE.md` 集成新 FAQ 系統

---

## 項目概述 (Project Overview)

### 系統架構

```
SKidiyog 獨立系統
├── PHP 7.4+ / 8.0+ 後端
├── MySQL 數據庫
├── JSON 數據文件 (度假村、文章、導師)
└── React-free 前端 (HTML + CSS + JavaScript)

整合點
├── 度假村頁面 → FAQ 卡片
├── 後台管理 → FAQ 插入界面
└── API → 新系統 (faq.diy.ski) 連接
```

### 核心功能

✅ **度假村信息管理**
- 40+ 度假村詳細信息
- 多語言支持 (中文/英文/日文)
- 部分動態內容，部分靜態 JSON

✅ **文章發布系統**
- 新聞與更新文章
- 分類與搜尋
- 後台編輯界面

✅ **導師資訊**
- 導師檔案與背景
- 課程安排
- 評價與反饋

✅ **用戶帳戶**
- 註冊與登入
- 個人信息管理
- 預訂歷史

✅ **後台管理**
- 度假村編輯
- 文章管理
- 訂單查詢
- FAQ 卡片插入 (新增)

---

## 目錄結構 (Directory Structure)

```
skidiyog/
├── README.md                     # 本文件
├── DEPLOYMENT_GUIDE.md          # 部署指南 (Zeabur/傳統/Docker)
├── DEPLOYMENT_CHECKLIST.md      # 部署檢查表
├── FAQ_INTEGRATION_GUIDE.md     # FAQ 集成指南
├── .gitignore                   # Git 忽略規則
│
├── park.php                     # 度假村詳細頁面 + FAQ 卡片
├── article.php                  # 文章詳細頁面
├── articleList.php              # 文章列表
├── instructor.php               # 導師信息
├── index.php                    # 首頁
│
├── account_*.php                # 帳戶系統 (登入/註冊/密碼重置等)
├── 2fauth.php                   # 雙因素認證
│
├── bkAdmin/                     # 後台管理面板
│   ├── index.php               # 後台首頁
│   ├── parks.php               # 度假村編輯 + FAQ 插入
│   ├── articles.php            # 文章編輯
│   ├── instructors.php         # 導師管理
│   └── ...
│
├── includes/                    # 核心功能
│   ├── sdk.php                 # 系統初始化 & 配置加載
│   ├── config.php              # 數據庫配置 (不在 Git)
│   ├── config.example.php      # 配置示例
│   ├── db.class.php            # 數據庫類
│   ├── ko.class.php            # 度假村數據處理
│   ├── mj.class.php            # 文章數據處理
│   ├── crypto.class.php        # 加密工具
│   └── ...
│
├── database/                    # 數據文件
│   ├── parks.json              # 度假村數據
│   ├── articles.json           # 文章數據
│   ├── instructors.json        # 導師數據
│   └── ...
│
├── assets/                      # 靜態資源
│   ├── css/                    # 樣式表
│   ├── js/                     # JavaScript
│   │   └── faq-integration.js  # FAQ 集成 (新增)
│   ├── images/                # 圖像
│   └── uploads/               # 用戶上傳文件
│
├── .htaccess                   # Apache 重寫規則
└── ...
```

---

## 重要配置 (Critical Configuration)

### 數據庫連接

**文件**: `includes/config.php` (不在 Git，本地創建)

```php
<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'skidiyog_user');
define('DB_PASS', 'your_password_here');
define('DB_NAME', 'skidiyog');
define('SECRET_KEY', 'unique_secret_key_here');
?>
```

### 自動域名適應

**文件**: `includes/sdk.php` (Line 17-18)

```php
if(isset($_SERVER['HTTP_HOST'])){
  define("domain_name", $_SERVER['HTTP_HOST']);
}
```

✨ **這表示**：系統自動適應任何域名，無需修改代碼!

### 環境變量 (Zeabur)

```
DB_HOST = mysql_service_host
DB_USER = skidiyog_user
DB_PASS = secure_password
DB_NAME = skidiyog
SECRET_KEY = random_32_char_key
FAQ_API_URL = https://faq.diy.ski/api/v1
FAQ_ENABLED = true
```

---

## 部署流程 (Deployment Flow)

### 階段 1: 準備 (Preparation)

- [ ] Clone 或下載倉庫
- [ ] 準備虛擬主機或 Zeabur 帳戶
- [ ] 準備 MySQL 數據庫

### 階段 2: 部署 (Deployment)

- [ ] 按 `DEPLOYMENT_GUIDE.md` 部署應用
- [ ] 配置環境變量
- [ ] 導入數據 (如有備份)

### 階段 3: 驗證 (Verification)

- [ ] 使用 `DEPLOYMENT_CHECKLIST.md` 逐項驗證
- [ ] 運行 `verify-setup.php` 檢查環境
- [ ] 測試所有功能

### 階段 4: FAQ 集成 (Optional - After Verification)

- [ ] 按 `FAQ_INTEGRATION_GUIDE.md` 集成 FAQ 卡片
- [ ] 修改 `park.php` 和 `bkAdmin/parks.php`
- [ ] 提交至 GitHub，自動部署

### 階段 5: 最終遷移 (Final Migration)

- [ ] 備份原始 diy.ski 數據
- [ ] 更新 DNS
- [ ] 監測系統穩定性
- [ ] 完成遷移

---

## 技術棧 (Technology Stack)

| 組件 | 版本 | 用途 |
|------|------|------|
| PHP | 8.0+ LTS | 後端框架 |
| MySQL | 8.0+ | 數據存儲 |
| Apache | 2.4+ | Web 伺服器 |
| mod_rewrite | 已啟用 | URL 重寫 |
| jQuery | 3.x | 前端交互 |
| Foundation | 6.x | 響應式設計 |
| Node.js | 14+ | (可選) 構建工具 |
| Docker | Latest | (可選) 容器化 |

---

## 常見問題 (FAQ)

### Q1: 能否不需要原始系統數據直接部署?
**A**: 是的！skidiyog 是完全獨立的。所有數據都在 `database/` 目錄中的 JSON 文件。

### Q2: 部署後如何驗證環境配置?
**A**:
1. 訪問 `http://your-domain/verify-setup.php`
2. 檢查所有環境指標是否顯示 ✓
3. 若全部通過，環境配置就可以

### Q3: 可以同時運行新舊系統嗎?
**A**: 可以！新 FAQ 系統和舊 skidiyog 可以在不同域名上運行:
- 舊系統: `diy.ski` (skidiyog)
- 新系統: `faq.diy.ski` (Zeabur)
- 通過 API 集成，無衝突

### Q4: FAQ 卡片集成會影響性能嗎?
**A**: 不會。FAQ API 調用有 3 秒超時，若失敗會自動降級（不顯示卡片）。

### Q5: 如何回滾到原始系統?
**A**: DNS 指回原服務器即可。新舊系統獨立運行，無互相影響。

---

## 支援與文檔 (Support)

### 文檔清單

| 文檔 | 內容 | 適用階段 |
|------|------|---------|
| `DEPLOYMENT_GUIDE.md` | 詳細部署步驟 (Zeabur/傳統/Docker) | 部署階段 |
| `DEPLOYMENT_CHECKLIST.md` | 逐項驗證清單 | 部署後驗證 |
| `FAQ_INTEGRATION_GUIDE.md` | FAQ 卡片集成代碼與步驟 | 驗證完成後 |
| `CLAUDE.md` (project root) | 項目指導與規範 | 開發參考 |

### 故障排查

常見問題已在 `DEPLOYMENT_GUIDE.md` 第 10 章詳細說明:
- 白屏問題
- 404 錯誤
- 數據庫連接失敗
- 性能問題

### 聯繫方式

- **GitHub Issues**: https://github.com/James3014/skidiyog/issues
- **開發者**: James Chen
- **Email**: (待添加)

---

## 安全建議 (Security Notes)

⚠️ **部署前務必**:

1. **生成強密鑰**
   ```bash
   openssl rand -base64 32  # 用於 SECRET_KEY
   ```

2. **保護敏感文件**
   - `includes/config.php` ✗ 不提交至 Git
   - `.env` 文件 ✗ 不提交至 Git
   - 數據庫密碼 ✗ 使用環境變量

3. **啟用 HTTPS**
   - Let's Encrypt (免費)
   - 或自簽證書

4. **設置防火牆規則**
   - 限制 MySQL 端口訪問
   - 只允許必要的 IP

5. **定期備份**
   ```bash
   mysqldump -u user -p database > backup_$(date +%Y%m%d).sql
   ```

---

## 版本歷史 (Version History)

| 版本 | 日期 | 說明 |
|------|------|------|
| 1.0 | 2025-11-06 | GitHub 初始化，添加部署文檔 |
| - | - | FAQ 卡片集成 (待完成) |
| - | - | 數據遷移 (待完成) |
| - | - | 最終上線 (待完成) |

---

## 下一步 (Next Steps)

1. **立即執行**:
   - [ ] 閱讀本 README
   - [ ] 根據 `DEPLOYMENT_GUIDE.md` 選擇部署方式
   - [ ] 執行部署

2. **部署後**:
   - [ ] 按 `DEPLOYMENT_CHECKLIST.md` 驗證
   - [ ] 運行 `verify-setup.php`
   - [ ] 記錄環境配置詳情

3. **整合階段**:
   - [ ] 參考 `FAQ_INTEGRATION_GUIDE.md` 整合 FAQ
   - [ ] 修改 `park.php` 和後台
   - [ ] 提交代碼至 GitHub

4. **上線準備**:
   - [ ] 完整測試
   - [ ] 備份原系統
   - [ ] DNS 遷移計劃
   - [ ] 用戶通知

---

## 授權與維護 (License & Maintenance)

**開發者**: James Chen
**倉庫**: https://github.com/James3014/skidiyog
**状態**: 活躍維護中
**最後更新**: 2025-11-06

---

## 快速命令參考 (Quick Commands Reference)

```bash
# 本地 PHP 伺服器 (開發)
php -S localhost:8000

# 驗證環境配置
curl http://localhost:8000/verify-setup.php

# 數據庫驗證
mysql -u user -p database -e "SHOW TABLES;"

# Git 提交與推送
git add .
git commit -m "your message"
git push origin main

# Docker 本地運行
docker-compose up -d
docker-compose logs -f

# 查看日誌
tail -f /var/log/php-errors.log
tail -f /var/log/apache2/access.log
```

---

**🎉 準備好了嗎？從 `DEPLOYMENT_GUIDE.md` 開始吧！**
