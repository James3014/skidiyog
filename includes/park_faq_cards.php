<?php
/**
 * Park FAQ Cards Component
 *
 * Renders park-specific FAQ cards with:
 * - Multi-language support (zh, en, th)
 * - Tag-based filtering
 * - Click tracking to analytics backend
 * - Session-based anonymous user tracking
 *
 * Usage:
 * renderParkFAQCards($park_slug, $park_cname);
 */

// API endpoint configuration
define('PARK_FAQ_API_ENDPOINT', 'http://localhost:3000/api/v1/park-faq');

/**
 * Get or create session ID for anonymous user tracking
 * Stores session ID in browser localStorage via JavaScript
 *
 * @return string Session ID
 */
function getParkFaqSessionId() {
  // Generate or retrieve session ID
  // This will be handled by JavaScript in the frontend
  return null; // JS will inject this
}

/**
 * Render park FAQ cards component
 *
 * @param string $park_slug Park identifier (e.g., 'appi')
 * @param string $park_cname Park display name in Chinese
 * @param array $options Optional rendering options
 */
function renderParkFAQCards($park_slug, $park_cname, $options = []) {
  $lang = isset($_GET['lang']) ? $_GET['lang'] : 'zh';
  $supported_langs = ['zh', 'en', 'th'];

  if (!in_array($lang, $supported_langs)) {
    $lang = 'zh';
  }

  $api_endpoint = PARK_FAQ_API_ENDPOINT;

  // Language labels
  $lang_labels = [
    'zh' => '中文',
    'en' => 'English',
    'th' => 'ไทย'
  ];

  // Card container ID
  $container_id = 'park-faq-cards-' . $park_slug;
?>

<div id="<?php echo htmlspecialchars($container_id); ?>" class="park-faq-cards-container">
  <!-- Language Switcher -->
  <div class="park-faq-language-switcher">
    <?php foreach ($supported_langs as $available_lang): ?>
      <button
        class="park-faq-lang-btn <?php echo $lang === $available_lang ? 'active' : ''; ?>"
        data-lang="<?php echo htmlspecialchars($available_lang); ?>"
        onclick="switchParkFaqLanguage('<?php echo htmlspecialchars($park_slug); ?>', '<?php echo htmlspecialchars($available_lang); ?>')">
        <?php echo htmlspecialchars($lang_labels[$available_lang]); ?>
      </button>
    <?php endforeach; ?>
  </div>

  <!-- Tag Filter -->
  <div class="park-faq-tag-filter">
    <div class="park-faq-tags" id="park-faq-tags-<?php echo htmlspecialchars($park_slug); ?>">
      <!-- Tags will be populated by JavaScript -->
    </div>
  </div>

  <!-- Loading State -->
  <div class="park-faq-loading" id="park-faq-loading-<?php echo htmlspecialchars($park_slug); ?>">
    <p>讀取常見問題中...</p>
  </div>

  <!-- Cards Grid -->
  <div
    class="park-faq-cards-grid"
    id="park-faq-grid-<?php echo htmlspecialchars($park_slug); ?>"
    data-park-slug="<?php echo htmlspecialchars($park_slug); ?>"
    data-park-cname="<?php echo htmlspecialchars($park_cname); ?>"
    data-lang="<?php echo htmlspecialchars($lang); ?>"
    data-api-endpoint="<?php echo htmlspecialchars($api_endpoint); ?>">
    <!-- Cards will be populated by JavaScript -->
  </div>

  <!-- Error State -->
  <div class="park-faq-error" id="park-faq-error-<?php echo htmlspecialchars($park_slug); ?>" style="display:none;">
    <p>無法載入常見問題，請稍後重試。</p>
  </div>
</div>

<!-- Inline Script for Park FAQ Cards -->
<script>
(function() {
  'use strict';

  /**
   * Configuration
   */
  const PARK_FAQ_CONFIG = {
    sessionIdKey: 'park_faq_session_id',
    debounceDelay: 300,
    apiTimeout: 5000
  };

  /**
   * Get or create anonymous session ID
   */
  function getSessionId() {
    let sessionId = localStorage.getItem(PARK_FAQ_CONFIG.sessionIdKey);
    if (!sessionId) {
      // Generate UUID-like session ID
      sessionId = 'sess_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
      localStorage.setItem(PARK_FAQ_CONFIG.sessionIdKey, sessionId);
    }
    return sessionId;
  }

  /**
   * Fetch park FAQ cards from API
   */
  async function fetchParkFaqCards(parkSlug, lang) {
    const grid = document.querySelector(`[data-park-slug="${parkSlug}"]`);
    if (!grid) return;

    const apiEndpoint = grid.dataset.apiEndpoint;
    const loadingEl = document.getElementById(`park-faq-loading-${parkSlug}`);
    const errorEl = document.getElementById(`park-faq-error-${parkSlug}`);

    try {
      loadingEl.style.display = 'block';
      errorEl.style.display = 'none';

      const response = await fetch(`${apiEndpoint}/cards?park_slug=${encodeURIComponent(parkSlug)}&lang=${encodeURIComponent(lang)}`, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json'
        },
        timeout: PARK_FAQ_CONFIG.apiTimeout
      });

      if (!response.ok) {
        throw new Error(`API error: ${response.status}`);
      }

      const data = await response.json();

      if (!data.success) {
        throw new Error(data.error?.message || 'Failed to fetch cards');
      }

      // Render cards and tags
      renderParkFaqCards(grid, data.data, lang);
      renderParkFaqTags(parkSlug, data.data, lang);

      loadingEl.style.display = 'none';
    } catch (error) {
      console.error('[Park FAQ] Error fetching cards:', error);
      loadingEl.style.display = 'none';
      errorEl.style.display = 'block';
    }
  }

  /**
   * Render FAQ cards in grid
   */
  function renderParkFaqCards(grid, data, lang) {
    const cards = data.cards || [];
    grid.innerHTML = '';

    if (cards.length === 0) {
      grid.innerHTML = '<p class="park-faq-no-cards">暫無常見問題</p>';
      return;
    }

    cards.forEach(card => {
      const cardEl = document.createElement('div');
      cardEl.className = 'park-faq-card';
      cardEl.dataset.cardId = card.id;
      cardEl.innerHTML = `
        <div class="park-faq-card-header">
          <h3 class="park-faq-card-question">${escapeHtml(card.question)}</h3>
          <button class="park-faq-card-toggle" onclick="toggleParkFaqCardAnswer(this)">
            <span class="icon">▼</span>
          </button>
        </div>
        <div class="park-faq-card-answer" style="display:none;">
          <p class="park-faq-card-answer-text">${escapeHtml(card.answer)}</p>
          <div class="park-faq-card-tags">
            ${card.tags.map(tag => `
              <button
                class="park-faq-card-tag"
                data-tag-name="${escapeHtml(tag.name)}"
                onclick="trackParkFaqTagClick('${escapeHtml(card.id)}', '${encodeURIComponent(card.park_slug)}', '${escapeHtml(tag.name)}', '${lang}')">
                ${escapeHtml(tag.name)}
              </button>
            `).join('')}
          </div>
        </div>
      `;
      grid.appendChild(cardEl);

      // Track card view
      trackParkFaqCardView(card.id, card.park_slug, lang);
    });

    // Update grid data attribute with language
    grid.dataset.lang = lang;
  }

  /**
   * Render filter tags
   */
  function renderParkFaqTags(parkSlug, data, lang) {
    const tagsContainer = document.getElementById(`park-faq-tags-${parkSlug}`);
    const tags = data.available_tags || [];

    tagsContainer.innerHTML = '';

    // Add "All" button
    const allBtn = document.createElement('button');
    allBtn.className = 'park-faq-tag-filter-btn active';
    allBtn.textContent = lang === 'zh' ? '全部' : (lang === 'en' ? 'All' : 'ทั้งหมด');
    allBtn.onclick = () => filterParkFaqCardsByTag(parkSlug, null);
    tagsContainer.appendChild(allBtn);

    // Add individual tag buttons
    tags.forEach(tag => {
      const btn = document.createElement('button');
      btn.className = 'park-faq-tag-filter-btn';
      btn.textContent = tag;
      btn.dataset.tag = tag;
      btn.onclick = () => filterParkFaqCardsByTag(parkSlug, tag);
      tagsContainer.appendChild(btn);
    });
  }

  /**
   * Filter cards by tag
   */
  function filterParkFaqCardsByTag(parkSlug, filterTag) {
    const grid = document.querySelector(`[data-park-slug="${parkSlug}"]`);
    const cards = grid.querySelectorAll('.park-faq-card');
    const tagsContainer = document.getElementById(`park-faq-tags-${parkSlug}`);
    const tagBtns = tagsContainer.querySelectorAll('.park-faq-tag-filter-btn');

    // Update active button
    tagBtns.forEach(btn => {
      if (filterTag === null && !btn.dataset.tag) {
        btn.classList.add('active');
      } else if (btn.dataset.tag === filterTag) {
        btn.classList.add('active');
      } else {
        btn.classList.remove('active');
      }
    });

    // Filter cards
    cards.forEach(card => {
      if (filterTag === null) {
        card.style.display = 'block';
      } else {
        const cardTags = Array.from(card.querySelectorAll('.park-faq-card-tag'))
          .map(el => el.dataset.tagName);
        card.style.display = cardTags.includes(filterTag) ? 'block' : 'none';
      }
    });
  }

  /**
   * Toggle card answer visibility
   */
  window.toggleParkFaqCardAnswer = function(btn) {
    const card = btn.closest('.park-faq-card');
    const answerDiv = card.querySelector('.park-faq-card-answer');
    const isVisible = answerDiv.style.display !== 'none';

    answerDiv.style.display = isVisible ? 'none' : 'block';
    btn.classList.toggle('expanded', !isVisible);
  };

  /**
   * Track card view
   */
  async function trackParkFaqCardView(cardId, parkSlug, lang) {
    try {
      const grid = document.querySelector(`[data-park-slug="${parkSlug}"]`);
      const apiEndpoint = grid.dataset.apiEndpoint;
      const sessionId = getSessionId();

      await fetch(`${apiEndpoint}/track-card-view`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          card_id: cardId,
          park_slug: parkSlug,
          language: lang,
          session_id: sessionId
        })
      });
    } catch (error) {
      console.warn('[Park FAQ] Failed to track card view:', error);
    }
  }

  /**
   * Track tag click
   */
  window.trackParkFaqTagClick = async function(cardId, parkSlug, tagName, lang) {
    try {
      const grid = document.querySelector(`[data-park-slug="${decodeURIComponent(parkSlug)}"]`);
      const apiEndpoint = grid.dataset.apiEndpoint;
      const sessionId = getSessionId();

      await fetch(`${apiEndpoint}/track-tag-click`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          tag_name: tagName,
          card_id: cardId,
          park_slug: decodeURIComponent(parkSlug),
          language: lang,
          session_id: sessionId
        })
      });

      console.log(`[Park FAQ] Tracked tag click: ${tagName}`);
    } catch (error) {
      console.warn('[Park FAQ] Failed to track tag click:', error);
    }
  };

  /**
   * Switch language
   */
  window.switchParkFaqLanguage = function(parkSlug, lang) {
    // Update URL parameter
    const url = new URL(window.location);
    url.searchParams.set('lang', lang);
    window.history.pushState({}, '', url);

    // Update button states
    const container = document.getElementById(`park-faq-cards-${parkSlug}`);
    const langBtns = container.querySelectorAll('.park-faq-lang-btn');
    langBtns.forEach(btn => {
      btn.classList.toggle('active', btn.dataset.lang === lang);
    });

    // Fetch new cards
    fetchParkFaqCards(parkSlug, lang);
  };

  /**
   * HTML escape utility
   */
  function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }

  /**
   * Initialize: Fetch cards on page load
   */
  document.addEventListener('DOMContentLoaded', () => {
    const grids = document.querySelectorAll('.park-faq-cards-grid');
    grids.forEach(grid => {
      const parkSlug = grid.dataset.parkSlug;
      const lang = grid.dataset.lang;
      fetchParkFaqCards(parkSlug, lang);
    });
  });
})();
</script>

