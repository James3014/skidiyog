# SKidiyog 快速部署指南 (無登入版)

**目標**: 前台看介紹、後台改文章 - 無需登入功能
**部署時間**: 5 分鐘
**GitHub**: https://github.com/James3014/skidiyog

---

## ✅ 系統已準備好

✓ 後台已移除所有登入檢查
✓ config.php 已簡化，無認證邏輯
✓ verify-setup.php 可驗證環境
✓ 代碼已推送至 GitHub

---

## 🚀 3 步驟快速部署

### 步驟 1: Zeabur 儀表板設置 (2 分鐘)

進入: https://dash.zeabur.com

1. 進入 `skidiyog` 專案
2. 點擊 "Settings" → "Environment Variables"
3. **添加這些變量**:

```
DB_HOST = [您的 MySQL Host]
DB_USER = [您的 MySQL Username]
DB_PASS = [您的 MySQL Password]
DB_NAME = skidiyog
```

### 步驟 2: 觸發重新部署 (1 分鐘)

1. 進入 "Deployments" 頁籤
2. 點擊最新部署旁的 "..." → "Redeploy"
3. 等待部署完成 (橘色 → 綠色，2-3 分鐘)

### 步驟 3: 驗證部署 (2 分鐘)

**訪問這些 URL**:

| URL | 預期結果 |
|-----|--------|
| https://skidiyog.zeabur.app/ | 首頁正常顯示 |
| https://skidiyog.zeabur.app/park.php?name=naeba | Naeba 度假村介紹 |
| https://skidiyog.zeabur.app/bkAdmin/parks.php | 後台雪場編輯頁面 |
| https://skidiyog.zeabur.app/bkAdmin/articles.php | 後台文章編輯頁面 |
| https://skidiyog.zeabur.app/verify-setup.php | 環境驗證報告 |

---

## 📋 測試清單

部署完成後逐項檢查:

- [ ] https://skidiyog.zeabur.app/ - 首頁正常加載
- [ ] https://skidiyog.zeabur.app/park.php?name=naeba - 度假村頁面顯示
- [ ] https://skidiyog.zeabur.app/park.php?name=appi - 不同度假村可訪問
- [ ] https://skidiyog.zeabur.app/park.php?name=hakuba - 多個度假村測試通過
- [ ] https://skidiyog.zeabur.app/bkAdmin/parks.php - 後台度假村編輯頁面可訪問
- [ ] https://skidiyog.zeabur.app/bkAdmin/articles.php - 後台文章編輯頁面可訪問
- [ ] https://skidiyog.zeabur.app/bkAdmin/menu.php - 後台菜單正常
- [ ] https://skidiyog.zeabur.app/verify-setup.php - 環境驗證全部 ✓

---

## ❌ 常見問題

### Q1: 頁面顯示空白或錯誤
**原因**: MySQL 未連接
**解決**:
1. 檢查環境變量是否正確設置
2. 確認 MySQL 服務已在線 (Zeabur 儀表板)
3. 訪問 verify-setup.php 查看詳細錯誤

### Q2: 後台頁面無法訪問 (404)
**原因**: .htaccess 重寫規則未啟用或代碼未部署
**解決**:
1. 檢查 Deployments 是否已完成 (綠色狀態)
2. 重新部署 (Redeploy)
3. 清除瀏覽器緩存 (Ctrl+Shift+Delete)

### Q3: verify-setup.php 無法訪問 (404)
**原因**: 代碼還在構建中
**解決**:
1. 等待 5-10 分鐘
2. 重新刷新頁面
3. 檢查 Deployments 狀態

---

## 📊 系統功能

### 前台 (無需登入)
✓ 查看所有度假村介紹
✓ 查看度假村信息、交通、票價等
✓ 查看文章列表
✓ 響應式設計，支持手機

### 後台 (無需登入)
✓ 編輯度假村介紹文字
✓ 編輯文章
✓ 選擇度假村、文章後直接編輯
✓ 簡單易用的表單界面

### 無需功能
✗ 用戶登入/註冊
✗ 訂單管理
✗ 教練管理
✗ 統計報表

---

## 🔍 驗證工具

**verify-setup.php** 會檢查:

```
PHP 版本 ≥ 8.1
MySQL 擴展已加載
MySQL 連接成功
文件和目錄權限正確
環境變量已設置
```

訪問: https://skidiyog.zeabur.app/verify-setup.php

---

## 🎯 下一步 (可選)

部署完成且功能驗證無誤後:

### 方案 A: 整合 FAQ 卡片
參考: **FAQ_INTEGRATION_GUIDE.md**
- 修改 park.php 添加 FAQ 卡片
- 修改後台添加 FAQ 插入按鈕
- 預計: 2-3 小時

### 方案 B: 最終遷移到生產
參考: **DEPLOYMENT_CHECKLIST.md**
- 備份原始 diy.ski 數據
- 更新 DNS
- 監測系統穩定性
- 預計: 1-2 小時

---

## 💡 簡化說明

**為什麼這麼快?**

原始系統設計就沒有真正的登入檢查:
- 後台頁面直接訪問
- 沒有 session 驗證
- 所有編輯功能都是開放的

**數據庫**:
- 使用原始 AWS RDS (或您指定的 MySQL)
- 所有度假村和文章數據保留
- 完全兼容

**簡化**:
- 移除了無用的認證代碼
- 簡化 config.php
- 添加 verify-setup.php 用於故障排查

---

## 🔗 相關文檔

| 文檔 | 用途 |
|-----|------|
| **QUICK_DEPLOY.md** | 本文檔 - 快速部署指南 |
| ZEABUR_SETUP_REQUIRED.md | 詳細環境變量設置 |
| DEPLOYMENT_GUIDE.md | 完整部署指南 |
| FAQ_INTEGRATION_GUIDE.md | FAQ 卡片集成 |
| DEPLOYMENT_CHECKLIST.md | 最終遷移清單 |

---

## 📞 故障排查

1. **訪問 verify-setup.php** - 最直接的診斷
2. **檢查 Zeabur Logs** - 儀表板 → Logs
3. **清除緩存** - Ctrl+Shift+Delete
4. **重新部署** - Redeploy

---

**預計部署時間**: 5-10 分鐘

**現在就開始吧！** 🚀

進入 Zeabur 儀表板添加環境變量，然後等待部署完成。
