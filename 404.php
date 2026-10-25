<?php defined('ABSPATH') || exit; get_header(); get_template_part('frontend/views/layout/header'); ?>
<main id="main" class="standalone"><p class="eyebrow red">404</p><h1>PAGE NOT<br><em>FOUND.</em></h1><a class="button" href="<?php echo esc_url(home_url('/')); ?>">Back to home ↗</a></main>
<?php get_template_part('frontend/views/layout/footer'); get_footer(); ?>