<!-- CSS Styles for Park FAQ Cards -->
<style>
.park-faq-cards-container {
  width: 100%;
  margin: 2rem 0;
}

/* Language Switcher */
.park-faq-language-switcher {
  display: flex;
  gap: 0.5rem;
  margin-bottom: 1.5rem;
  border-bottom: 1px solid #e0e0e0;
  padding-bottom: 1rem;
}

.park-faq-lang-btn {
  padding: 0.5rem 1rem;
  border: none;
  background: transparent;
  cursor: pointer;
  font-size: 0.9rem;
  color: #666;
  border-bottom: 3px solid transparent;
  transition: all 0.3s ease;
}

.park-faq-lang-btn:hover {
  color: #333;
}

.park-faq-lang-btn.active {
  color: #1976d2;
  border-bottom-color: #1976d2;
  font-weight: 600;
}

/* Tag Filter */
.park-faq-tag-filter {
  margin-bottom: 1.5rem;
}

.park-faq-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
}

.park-faq-tag-filter-btn {
  padding: 0.5rem 1rem;
  border: 1px solid #ddd;
  border-radius: 20px;
  background: #f5f5f5;
  cursor: pointer;
  font-size: 0.85rem;
  transition: all 0.3s ease;
}

.park-faq-tag-filter-btn:hover {
  border-color: #1976d2;
  background: #e3f2fd;
}

