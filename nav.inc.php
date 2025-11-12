<?php
// Compatibility wrapper so legacy pages can still include nav.inc.php
require_once __DIR__ . '/includes/sdk.php';

$nav_display_name = '';
if (isset($parkData['display_name'])) {
    $nav_display_name = $parkData['display_name'];
} elseif (isset($park_info['cname']) && $park_info['cname']!=='') {
    $nav_display_name = $park_info['cname'];
} elseif (!empty($name)) {
    $nav_display_name = ucfirst($name);
}

$nav_sections = array();
if (!empty($parkData['sections']) && is_array($parkData['sections'])) {
    $nav_sections = $parkData['sections'];
} elseif (isset($SECTION_HEADER) && is_array($SECTION_HEADER)) {
    foreach ($SECTION_HEADER as $key => $label) {
        if ($key === 'all') { continue; }
        $nav_sections[] = array('key' => $key, 'title' => $label);
    }
}

$nav_context = array(
    'display_name' => $nav_display_name,
    'name' => isset($name) ? $name : null,
    'is_park_context' => (isset($is_park_context) ? $is_park_context : (strstr($_SERVER['PHP_SELF'],'routing') || strstr($_SERVER['PHP_SELF'],'park'))),
    'sections' => $nav_sections
);

renderNav($nav_context);
