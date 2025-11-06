<?php
$json = file_get_contents(__DIR__ . '/articles_parks_sections.json');
$all_data = json_decode($json, true);

// 提取雪場資料（section 不為空的記錄）
$parks_data = [];
foreach ($all_data as $item) {
    $section = isset($item['section']) ? trim($item['section']) : '';
    if (!empty($section)) {
        $parks_data[] = $item;
    }
}

echo "提取到 " . count($parks_data) . " 筆 parks section 資料\n";
file_put_contents(__DIR__ . '/parks.json', json_encode($parks_data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
echo "已儲存到 parks.json\n";
