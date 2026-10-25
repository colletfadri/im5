<?php
defined('ABSPATH') || exit;
get_header();
get_template_part('frontend/views/layout/header');
?>
<main id="main" class="standalone">
<a class="back-link" href="<?php echo esc_url(home_url('/#arbeiten')); ?>">← All projects</a>
<?php while (have_posts()) {
    the_post();
    if (post_password_required()) { echo get_the_password_form(); }
    else { get_template_part('frontend/views/pages/work', null, ['work' => fex_work_data(get_the_ID())]); }
} ?>
</main>
<?php get_template_part('frontend/views/layout/footer'); get_footer(); ?>
