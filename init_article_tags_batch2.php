<?php
/**
 * 文章 Tags 初始化腳本 - 批次 2
 *
 * 目的：為缺失 FAQ tags 的文章補充標籤（第二批）
 * 使用方式：訪問 https://skidiyog.zeabur.app/init_article_tags_batch2.php
 *
 * 這是一次性腳本，執行完成後可以刪除。
 */

// 安全檢查：只允許本地或特定 IP 訪問
if (!in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1', 'localhost'))) {
    // 允許從任何地方訪問（如需安全性請改為檢查密碼或 token）
}

require('includes/sdk.php');

echo "<h1>文章 FAQ Tags 初始化 - 批次 2</h1>";
echo "<pre>";

try {
    // 連接資料庫
    $db = new DB();

    // 定義更新語句（第二批 - 缺失 tags 的 15 篇文章）
    $updates = array(
        array(9, '#社群互動,#雪友聚會'),
        array(10, '#教練職業,#國際資格,#職涯發展'),
        array(12, '#進階課程,#進度分級,#課程設計'),
        array(13, '#活動推廣,#品牌合作'),
        array(14, '#進階課程,#單板教學,#技術細節'),
        array(16, '#保險須知,#費用說明,#旅平險,#風險管理'),
        array(19, '#進階課程,#單板教學,#技術細節'),
        array(20, '#進階課程,#單板教學,#技術細節'),
        array(21, '#安全保障,#運動傷害,#防護建議'),
        array(23, '#雪具選購,#單板裝備,#初學裝備,#租借建議'),
        array(29, '#雪具選購,#防護用品,#裝備建議'),
        array(31, '#雪具選購,#雙板裝備,#裝備建議,#租借建議'),
        array(33, '#特殊需求,#輔具諮詢,#安全保障,#課程客製'),
        array(36, '#品牌合作,#產品推廣'),
        array(38, '#招聘資訊,#品牌文化,#職涯發展'),
    );

    $updated = 0;
    $errors = array();

    foreach ($updates as $item) {
        $idx = $item[0];
        $tags = $item[1];

        try {
            // 檢查現有 tags
            $row = $db->SELECT('articles', array('idx' => $idx));
            if (!empty($row)) {
                $current_tags = isset($row[0]['tags']) ? $row[0]['tags'] : '';

                // 檢查是否已經有 tags
                if (empty($current_tags)) {
                    // 執行更新
                    $result = $db->UPDATE('articles', array('tags' => $tags), array('idx' => $idx));
                    if ($result) {
                        echo "✅ 更新成功: idx={$idx}, tags={$tags}\n";
                        $updated++;
                    } else {
                        echo "❌ 更新失敗: idx={$idx}\n";
                        $errors[] = "idx={$idx}";
                    }
                } else {
                    echo "⏭️  已經有 tags: idx={$idx} (tags = {$current_tags})\n";
                    $updated++;
                }
            } else {
                echo "⚠️  找不到文章: idx={$idx}\n";
            }
        } catch (Exception $e) {
            echo "❌ 錯誤 (idx={$idx}): " . $e->getMessage() . "\n";
            $errors[] = "idx={$idx}: " . $e->getMessage();
        }
    }

    echo "\n========== 更新摘要 ==========\n";
    echo "已更新或確認: {$updated} 篇文章\n";
    if (!empty($errors)) {
        echo "錯誤數量: " . count($errors) . "\n";
        foreach ($errors as $error) {
            echo "  - {$error}\n";
        }
    }
    echo "\n✅ 初始化完成！\n";
    echo "你現在可以刪除這個檔案 (init_article_tags_batch2.php)。\n";

} catch (Exception $e) {
    echo "❌ 致命錯誤: " . $e->getMessage();
    echo "\n堆疊追蹤:\n";
    echo $e->getTraceAsString();
}

echo "</pre>";
?>
