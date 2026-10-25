<?php
defined('ABSPATH') || exit;
function fex_register_work(): void {
    register_post_type('fex_work', [
        'labels' => [
            'name' => 'Projects', 'singular_name' => 'Project', 'menu_name' => 'Projects',
            'all_items' => 'All projects', 'add_new' => 'Add project', 'add_new_item' => 'Add a project',
            'edit_item' => 'Edit project', 'new_item' => 'Add project', 'view_item' => 'View project',
            'search_items' => 'Search projects', 'not_found' => 'No projects found',
            'not_found_in_trash' => 'No projects in the bin',
        ],
        'public' => true, 'show_ui' => true, 'show_in_rest' => false,
        'has_archive' => false, 'hierarchical' => false,
        'supports' => ['title', 'thumbnail', 'revisions'],
        'menu_icon' => 'dashicons-format-gallery', 'menu_position' => 20,
        'rewrite' => ['slug' => 'arbeit', 'with_front' => false],
    ]);
}
