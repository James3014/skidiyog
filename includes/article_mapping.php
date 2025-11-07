<?php
/**
 * Article IDX to URL Mapping Table
 * Generated: 2025-11-08
 * Purpose: Support 301 redirects from old article.php?idx=X to new SEO-friendly URLs
 */

// Load mapping from JSON file
$ARTICLE_MAPPING = json_decode(file_get_contents(__DIR__ . '/../article_mapping.json'), true);

/**
 * Get new URL for an article by idx
 * @param int $idx Article index
 * @return string|null New URL or null if not found
 */
function getArticleNewUrl($idx) {
    global $ARTICLE_MAPPING;
    return $ARTICLE_MAPPING[$idx]['new_url'] ?? null;
}

/**
 * Get old URL for an article by idx
 * @param int $idx Article index
 * @return string|null Old URL or null if not found
 */
function getArticleOldUrl($idx) {
    global $ARTICLE_MAPPING;
    return $ARTICLE_MAPPING[$idx]['old_url'] ?? null;
}

/**
 * Get article title by idx
 * @param int $idx Article index
 * @return string|null Article title or null if not found
 */
function getArticleTitle($idx) {
    global $ARTICLE_MAPPING;
    return $ARTICLE_MAPPING[$idx]['title'] ?? null;
}

/**
 * Check if article idx exists in mapping
 * @param int $idx Article index
 * @return bool
 */
function articleExists($idx) {
    global $ARTICLE_MAPPING;
    return isset($ARTICLE_MAPPING[$idx]);
}
