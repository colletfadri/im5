<?php
$work = $args['work']; $media = $work['media'];
// A model's poster is already visible in its 3D stage; keep any separate main medium.
if (!empty($work['model']) && $media && $work['preview'] && $media['id'] === $work['preview']['id']) { $media = false; }
?>
<article class="work-detail">
<?php if (!empty($work['model'])) { get_template_part('frontend/views/media/model', null, ['model' => $work['model'], 'poster' => $work['preview'], 'title' => $work['title'], 'rotate' => $work['model_rotate'], 'animate' => $work['model_animate']]); } ?>
<?php if ($media): ?><div class="detail-media">
<?php if ($media['type'] === 'video'): ?><video controls playsinline preload="none"<?php if ($work['preview']) { echo ' poster="' . esc_url($work['preview']['url']) . '"'; } ?> aria-label="<?php echo esc_attr($work['title']); ?>"><source src="<?php echo esc_url($media['url']); ?>" type="<?php echo esc_attr($media['mime']); ?>">Your browser cannot play this video. <a href="<?php echo esc_url($media['url']); ?>">Open video</a></video>
<?php elseif ($media['type'] === 'image'): echo fex_image_html($media, 'large', ['sizes' => '(min-width: 1200px) 1100px, 92vw']); ?>
<?php elseif ($media['type'] === 'model'): get_template_part('frontend/views/media/model', null, ['model' => $media, 'poster' => $work['preview'], 'title' => $work['title'], 'rotate' => true]); ?>
<?php else: ?><a class="document-link" href="<?php echo esc_url($media['url']); ?>" target="_blank" rel="noopener">Open PDF ↗</a><?php endif; ?>
</div><?php endif; ?>
<div class="detail-copy"><p class="eyebrow red"><?php echo esc_html(implode(' / ', $work['categories'])); ?></p><h2 class="detail-title"><?php echo esc_html($work['title']); ?></h2><p class="detail-subtitle"><?php echo esc_html($work['subtitle']); ?></p><div class="prose"><?php echo wp_kses_post($work['description']); ?></div>
<?php if ($work['gallery']): ?><div class="detail-gallery">
<?php foreach ($work['gallery'] as $file): ?><figure>
<?php if ($file['type'] === 'image'): ?><a href="<?php echo esc_url($file['url']); ?>" target="_blank" rel="noopener"><?php echo fex_image_html($file, 'large', ['sizes' => '(max-width: 700px) 88vw, (min-width: 1200px) 500px, 44vw']); ?></a>
<?php elseif ($file['type'] === 'video'): ?><video controls playsinline preload="none" aria-label="<?php echo esc_attr($file['caption'] ?: $file['title']); ?>"><source src="<?php echo esc_url($file['url']); ?>" type="<?php echo esc_attr($file['mime']); ?>"></video>
<?php elseif ($file['type'] === 'model'): get_template_part('frontend/views/media/model', null, ['model' => $file, 'title' => $file['caption'] ?: $file['title']]); ?>
<?php else: ?><a class="document-link" href="<?php echo esc_url($file['url']); ?>" target="_blank" rel="noopener"><?php echo esc_html($file['caption'] ?: $file['title']); ?> · PDF ↗</a><?php endif; ?>
<?php if ($file['caption']): ?><figcaption><?php echo esc_html($file['caption']); ?></figcaption><?php endif; ?>
</figure><?php endforeach; ?>
</div><?php endif; ?></div>
</article>
