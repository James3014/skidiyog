<?php

function renderSectionContent($section)
{
    $content = isset($section['content']) ? $section['content'] : '';
    $mode = isset($section['render_mode']) ? $section['render_mode'] : 'rich';

    if ($mode === 'pre') {
        return '<div class="section-content section-content--pre"><pre>' . convert_media_urls($content) . '</pre></div>';
    }

    return '<div class="section-content section-content--rich">' . normalize_rich_text($content) . '</div>';
}

function renderSectionList($sections)
{
    if (empty($sections) || !is_array($sections)) {
        echo '<p>暫無雪場詳細資訊</p>';
        return;
    }

    foreach ($sections as $section) {
        if (empty($section['key']) || empty($section['title'])) {
            continue;
        }
        echo '<h1 id="' . $section['key'] . '">' . $section['title'] . '</h1>';
        echo renderSectionContent($section) . '<hr>';
    }
}
