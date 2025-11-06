# Articles 資料問題說明

## 問題總結

提供的 `articles.json` 檔案內容不正確，**只包含 3 筆資料**，無法匯入完整的文章列表。

---

## 詳細分析

### 1. 目前的資料狀況

**檔案**: `database/articles.json` (1.4MB, 726 筆記錄)

**資料組成**:
- ✅ **3 筆**：真正的滑雪場介紹（留壽都、藏王、小叮噹）
- ❌ **723 筆**：都是 parks sections 資料（不是文章）

**範例記錄** (錯誤類型 - parks section):
```json
{
  "idx": 338,
  "name": "naeba",           // 雪場名稱
  "section": "about",        // 這是 section 欄位！
  "content": "苗場滑雪場介紹...",
  "cname": "",               // 空的標題
  "desc": "",
  "article": "..."
}
```

### 2. 應該要有的資料

根據原網站 https://diy.ski/articleList.php 顯示，應該要有 **24-28 篇文章**：

1. 日本自助滑雪春雪篇，四月也可以滑雪
2. 學習Snowboard分級進度確認
3. 自學滑雪(sb)和教練指導的優劣分析
4. 滑雪分享及聚會
5. 如何花最少錢成為國際滑雪教練
6. 新手日本自助滑雪攻略
7. 學習滑雪上課人數很重要
8. SKIDIY X 真空雪板 捕獲野生教練活動
9. Snowboard滑行教學-QuickRide系統(1)-基礎
10. 親子及家族滑雪需注意的事項
11. 日本自助滑雪攻略-自助滑雪與跟團滑雪投保旅遊平安險的選擇
12. 步驟123如何挑選自己的滑雪裝備[Snowboard 雪板篇]
... (共 24-28 篇)

---

## 正確的資料結構

### 應該匯出的資料表
**MySQL 資料表名稱**: `articles`
**資料庫**: `skidiy` 或 `snow` (請確認)

### 正確的 SQL 查詢
```sql
SELECT
  idx,           -- 文章 ID
  title,         -- 文章標題 (必要欄位)
  tags,          -- 標籤
  article,       -- 文章內容 (HTML 格式)
  keyword,       -- 搜尋關鍵字
  timestamp      -- 發布時間
FROM articles
WHERE title IS NOT NULL
  AND title != ''
  AND title NOT IN ('', ' ')  -- 排除空白標題
ORDER BY idx;
```

### 正確的 JSON 格式
```json
[
  {
    "idx": 1,
    "title": "日本自助滑雪春雪篇，四月也可以滑雪",
    "tags": "春雪,四月,滑雪",
    "article": "<h2>內容標題</h2><p>文章內容...</p>",
    "keyword": "春雪 四月 日本 滑雪",
    "timestamp": "2023-03-15 10:00:00"
  },
  {
    "idx": 2,
    "title": "學習Snowboard分級進度確認",
    "tags": "Snowboard,分級,進度",
    "article": "<h2>內容...</h2>",
    "keyword": "snowboard 分級 進度",
    "timestamp": "2023-02-20 15:30:00"
  }
  ...
]
```

### 重要特徵 (用來識別正確的資料)

✅ **必須有**:
- `title` 欄位 (文章標題，非空)
- `article` 欄位 (文章內容，通常是 HTML)
- 標題應該是完整的句子，例如："日本自助滑雪春雪篇，四月也可以滑雪"

❌ **不應該有**:
- `section` 欄位 (這是 parks 資料表的欄位)
- `name` 欄位值為雪場名稱 (rusutsu, naeba, niseko 等)

---

## 如何匯出正確的資料

### 方法 1: 使用 MySQL 命令列
```bash
mysql -u root -p skidiy -e "
  SELECT
    idx, title, tags, article, keyword, timestamp
  FROM articles
  WHERE title IS NOT NULL AND title != ''
  ORDER BY idx
" --skip-column-names --batch | \
  php -r '
    $rows = [];
    while ($line = fgets(STDIN)) {
      $fields = explode("\t", trim($line));
      $rows[] = [
        "idx" => (int)$fields[0],
        "title" => $fields[1],
        "tags" => $fields[2],
        "article" => $fields[3],
        "keyword" => $fields[4],
        "timestamp" => $fields[5]
      ];
    }
    echo json_encode($rows, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
  ' > articles_correct.json
```

### 方法 2: 使用 PHP 腳本
```php
<?php
$db = new mysqli('localhost', 'root', 'password', 'skidiy');
$result = $db->query("
  SELECT idx, title, tags, article, keyword, timestamp
  FROM articles
  WHERE title IS NOT NULL AND title != ''
  ORDER BY idx
");

$articles = [];
while ($row = $result->fetch_assoc()) {
    $articles[] = $row;
}

file_put_contents(
    'articles_correct.json',
    json_encode($articles, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
);

echo "匯出完成！共 " . count($articles) . " 篇文章\n";
?>
```

---

## 驗證方法

匯出後，請確認：

1. **檔案大小**: 應該比較小（如果 24-28 篇文章，大約 100-500KB）
2. **記錄數量**: 應該是 24-28 筆左右
3. **每筆記錄都有 `title` 欄位**: 用文字編輯器打開，搜尋 `"title"`，應該每筆都有
4. **沒有 `section` 欄位**: 搜尋 `"section"`，應該找不到或很少
5. **標題內容正確**: 打開檔案看前幾筆的 title，應該是完整的文章標題

### 快速驗證指令
```bash
# 檢查記錄數
cat articles_correct.json | grep '"idx"' | wc -l

# 檢查是否都有 title
cat articles_correct.json | grep '"title"' | head -10

# 檢查檔案大小
ls -lh articles_correct.json
```

---

## 對比：錯誤 vs 正確

### ❌ 錯誤 (目前的 articles.json)
```json
{
  "idx": 338,
  "name": "naeba",         // 雪場名稱
  "section": "about",      // parks 的 section
  "content": "...",
  "cname": "",             // 空標題
  "article": "..."
}
```

### ✅ 正確 (應該要的格式)
```json
{
  "idx": 1,
  "title": "日本自助滑雪春雪篇，四月也可以滑雪",  // 完整標題
  "tags": "春雪,滑雪",
  "article": "<h2>內容...</h2>",
  "keyword": "春雪 日本 滑雪",
  "timestamp": "2023-03-15 10:00:00"
}
```

---

## 聯絡資訊

如有問題請聯繫：
- 需要的檔案名稱：`articles_correct.json`
- 預期筆數：24-28 筆
- 驗證方式：每筆記錄必須有 `title` 欄位，且是完整的文章標題

---

**產生時間**: 2025-11-06
**問題檔案**: `database/articles.json` (1.4MB, 726 筆)
**正確筆數**: 應該 24-28 筆
