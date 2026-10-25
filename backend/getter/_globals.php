<?php
defined('ABSPATH') || exit;
function fex_settings(): array {
    $defaults = [
        'fex_name' => 'Fadri Collet', 'fex_brand' => 'FEX', 'fex_eyebrow' => 'FADRI COLLET / PORTFOLIO',
        'fex_title' => 'MY', 'fex_title_accent' => 'WORK.',
        'fex_intro' => 'I am Fadri. I work in 3D, animation, film and photography. Here is a selection of my projects.',
        'fex_background' => 0, 'fex_background_video' => 0,
        'fex_focus' => '3D / ANIMATION / FILM / PHOTOGRAPHY', 'fex_location' => 'SWITZERLAND',
        'fex_work_label' => 'SELECTED WORK',
        'fex_about_enabled' => 'on', 'fex_about_title' => 'ABOUT ME.',
        'fex_about_text' => 'I am Fadri. I work in 3D, animation, film and photography.',
        'fex_about_tools' => '', 'fex_about_image' => 0, 'fex_about_model' => 0,
        'fex_about_model_rotate' => 'on', 'fex_about_model_animate' => 'off',
        'fex_interests_enabled' => 'on', 'fex_interests_motion' => 'on',
        'fex_hover_preview' => 'on', 'fex_hero_parallax' => 'on',
        'fex_hero_glow' => 'on', 'fex_hover_arrows' => 'on',
        'fex_email' => 'fadri.collet@gmail.com', 'fex_contact_title' => 'CONTACT.',
        'fex_contact_text' => 'Have a question about a project or want to work together?',
        'fex_contact_identity' => "Fadri Collet\n3D / Animation / Film / Photography",
        'fex_footer_text' => 'Fadri Collet', 'fex_privacy_url' => '',
    ];
    $empty_allowed = ['fex_work_label', 'fex_about_text', 'fex_about_tools'];
    foreach (fex_interest_defaults() as $slug => $interest) {
        $prefix = 'fex_interest_' . $slug . '_';
        $defaults[$prefix . 'title'] = $interest['title'];
        $defaults[$prefix . 'text'] = $interest['text'];
        $defaults[$prefix . 'image'] = 0;
        $empty_allowed[] = $prefix . 'title';
        $empty_allowed[] = $prefix . 'text';
    }
    foreach ($defaults as $key => &$value) {
        if (!function_exists('carbon_get_theme_option')) { continue; }
        $saved = carbon_get_theme_option($key);
        if ($saved !== null && $saved !== false && $saved !== '') { $value = $saved; }
    }
    unset($value);
    // Saved empty titles hide an interest; saved empty copy stays empty.
    foreach ($empty_allowed as $key) {
        if (function_exists('carbon_get_theme_option') && get_option('_' . $key, null) !== null) {
            $defaults[$key] = (string) carbon_get_theme_option($key);
        }
    }
    $defaults['fex_email'] = sanitize_email((string) $defaults['fex_email']) ?: 'fadri.collet@gmail.com';
    $defaults['fex_privacy_url'] = esc_url_raw((string) $defaults['fex_privacy_url']) ?: get_privacy_policy_url();
    return $defaults;
}
