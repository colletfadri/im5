<?php
defined('ABSPATH') || exit;
add_action('after_setup_theme', function () {
    register_nav_menus(['main' => 'Main navigation', 'footer' => 'Footer / legal pages']);
});
