<?php
$cfg = fex_settings(); $rows = fex_rows();
$interests = fex_interest_data($cfg);
$background = get_media_by_id((int) $cfg['fex_background'], 'full');
$background_video = get_media_by_id((int) $cfg['fex_background_video']);
if ($background_video && $background_video['type'] !== 'video') { $background_video = false; }
?>
<main id="main">
<section class="hero" id="start">
<?php if ($background && $background['type'] === 'image'): ?>
<?php echo fex_image_html($background, 'full', ['class' => 'hero-art custom-hero', 'alt' => '', 'loading' => 'eager', 'fetchpriority' => 'high', 'sizes' => '(max-width: 700px) 100vw, 77vw']); ?>
<?php else: ?><div class="hero-art default-hero" aria-hidden="true"></div><?php endif; ?>
<?php if ($background_video): ?><video class="hero-art custom-hero hero-video" data-src="<?php echo esc_url($background_video['url']); ?>" muted loop playsinline preload="none" aria-hidden="true" tabindex="-1"<?php if ($background && $background['type'] === 'image') { echo ' poster="' . esc_url($background['url']) . '"'; } ?>></video><?php endif; ?>
<div class="hero-shade"></div>
<div class="hero-glow" aria-hidden="true"></div>
<div class="hero-copy">
<p class="eyebrow"><i aria-hidden="true"></i><?php echo esc_html($cfg['fex_eyebrow']); ?></p>
<h1><?php echo nl2br(esc_html($cfg['fex_title'])); ?><br><em><?php echo nl2br(esc_html($cfg['fex_title_accent'])); ?></em></h1>
<p class="intro"><?php echo esc_html($cfg['fex_intro']); ?></p>
<a class="button" href="#arbeiten">View projects <span aria-hidden="true">↗</span></a>
</div>
<?php if ($background_video): ?><button class="hero-video-toggle" type="button" hidden>Pause background</button><?php endif; ?>
<div class="hero-bottom"><span><?php echo esc_html($cfg['fex_location'] . ' / ' . wp_date('Y')); ?></span><a href="#arbeiten">EXPLORE THE PROJECTS <span class="down" aria-hidden="true">↓</span></a><span class="right-label"><?php echo esc_html($cfg['fex_focus']); ?></span></div>
</section>
<?php if (!$rows): ?>
<section id="arbeiten" class="empty-state"><p class="eyebrow red">WORK</p><h2>PROJECTS.</h2><p>My projects will appear here soon.</p><?php if (current_user_can('edit_posts')): ?><p><a href="<?php echo esc_url(admin_url('post-new.php?post_type=fex_work')); ?>">Add your first project ↗</a></p><?php endif; ?><span id="galerie"></span></section>
<?php endif; ?>
<?php foreach ($rows as $index => $row): $anchor = $index === 0 ? 'arbeiten' : ($index === 1 ? 'galerie' : $row['id']); ?>
<section class="project-row<?php echo $index % 2 ? ' alternate' : ''; ?>" id="<?php echo esc_attr($anchor); ?>" aria-labelledby="row-title-<?php echo esc_attr($row['id']); ?>">
<div class="section-head"><div><?php if ($cfg['fex_work_label'] !== ''): ?><p class="eyebrow red"><?php echo esc_html(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) . ' / ' . $cfg['fex_work_label']); ?></p><?php endif; ?><h2 id="row-title-<?php echo esc_attr($row['id']); ?>"><?php echo esc_html($row['title']); ?><span>.</span></h2></div><p class="section-note"><?php echo esc_html($row['note']); ?></p></div>
<div class="track" tabindex="0" aria-label="<?php echo esc_attr($row['title'] . ' – horizontally scrollable projects'); ?>">
<?php foreach ($row['works'] as $i => $work) { get_template_part('frontend/views/teaser/work', null, ['work' => $work, 'index' => $i + 1]); } ?>
</div>
<div class="section-foot"><span><?php echo esc_html(sprintf('%02d %s', count($row['works']), count($row['works']) === 1 ? 'PROJECT' : 'PROJECTS')); ?></span><div class="progress" aria-hidden="true"><i></i></div><div class="gallery-controls"><button class="gallery-prev" type="button" aria-label="Previous projects">←</button><button class="gallery-next" type="button" aria-label="Next projects">→</button><span class="gallery-hint">DRAG OR SCROLL →</span></div></div>
</section>
<?php endforeach; ?>
<?php if (count($rows) === 1): ?><span id="galerie"></span><?php endif; ?>
<?php get_template_part('frontend/views/pages/interests', null, ['items' => $interests, 'motion' => $cfg['fex_interests_motion'] === 'on', 'number' => count($rows) + 1]); ?>
<?php get_template_part('frontend/views/pages/about', null, ['settings' => $cfg, 'number' => count($rows) + 1, 'has_interests' => (bool) $interests]); ?>
<?php get_template_part('frontend/views/layout/footer', null, ['number' => count($rows) + ($cfg['fex_about_enabled'] === 'on' ? 2 : 1)]); ?>
</main>
<dialog id="project-dialog" aria-label="Project details"><button class="close" type="button" aria-label="Close project details">×</button><div class="modal-content"></div></dialog>
