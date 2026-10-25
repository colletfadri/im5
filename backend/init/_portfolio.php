<?php
defined('ABSPATH') || exit;
add_action('after_setup_theme', function () {
    load_theme_textdomain('fex-portfolio', get_template_directory() . '/languages');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'gallery', 'caption', 'style', 'script']);
    add_image_size('fex-card', 1400, 900, false);
});
add_action('wp_enqueue_scripts', function () {
    $base = get_template_directory();
    wp_enqueue_style('fex-fonts', get_template_directory_uri() . '/frontend/styles/_fonts.css', [], (string) filemtime($base . '/frontend/styles/_fonts.css'));
    wp_enqueue_style('fex-main', get_template_directory_uri() . '/frontend/styles/main.css', ['fex-fonts'], (string) filemtime($base . '/frontend/styles/main.css'));
    wp_enqueue_script('fex-main', get_template_directory_uri() . '/frontend/scripts/main.js', [], (string) filemtime($base . '/frontend/scripts/main.js'), true);
    $cfg = fex_settings();
    wp_add_inline_script('fex-main', 'window.fexPortfolio = ' . wp_json_encode([
        'hoverPreview' => $cfg['fex_hover_preview'] === 'on',
        'heroParallax' => $cfg['fex_hero_parallax'] === 'on',
        'heroGlow' => $cfg['fex_hero_glow'] === 'on',
        'hoverArrows' => $cfg['fex_hover_arrows'] === 'on',
        'modelModule' => get_template_directory_uri() . '/frontend/vendor/model-viewer/model-viewer.min.js',
        'modelAssets' => get_template_directory_uri() . '/frontend/vendor/model-viewer/',
        'projectEndpoint' => rest_url('fex/v1/work/'),
    ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . ';', 'before');
    if (function_exists('wp_script_add_data')) { wp_script_add_data('fex-main', 'strategy', 'defer'); }
    foreach (['footer-fish', 'interests'] as $feature) {
        if ($feature === 'interests' && !is_front_page()) { continue; }
        $handle = 'fex-' . $feature;
        wp_enqueue_script($handle, get_template_directory_uri() . '/frontend/scripts/' . $feature . '.js', [], (string) filemtime($base . '/frontend/scripts/' . $feature . '.js'), true);
        if (function_exists('wp_script_add_data')) { wp_script_add_data($handle, 'strategy', 'defer'); }
    }
});
add_action('after_switch_theme', function () {
    // Registration also runs on init; register here before flushing on activation.
    fex_register_work();
    fex_register_category();
    flush_rewrite_rules();
});
