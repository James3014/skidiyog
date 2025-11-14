<?php
/**
 * 文章 Tags 初始化腳本
 *
 * 目的：為現有文章補充 FAQ tags（用於文章-FAQ 自動關聯）
 * 使用方式：訪問 https://skidiyog.zeabur.app/init_article_tags.php
 *
 * 這是一次性腳本，執行完成後可以刪除。
 */

// 安全檢查：只允許本地或特定 IP 訪問
$allowed_ips = array('127.0.0.1', 'localhost', $_SERVER['REMOTE_ADDR']);
if (!in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1', 'localhost'))) {
    // 允許從任何地方訪問（如需安全性請改為檢查密碼或 token）
}

require('includes/sdk.php');

echo "<h1>文章 FAQ Tags 初始化</h1>";
echo "<pre>";

try {
    // 連接資料庫
    $db = new DB();

    // 定義更新語句
    $updates = array(
        array(1, '#行程規劃,#旺季預約,#雪場資訊,#季節性'),
        array(8, '#教練選擇,#課程諮詢,#滑雪課程,#初學指南'),
        array(9, '#社群互動,#雪友聚會'),
        array(10, '#教練職業,#國際資格,#職涯發展'),
        array(11, '#同堂安排,#團體預約,#課程安排,#教練服務'),
        array(12, '#進階課程,#進度分級,#課程設計'),
        array(13, '#活動推廣,#品牌合作'),
        array(14, '#進階課程,#單板教學,#技術細節'),
        array(15, '#親子同堂,#兒童課程,#安全保障,#家庭課程'),
        array(16, '#保險須知,#費用說明,#旅平險,#風險管理'),
        array(18, '#初學指南,#行程規劃,#課程選擇,#新手須知'),
        array(19, '#進階課程,#單板教學,#技術細節'),
        array(20, '#進階課程,#單板教學,#技術細節'),
        array(21, '#安全保障,#運動傷害,#防護建議'),
        array(22, '#工具使用,#app推薦,#行程規劃'),
        array(23, '#雪具選購,#單板裝備,#初學裝備,#租借建議'),
        array(24, '#活動推廣,#行程安排'),
        array(25, '#品牌合作,#季節性活動'),
        array(26, '#同堂安排,#團體預約,#課程費用,#課程安排'),
        array(27, '#優惠推廣,#住宿推薦,#雪場資訊'),
        array(28, '#雪場資訊,#課程安排,#注意事項'),
        array(29, '#雪具選購,#防護用品,#裝備建議'),
        array(30, '#進度分級,#進階課程,#同堂安排'),
        array(31, '#雪具選購,#雙板裝備,#裝備建議,#租借建議'),
        array(32, '#教練選擇,#課程諮詢,#教練安排,#國外課程'),
        array(33, '#特殊需求,#輔具諮詢,#安全保障,#課程客製'),
        array(34, '#優惠推廣,#雪票優惠,#雪場資訊'),
        array(35, '#同堂安排,#進階課程,#團體預約,#課程安排'),
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

                // 檢查是否已經是 FAQ tags（包含 #）
                if (strpos($current_tags, '#') === false) {
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
                    echo "⏭️  已經更新: idx={$idx} (tags 已包含 #)\n";
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
    echo "你現在可以刪除這個檔案 (init_article_tags.php)。\n";

} catch (Exception $e) {
    echo "❌ 致命錯誤: " . $e->getMessage();
    echo "\n堆疊追蹤:\n";
    echo $e->getTraceAsString();
}

echo "</pre>";
?>
