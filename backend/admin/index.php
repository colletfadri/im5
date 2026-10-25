<?php
defined('ABSPATH') || exit;
add_action('admin_notices', function () {
    if (!current_user_can('manage_options')) { return; }
    if (!function_exists('carbon_get_post_meta')) {
        echo '<div class="notice notice-warning"><p><strong>FEX Portfolio:</strong> Install and activate Carbon Fields to enable project fields and portfolio settings. <a href="https://carbonfields.net/zip/latest/">Official download</a>. The website remains available without the plugin.</p></div>';
    }
});
add_filter('manage_fex_work_posts_columns', function ($columns) {
    $columns['fex_position'] = 'Position';
    return $columns;
});
add_action('manage_fex_work_posts_custom_column', function ($column, $id) {
    if ($column === 'fex_position') { echo esc_html((string) fex_position(fex_post_value((int) $id, 'fex_position', 10))); }
}, 10, 2);
add_filter('manage_edit-fex_category_columns', function ($columns) { $columns['fex_position'] = 'Row order'; return $columns; });
add_filter('manage_fex_category_custom_column', function ($value, $column, $id) {
    return $column === 'fex_position' ? esc_html((string) fex_position(fex_term_value((int) $id, 'fex_position', 10))) : $value;
}, 10, 3);
add_action('admin_menu', function () {
    add_submenu_page('edit.php?post_type=fex_work', 'Guide', 'Guide', 'edit_posts', 'fex-help', function () {
        echo '<div class="wrap"><h1>Editing your portfolio</h1><ol>';
        echo '<li><strong>Projects → Add project:</strong> Enter a title, short line, preview image and project details.</li>';
        echo '<li><strong>Categories:</strong> Choose one or more categories. A new category becomes a project row when it contains published projects.</li>';
        echo '<li><strong>Position:</strong> You can change the order at any time. Lower numbers appear first. For example, use 10, 20, 30 and insert 15 between the first two.</li>';
        echo '<li><strong>Gallery:</strong> Add images, GIFs, videos or PDFs and drag the entries to reorder them.</li>';
        echo '<li><strong>Portfolio settings:</strong> Edit the title, introduction, background image/GIF, background video and email. Use the Projects tab to edit the small label above each row.</li>';
        echo '<li><strong>About me:</strong> Add a personal introduction, tools, a portrait or a GLB model. This section appears after the projects and can be hidden.</li>';
        echo '<li><strong>Interests:</strong> Edit the three headings, texts and images under Portfolio settings → Interests. Leave a heading empty to hide its section. Scroll animation can be switched off.</li>';
        echo '<li><strong>3D model:</strong> Choose a glTF Binary (.glb) file in a project or under About me. It loads after clicking View in 3D. Add a poster image.</li>';
        echo '<li><strong>Interaction:</strong> Configure the large cursor preview, background motion, white spotlight and moving arrows.</li>';
        echo '<li><strong>Appearance → Menus:</strong> Optionally create a main menu and footer links for legal and privacy pages.</li>';
        echo '<li><strong>Tools → English content:</strong> Preview and translate the original German copy already saved in WordPress. Custom text is kept for you to review.</li>';
        echo '</ol><p>This theme includes no demo projects or sample images. Add your work under Projects and choose your own images in Portfolio settings. Existing WordPress content is preserved.</p></div>';
    });
});