.park-faq-tag-filter-btn.active {
  background: #1976d2;
  color: white;
  border-color: #1976d2;
}

/* Loading & Error States */
.park-faq-loading,
.park-faq-error {
  padding: 2rem;
  text-align: center;
  color: #666;
}

.park-faq-error {
  background: #ffebee;
  color: #c62828;
  border-radius: 4px;
}

/* Cards Grid */
.park-faq-cards-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.5rem;
  margin: 1rem 0;
}

@media (max-width: 768px) {
  .park-faq-cards-grid {
    grid-template-columns: 1fr;
  }
}

/* Card Styles */
.park-faq-card {
  background: white;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  overflow: hidden;
  transition: all 0.3s ease;
}

.park-faq-card:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  border-color: #1976d2;
}

.park-faq-card-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  padding: 1.25rem;
  cursor: pointer;
  background: #f9f9f9;
  border-bottom: 1px solid #e0e0e0;
}

.park-faq-card-question {
  margin: 0;
  font-size: 1rem;
  font-weight: 600;
  color: #333;
  line-height: 1.4;
  flex: 1;
}

.park-faq-card-toggle {
  background: none;
  border: none;
  cursor: pointer;
  padding: 0.25rem 0.5rem;
  color: #666;
  font-size: 0.8rem;
  transition: transform 0.3s ease;
  margin-left: 0.5rem;
  flex-shrink: 0;
}

