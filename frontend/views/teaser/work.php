<?php $work = $args['work']; $number = $args['index']; ?>
<a class="project" data-work="<?php echo (int) $work['id']; ?>" href="<?php echo esc_url($work['url']); ?>" draggable="false" aria-label="<?php echo esc_attr('View ' . $work['title']); ?>" aria-haspopup="dialog">
<div class="project-img fit-<?php echo esc_attr($work['fit']); ?>">
<?php if ($work['preview']): ?>
<?php echo fex_image_html($work['preview'], 'fex-card', ['class' => 'card-media', 'sizes' => '(max-width: 700px) 82vw, (min-width: 1580px) 707px, calc(45.6vw - 13px)', 'draggable' => 'false']); ?>
<?php elseif ($work['media'] && $work['media']['type'] === 'image'): ?>
<?php echo fex_image_html($work['media'], 'fex-card', ['class' => 'card-media', 'sizes' => '(max-width: 700px) 82vw, (min-width: 1580px) 707px, calc(45.6vw - 13px)', 'draggable' => 'false']); ?>
<?php else: ?><span class="image-placeholder"><?php echo esc_html($work['title']); ?></span><?php endif; ?>
<?php if ($work['preview_video']): ?><video class="card-preview" data-src="<?php echo esc_url($work['preview_video']['url']); ?>" muted loop playsinline preload="none" aria-hidden="true" tabindex="-1"></video><?php endif; ?>
<span class="image-index"><?php echo esc_html(sprintf('%03d / %s', $number, $work['subtitle'])); ?></span><span class="media-label"><?php echo $work['media'] && $work['media']['type'] === 'video' ? 'WATCH VIDEO' : 'VIEW PROJECT'; ?></span><span class="open" aria-hidden="true">↗</span>
</div><div class="caption"><h3><?php echo esc_html($work['title']); ?></h3><p><?php echo esc_html($work['subtitle']); ?></p></div>
</a>
