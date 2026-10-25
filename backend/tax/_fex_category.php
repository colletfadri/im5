<?php
defined('ABSPATH') || exit;
function fex_register_category(): void {
    register_taxonomy('fex_category', ['fex_work'], [
        'labels' => [
            'name' => 'Categories', 'singular_name' => 'Category', 'menu_name' => 'Categories',
            'all_items' => 'All categories', 'edit_item' => 'Edit category',
            'add_new_item' => 'Add a category', 'new_item_name' => 'New category name',
            'search_items' => 'Search categories',
        ],
        'public' => false, 'publicly_queryable' => false, 'show_ui' => true,
        'show_admin_column' => true, 'show_in_rest' => false, 'hierarchical' => true,
        'rewrite' => false,
    ]);
}
