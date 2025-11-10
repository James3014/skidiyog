<?php
require('includes/sdk.php');

$renderParam = defined('SKID_PREVIEW_RENDER_PARAM') ? SKID_PREVIEW_RENDER_PARAM : 'render';

$target = '/parkList.php';
$query = [];
if (!empty($_GET['preview_token'])) {
    $query['preview_token'] = $_GET['preview_token'];
}
if (!empty($_GET[$renderParam])) {
    $query[$renderParam] = $_GET[$renderParam];
}
$qs = $query ? '?' . http_build_query($query) : '';
header('Location: ' . $target . $qs);
exit;
