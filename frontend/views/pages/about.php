<?php
defined('ABSPATH') || exit;
$cfg = $args['settings'];
if ($cfg['fex_about_enabled'] !== 'on') { return; }
$image = get_media_by_id((int) $cfg['fex_about_image'], 'full');
if ($image && $image['type'] !== 'image') { $image = false; }
$model = get_media_by_id((int) $cfg['fex_about_model']);
if ($model && $model['type'] !== 'model') { $model = false; }
$tools = array_filter(array_map('trim', explode(',', (string) $cfg['fex_about_tools'])));
?>
<section class="about<?php echo $image || $model ? ' about-with-media' : ''; ?>" id="<?php echo !empty($args['has_interests']) ? 'ueber-mich-details' : 'ueber-mich'; ?>" aria-labelledby="about-title">
    <div class="about-copy">
        <p class="eyebrow red"><?php echo esc_html(!empty($args['has_interests']) ? 'PERSONAL' : sprintf('%02d / PERSONAL', (int) $args['number'])); ?></p>
        <h2 id="about-title"><?php echo esc_html($cfg['fex_about_title']); ?></h2>
        <?php if ($cfg['fex_about_text'] !== ''): ?><div class="about-text prose"><?php echo wpautop(esc_html($cfg['fex_about_text'])); ?></div><?php endif; ?>
        <?php if ($tools): ?><ul class="about-tools" aria-label="Tools and areas of focus"><?php foreach ($tools as $tool): ?><li><?php echo esc_html($tool); ?></li><?php endforeach; ?></ul><?php endif; ?>
        <a class="button" href="#kontakt">Get in touch <span aria-hidden="true">↗</span></a>
    </div>
    <?php if ($image || $model): ?><div class="about-media">
        <?php if ($model): get_template_part('frontend/views/media/model', null, ['model' => $model, 'poster' => $image, 'title' => 'About me', 'rotate' => $cfg['fex_about_model_rotate'] === 'on', 'animate' => $cfg['fex_about_model_animate'] === 'on']);
        elseif ($image): echo fex_image_html($image, 'large', ['class' => 'about-image', 'sizes' => '(max-width: 700px) 88vw, (min-width: 1440px) 650px, 48vw']); endif; ?>
    </div><?php endif; ?>
</section>
