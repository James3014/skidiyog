# 響應式設計規範 - 最終版本

## 設計原則

使用 **CSS-first media query** 而非 Materialize 的 hide-on-* 類。原因：
- Materialize hide-on-* 在邊界值（600px, 992px）可能有歧義
- CSS media query 更精確、無重疊

## 斷點定義

| 寬度 | 名稱 | leftnav-mobile | leftnav--desktop | resort-content |
|------|------|---|---|---|
| < 600px | 手機 | ✅ 顯示 (display:block) | ❌ 隱藏 (display:none) | `col s12` (100%) |
| 600-992px | 平板 | ✅ 顯示 (display:block) | ❌ 隱藏 (display:none) | `col s12 m12` (100%) |
| ≥ 993px | 桌面 | ❌ 隱藏 (display:none) | ✅ 顯示 (col l3, 25%) | `col s12 m12 l9` (75%) |

## CSS 規則

### Mobile Navigation (leftnav-mobile)
```css
.leftnav-mobile {
  display: block;  /* 默認顯示 */
}
@media (min-width: 993px) {
  .leftnav-mobile {
    display: none !important;  /* 桌面隱藏 */
  }
}
```

### Desktop Sidebar (leftnav--desktop)
```css
@media (max-width: 992px) {
  .leftnav--desktop {
    display: none !important;  /* 平板/手機隱藏 */
  }
}
```

### Grid Classes in HTML
```html
<!-- Desktop sidebar: 25% width -->
<div class="col l3 leftnav leftnav--desktop">
  <!-- Only visible on ≥ 993px -->
</div>

<!-- Content: responsive width -->
<div class="col s12 m12 l9 resort-content">
  <!-- s12: < 600px (100%) -->
  <!-- m12: 600-992px (100%) -->
  <!-- l9: ≥ 993px (75%) -->
</div>
```

## 佈局流程

### < 600px（手機）
```
┌─────────────────┐
│  Navigation Bar │  (sticky top: 74px)
├─────────────────┤
│  Hero Section   │
├─────────────────┤
│  Mobile Nav     │  (leftnav-mobile, sticky top: 74px)
├─────────────────┤
│  Content (100%) │  (col s12)
└─────────────────┘
```

### 600-992px（平板）
```
┌──────────────────────┐
│  Navigation Bar      │  (sticky top: 74px)
├──────────────────────┤
│  Hero Section        │
├──────────────────────┤
│  Mobile Nav (100%)   │  (leftnav-mobile, sticky top: 74px)
├──────────────────────┤
│  Content (100%)      │  (col s12 m12)
└──────────────────────┘
```

### ≥ 993px（桌面）
```
┌────────────────────────────────────────┐
│        Navigation Bar                  │  (sticky top: 74px)
├────────────────────────────────────────┤
│        Hero Section                    │
├────────────────────────────────────────┤
│  Sidebar (25%) │ Content (75%)         │  (col l3 + col l9)
│  (sticky top   │ (resort-content)      │
│   40px)        │                       │
│                │                       │
│  - Logo        │  - Section 1          │
│  - Resort Name │  - Section 2          │
│  - Nav Links   │  - Section 3          │
│                │                       │
└────────────────────────────────────────┘
```

## 檢查清單

### 手機 (< 600px)
- [ ] Navigation bar 正常顯示，sticky
- [ ] Hero 圖片完整顯示
- [ ] Mobile nav chips 水平滾動，全寬
- [ ] 內容完整顯示，無遮擋
- [ ] 所有文字可讀

### 平板 (600-992px)
- [ ] Navigation bar 正常顯示
- [ ] Hero 圖片完整
- [ ] Mobile nav chips 完整顯示（整行無換行）
- [ ] 內容全寬顯示
- [ ] **邊界 992px**：側邊欄應隱藏，內容仍應顯示

### 桌面 (≥ 993px)
- [ ] 側邊欄出現，sticky
- [ ] 內容寬度變為 75%
- [ ] Mobile nav 隱藏
- [ ] 側邊欄和內容都可見，無重疊

## 實現方式

**文件修改**：
1. `assets/css/custom.min.css`：添加媒體查詢規則
2. `park.php`：
   - leftnav-mobile 移到 grid 外
   - 移除 Materialize hide-on-* 類
   - 依靠 CSS media query 控制顯示

**原則**：
- ✅ 所有 column 都在 row 內
- ✅ 沒有 col class 的元素不影響 grid
- ✅ CSS media query 精確控制
- ✅ 無邊界值歧義

## 測試工具

使用瀏覽器開發者工具：
```
Chrome/Firefox 開發者工具 → 切換設備工具列 (F12)
→ 調整寬度，檢查：
  - 320px, 375px, 600px, 768px, 992px, 1024px, 1200px
```

---

生成時間：2025-11-14
版本：v3 - CSS-First Media Query