.park-faq-card-toggle.expanded {
  transform: rotate(180deg);
}

/* Card Answer */
.park-faq-card-answer {
  padding: 1.25rem;
}

.park-faq-card-answer-text {
  margin: 0 0 1rem 0;
  font-size: 0.95rem;
  line-height: 1.6;
  color: #555;
}

/* Card Tags */
.park-faq-card-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.park-faq-card-tag {
  padding: 0.35rem 0.75rem;
  background: #e3f2fd;
  border: 1px solid #90caf9;
  border-radius: 16px;
  font-size: 0.75rem;
  cursor: pointer;
  transition: all 0.3s ease;
  color: #1976d2;
}

.park-faq-card-tag:hover {
  background: #1976d2;
  color: white;
  border-color: #1976d2;
}

/* No Cards Message */
.park-faq-no-cards {
  grid-column: 1 / -1;
  text-align: center;
  padding: 2rem;
  color: #999;
  font-size: 1rem;
}

/* Thai Language Support */
.park-faq-cards-container[data-lang="th"] .park-faq-card-question,
.park-faq-cards-container[data-lang="th"] .park-faq-card-answer-text {
  font-size: 1.05rem;
}

.park-faq-cards-container[data-lang="th"] .park-faq-card-answer-text {
  line-height: 1.8;
}
</style>

<?php
}
?>
