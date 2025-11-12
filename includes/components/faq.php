<?php

function renderFAQSection($faqs, $title = "常見問題") {
    if (empty($faqs)) {
        return;
    }

    ?>
    <div class="faq-section">
        <h3><?= htmlspecialchars($title) ?></h3>

        <?php foreach ($faqs as $index => $faq): ?>
        <div class="faq-item" data-faq-index="<?= $index ?>">
            <div class="faq-question">
                <span><?= htmlspecialchars($faq['q']) ?></span>
                <i class="material-icons faq-icon">expand_more</i>
            </div>
            <div class="faq-answer">
                <?= $faq['a'] ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Schema.org FAQPage structured data for SEO -->
    <script type="application/ld+json" class="faq-schema">
    {
        "@context": "https://schema.org",
        "@type": "FAQPage",
        "mainEntity": [
            <?php foreach ($faqs as $index => $faq): ?>
            {
                "@type": "Question",
                "name": "<?= addslashes(strip_tags($faq['q'])) ?>",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "<?= addslashes(strip_tags($faq['a'])) ?>"
                }
            }<?= ($index < count($faqs) - 1) ? ',' : '' ?>
            <?php endforeach; ?>
        ]
    }
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const faqItems = document.querySelectorAll('.faq-item');

        faqItems.forEach(item => {
            const question = item.querySelector('.faq-question');

            question.addEventListener('click', function() {
                const isActive = item.classList.contains('active');
                faqItems.forEach(i => i.classList.remove('active'));
                if (!isActive) {
                    item.classList.add('active');
                }
            });
        });
    });
    </script>
    <?php
}
