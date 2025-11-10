<?php
/**
 * Preview guard for site-wide crawl token enforcement.
 * Ensures every HTTP request carries the preview_token query parameter
 * (or an existing cookie from a previously validated request) before
 * returning page content. Also exposes a render flag for static mode.
 */

if (defined('SKID_PREVIEW_GUARD_LOADED')) {
    return;
}
define('SKID_PREVIEW_GUARD_LOADED', true);

$isCli = (php_sapi_name() === 'cli');
$renderParam = defined('SKID_PREVIEW_RENDER_PARAM') ? SKID_PREVIEW_RENDER_PARAM : 'render';
$renderValue = defined('SKID_PREVIEW_RENDER_VALUE') ? SKID_PREVIEW_RENDER_VALUE : 'static';

if ($isCli) {
    define('SKID_PREVIEW_MODE', false);
    define('SKID_STATIC_RENDER_MODE', false);
    return;
}

$enforcePreview = defined('SKID_PREVIEW_TOKEN_ENFORCED') ? SKID_PREVIEW_TOKEN_ENFORCED : false;
$allowedToken = defined('SKID_PREVIEW_TOKEN') ? SKID_PREVIEW_TOKEN : '';

$hasPreviewAccess = false;
$sessionToken = isset($_SESSION['preview_token']) ? $_SESSION['preview_token'] : null;
if ($enforcePreview && $allowedToken !== '') {
    $incomingToken = isset($_GET['preview_token']) ? trim($_GET['preview_token']) : null;
    if ($incomingToken && hash_equals($allowedToken, $incomingToken)) {
        $hasPreviewAccess = true;
        $_SESSION['preview_token'] = $allowedToken;
        // extend convenience via secure-ish cookie for navigation within preview site
        if (!isset($_COOKIE['preview_token']) || !hash_equals($_COOKIE['preview_token'], $allowedToken)) {
            setcookie('preview_token', $allowedToken, 0, '/', '', false, true);
        }
    } elseif ($sessionToken && hash_equals($sessionToken, $allowedToken)) {
        $hasPreviewAccess = true;
    } elseif (isset($_COOKIE['preview_token']) && hash_equals($_COOKIE['preview_token'], $allowedToken)) {
        $hasPreviewAccess = true;
        $_SESSION['preview_token'] = $allowedToken;
    }

    if (!$hasPreviewAccess) {
        header('X-Robots-Tag: noindex, nofollow', true);
        http_response_code(403);
        exit('Forbidden - preview token required');
    }

    header('X-Robots-Tag: noindex, nofollow', true);
}

define('SKID_PREVIEW_MODE', $enforcePreview && $allowedToken !== '');
define('SKID_STATIC_RENDER_MODE', isset($_GET[$renderParam]) && $_GET[$renderParam] === $renderValue);
