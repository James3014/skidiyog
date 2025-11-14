<?php
/**
 * 文章 Tags 批量修正腳本
 *
 * 目的：為剩餘 13 篇文章的 tags 重新對應為 FAQ 中實際存在的 tags
 * 使用方式：訪問 https://skidiyog.zeabur.app/init_article_tags_fix_batch.php
 */

require('includes/sdk.php');

echo "<h1>文章 FAQ Tags 批量修正腳本</h1>";
echo "<pre>";

try {
    $db = new DB();

    // 根據分析，這些是 FAQ 中實際存在的有效 tags
    // 我們為每篇文章選擇最相關的 FAQ tags
    $updates = array(
        // idx=9: 滑雪分享及聚會 -> 課程/教練相關
        array(9, '#教練選擇,#課程安排,#教練服務'),

        // idx=10: 如何花最少錢成為國際滑雪教練 -> 教練資格相關
        array(10, '#教練資格,#教練選擇,#課程安排'),

        // idx=12: 學習Snowboard分級進度確認 -> 課程等級相關
        array(12, '#課程安排,#學習建議,#教學安排'),

        // idx=13: SKIDIY X 真空雪板 活動 -> 課程安排相關
        array(13, '#課程安排,#教練選擇,#課程諮詢'),

        // idx=14: Snowboard滑行教學-QuickRide系統(1) -> 課程安排
        array(14, '#課程安排,#教學安排,#學習建議'),

        // idx=16: 自助滑雪與投保旅平險 -> 滑雪保險相關
        array(16, '#滑雪保險,#安全須知,#滑雪準備'),

        // idx=19: Snowboard滑行教學-QuickRide系統(2) -> 課程安排
        array(19, '#課程安排,#教學安排,#學習建議'),

        // idx=20: Snowboard滑行教學-QuickRide系統(3) -> 課程安排
        array(20, '#課程安排,#教學安排,#學習建議'),

        // idx=21: 單板 VS 雙板護具安全 -> 安全相關
        array(21, '#安全須知,#滑雪準備,#安全防護'),

        // idx=23: 滑雪裝備選購 -> 租借相關
        array(23, '#租借資訊,#滑雪準備,#課程安排'),

        // idx=29: 雪鏡選購 -> 租借相關
        array(29, '#租借資訊,#滑雪準備,#課程安排'),

        // idx=36: Cardo x Skidiy 合作 -> 課程安排
        array(36, '#課程安排,#教練選擇,#課程諮詢'),

        // idx=38: 招聘資訊 -> 教練相關
        array(38, '#教練資格,#教練選擇,#教練服務'),
    );

    $updated = 0;
    $errors = array();

    foreach ($updates as $item) {
        $idx = $item[0];
        $new_tags = $item[1];

        try {
            $row = $db->SELECT('articles', array('idx' => $idx));
            if (!empty($row)) {
                $old_tags = isset($row[0]['tags']) ? $row[0]['tags'] : '';

                $result = $db->UPDATE('articles', array('tags' => $new_tags), array('idx' => $idx));
                if ($result) {
                    echo "✅ 修正成功: idx={$idx}\n";
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
            $errors[] = "idx={$idx}";
        }
    }

    echo "\n========== 修正摘要 ==========\n";
    echo "已修正: {$updated} 篇文章\n";
    if (!empty($errors)) {
        echo "錯誤數量: " . count($errors) . "\n";
    }
    echo "\n✅ 批量修正完成！\n";
    echo "你現在可以刪除這個檔案。\n";

} catch (Exception $e) {
    echo "❌ 致命錯誤: " . $e->getMessage();
    echo "\n" . $e->getTraceAsString();
}

echo "</pre>";
?>
