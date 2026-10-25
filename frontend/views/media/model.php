<?php
defined('ABSPATH') || exit;
$model = $args['model'] ?? false;
if (!$model || $model['type'] !== 'model') { return; }
$poster = $args['poster'] ?? false;
if ($poster && $poster['type'] !== 'image') { $poster = false; }
$title = (string) ($args['title'] ?? $model['title']);
?>
<div class="model-stage" data-model-stage
    data-model-src="<?php echo esc_url($model['url']); ?>"
    data-model-alt="<?php echo esc_attr($title . ' – interactive 3D model'); ?>"
    data-model-rotate="<?php echo !empty($args['rotate']) ? 'true' : 'false'; ?>"
    data-model-animate="<?php echo !empty($args['animate']) ? 'true' : 'false'; ?>"
    <?php if ($poster): ?>data-model-poster="<?php echo esc_url($poster['url']); ?>"<?php endif; ?>>
    <div class="model-canvas">
        <?php if ($poster): echo fex_image_html($poster, 'large', ['class' => 'model-poster', 'sizes' => '(min-width: 1200px) 1100px, 92vw']); ?>
        <?php else: ?><div class="model-poster model-placeholder" aria-hidden="true"><span>3D</span><i></i></div><?php endif; ?>
    </div>
    <div class="model-overlay"><button class="button model-start" type="button">View in 3D <span aria-hidden="true">↗</span></button></div>
    <div class="model-toolbar"><p class="model-status" role="status" aria-live="polite">Interactive 3D model</p><button class="model-motion" type="button" hidden>Pause motion</button></div>
    <noscript><p class="model-nojs">Enable JavaScript to use the 3D viewer. <a href="<?php echo esc_url($model['url']); ?>" download>Download model</a></p></noscript>
</div>
