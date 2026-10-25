<?php
defined('ABSPATH') || exit;
/** Safe media access shared by the getters and templates. */
function get_media_by_id(int|string $id, string $size = 'large'): array|false {
    $id = absint($id);
    if (!$id || get_post_type($id) !== 'attachment') { return false; }
    $mime = (string) get_post_mime_type($id);
    $type = str_starts_with($mime, 'image/') ? 'image' : (str_starts_with($mime, 'video/') ? 'video' : ($mime === 'application/pdf' ? 'pdf' : ($mime === 'model/gltf-binary' ? 'model' : '')));
    if (!$type) { return false; }
    // Resized GIFs can lose animation. Always keep the original GIF source.
    if ($mime === 'image/gif') { $size = 'full'; }
    $url = $type === 'image' ? wp_get_attachment_image_url($id, $size) : wp_get_attachment_url($id);
    if (!$url) { return false; }
    return ['id' => $id, 'type' => $type, 'mime' => $mime, 'url' => $url, 'src' => $url,
        'title' => get_the_title($id), 'alt' => (string) get_post_meta($id, '_wp_attachment_image_alt', true) ?: get_the_title($id)];
}
/** Use WordPress responsive sources and dimensions for every uploaded image. */
function fex_image_html(array|false $image, string $size = 'large', array $attributes = []): string {
    if (!$image || $image['type'] !== 'image') { return ''; }
    $attributes = array_merge(['alt' => $image['alt'], 'loading' => 'lazy', 'decoding' => 'async'], $attributes);
    if ($image['mime'] === 'image/gif') {
        $size = 'full';
        // A non-empty srcset avoids WordPress generating resized static GIF candidates.
        $attributes['srcset'] = $image['url'] . ' 1x';
        unset($attributes['sizes']);
    }
    return wp_get_attachment_image((int) $image['id'], $size, false, $attributes);
}
function get_menu_by_slug(string $slug): array {
    $locations = get_nav_menu_locations();
    if (empty($locations[$slug])) { return []; }
    return array_map(function ($item) {
        return ['title' => $item->title, 'url' => $item->url, 'target' => $item->target];
    }, wp_get_nav_menu_items($locations[$slug]) ?: []);
}
function fex_position(mixed $value): int { return max(0, is_numeric($value) ? (int) $value : 10); }
function fex_post_value(int $id, string $key, mixed $default = ''): mixed {
    if (!function_exists('carbon_get_post_meta')) { return $default; }
    $value = carbon_get_post_meta($id, $key);
    return ($value === '' || $value === null || $value === false) ? $default : $value;
}
function fex_term_value(int $id, string $key, mixed $default = ''): mixed {
    if (!function_exists('carbon_get_term_meta')) { return $default; }
    $value = carbon_get_term_meta($id, $key);
    return ($value === '' || $value === null || $value === false) ? $default : $value;
}
