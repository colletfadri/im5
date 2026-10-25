<?php
defined('ABSPATH') || exit;
// Carbon Fields wird wie im Starter als WordPress-Plugin verwendet.
add_action('carbon_fields_register_fields', function () {
    require_once __DIR__ . '/_globals.php';
    require_once __DIR__ . '/_work.php';
    require_once __DIR__ . '/_category.php';
    require_once __DIR__ . '/_modules.php';
});
