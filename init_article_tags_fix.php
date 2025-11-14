<?php
/**
 * 文章 Tags 修正腳本
 *
 * 目的：修正某些文章的 tags（使用 FAQ 中實際存在的 tags）
 * 使用方式：訪問 https://skidiyog.zeabur.app/init_article_tags_fix.php
 *
 * 這是一次性腳本，執行完成後可以刪除。
 */

require('includes/sdk.php');

echo "<h1>文章 FAQ Tags 修正腳本</h1>";
echo "<pre>";

try {
    // 連接資料庫
    $db = new DB();

    // 定義修正語句
    // 這些是之前使用了 FAQ 不存在的 tags 的文章
    $updates = array(
        // idx=31: 雙板雪杖選購建議
        // 原 tags: #雪具選購,#雙板裝備,#裝備建議,#租借建議 (FAQ 中都不存在)
        // 修正為與租借和課程相關的 FAQ tags
        array(31, '#租借資訊,#教練選擇,#課程安排'),

        // idx=33: 身障雪友自助日本滑雪攻略分享
        // 原 tags: #特殊需求,#輔具諮詢,#安全保障,#課程客製 (FAQ 中都不存在)
        // 修正為與安全、滑雪準備、課程相關的 FAQ tags
        array(33, '#安全須知,#滑雪準備,#教練選擇,#課程諮詢'),
    );

    $updated = 0;
    $errors = array();

    foreach ($updates as $item) {
        $idx = $item[0];
        $new_tags = $item[1];

        try {
            // 檢查現有資料
            $row = $db->SELECT('articles', array('idx' => $idx));
            if (!empty($row)) {
                $old_tags = isset($row[0]['tags']) ? $row[0]['tags'] : '';

                // 執行更新
                $result = $db->UPDATE('articles', array('tags' => $new_tags), array('idx' => $idx));
                if ($result) {
                    echo "✅ 修正成功: idx={$idx}\n";
                    echo "   舊 tags: {$old_tags}\n";
                    echo "   新 tags: {$new_tags}\n";
                    $updated++;
                } else {
                    echo "❌ 修正失敗: idx={$idx}\n";
                    $errors[] = "idx={$idx}";
                }
            } else {
                echo "⚠️  找不到文章: idx={$idx}\n";
            }
        } catch (Exception $e) {
            echo "❌ 錯誤 (idx={$idx}): " . $e->getMessage() . "\n";
            $errors[] = "idx={$idx}: " . $e->getMessage();
        }
    }

    echo "\n========== 修正摘要 ==========\n";
    echo "已修正: {$updated} 篇文章\n";
    if (!empty($errors)) {
        echo "錯誤數量: " . count($errors) . "\n";
        foreach ($errors as $error) {
            echo "  - {$error}\n";
        }
    }
    echo "\n✅ 修正完成！\n";
    echo "你現在可以刪除這個檔案 (init_article_tags_fix.php)。\n";

} catch (Exception $e) {
    echo "❌ 致命錯誤: " . $e->getMessage();
    echo "\n堆疊追蹤:\n";
    echo $e->getTraceAsString();
}

echo "</pre>";
?>
