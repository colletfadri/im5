<main id="main" class="standalone prose-page">
<?php while (have_posts()): the_post(); ?><h1><?php the_title(); ?></h1><?php if (function_exists('render_modules')) { render_modules(); } ?><?php endwhile; ?>
</main>
