<?php $data = $args['data']; ?>
<section data-module="example" class="page-image">
<?php if ($data['image']): echo fex_image_html($data['image'], 'large', ['sizes' => '(min-width: 1200px) 1000px, 90vw']); endif; ?>
<?php if ($data['title']): ?><h2><?php echo esc_html($data['title']); ?></h2><?php endif; ?>
</section>
