<?php
defined('ABSPATH') || exit;
$items = $args['items'] ?? [];
if (!$items) { return; }
?>
<section class="interests" id="ueber-mich" aria-label="My interests" data-interest-motion="<?php echo $args['motion'] ? 'on' : 'off'; ?>">
    <p class="eyebrow red interest-eyebrow"><?php echo esc_html(sprintf('%02d / INTERESTS', (int) $args['number'])); ?></p>
    <?php foreach ($items as $index => $item): ?>
    <article class="interest interest--<?php echo esc_attr($item['slug']); ?><?php echo $index % 2 ? ' interest--reverse' : ''; ?><?php echo !$item['image'] ? ' interest--text-only' : ''; ?>" data-interest aria-labelledby="interest-<?php echo esc_attr($item['slug']); ?>">
        <div class="interest-copy">
            <span class="interest-number" aria-hidden="true"><?php echo esc_html(sprintf('%02d', $index + 1)); ?></span>
            <h2 id="interest-<?php echo esc_attr($item['slug']); ?>"><?php foreach (preg_split('/\R/u', $item['title']) as $line): if (trim($line) === '') { continue; } ?><span class="interest-title-line"><?php echo esc_html($line); ?></span><?php endforeach; ?></h2>
            <?php if (trim($item['text']) !== ''): ?><div class="interest-text prose"><?php echo wpautop(esc_html($item['text'])); ?></div><?php endif; ?>
        </div>
        <?php if ($item['image']): ?><figure class="interest-visual">
            <?php echo fex_image_html($item['image'], 'large', ['sizes' => '(max-width: 700px) 88vw, (min-width: 1360px) 600px, 46vw']); ?>
            <?php if ($item['slug'] === 'film'): ?><span class="interest-viewfinder" aria-hidden="true"></span><?php endif; ?>
        </figure><?php endif; ?>
    </article>
    <?php endforeach; ?>
</section>
