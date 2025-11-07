<?php
/**
 * 301 Redirect Handler for SEO URL Migration
 *
 * Purpose: Handle redirects from old URLs to new SEO-friendly URLs
 * Usage: Configure web server to route old URLs to this script
 *
 * Redirect Rules:
 * - article.php?idx=X → /article/{slug}-{idx}
 * - Future: Add more redirect rules as needed
 */

require_once __DIR__ . '/includes/article_mapping.php';

/**
 * Perform 301 redirect
 */
function redirect301($url) {
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: " . $url);
    exit();
}

/**
 * Handle article.php redirects
 */
function handleArticleRedirect() {
    if (!isset($_GET['idx'])) {
        return false;
    }

    $idx = intval($_GET['idx']);

    if (!articleExists($idx)) {
        return false;
    }

    $newUrl = getArticleNewUrl($idx);
    if ($newUrl) {
        redirect301($newUrl);
        return true;
    }

    return false;
}

/**
 * Main redirect logic
 */
function processRedirect() {
    $requestUri = $_SERVER['REQUEST_URI'];
    $scriptName = basename($_SERVER['SCRIPT_NAME']);

    // Handle article.php?idx=X redirects
    if ($scriptName === 'article.php' || strpos($requestUri, 'article.php') !== false) {
        if (handleArticleRedirect()) {
            return;
        }
    }

    // If no redirect rule matched, show 404 or continue to original page
    // For now, we'll just return false to let the original page load
    return false;
}

// Execute redirect logic
processRedirect();
