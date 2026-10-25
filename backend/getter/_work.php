<?php
defined('ABSPATH') || exit;
/** Only the fields needed by the project row; galleries are loaded on opening. */
function fex_work_card_data(int $id): array {
    $preview = get_media_by_id((int) fex_post_value($id, 'fex_preview', get_post_thumbnail_id($id)), 'fex-card');
    if ($preview && $preview['type'] !== 'image') { $preview = false; }
    $media = get_media_by_id((int) fex_post_value($id, 'fex_media'), 'fex-card') ?: $preview;
    $preview_video = get_media_by_id((int) fex_post_value($id, 'fex_preview_video'));
    if ($preview_video && $preview_video['type'] !== 'video') { $preview_video = false; }
    return [
        'id' => $id, 'title' => get_the_title($id), 'url' => get_permalink($id),
        'subtitle' => (string) fex_post_value($id, 'fex_subtitle'),
        'position' => fex_position(fex_post_value($id, 'fex_position', 10)),
        'preview' => $preview, 'media' => $media, 'preview_video' => $preview_video,
        'fit' => fex_post_value($id, 'fex_fit', 'cover') === 'contain' ? 'contain' : 'cover',
    ];
}
function fex_work_data(int $id): array {
    $work = fex_work_card_data($id);
    if ($work['media']) { $work['media'] = get_media_by_id($work['media']['id'], 'full'); }
    $model = get_media_by_id((int) fex_post_value($id, 'fex_model'));
    if ($model && $model['type'] !== 'model') { $model = false; }
    $gallery = [];
    foreach ((array) fex_post_value($id, 'fex_gallery', []) as $item) {
        if (!is_array($item)) { continue; }
        $file = get_media_by_id((int) ($item['file'] ?? 0), 'full');
        if ($file) { $file['caption'] = sanitize_text_field($item['caption'] ?? ''); $gallery[] = $file; }
    }
    $terms = get_the_terms($id, 'fex_category');
    return array_merge($work, [
        'description' => wp_kses_post((string) fex_post_value($id, 'fex_description')),
        'model' => $model,
        'model_rotate' => fex_post_value($id, 'fex_model_rotate', 'on') === 'on',
        'model_animate' => fex_post_value($id, 'fex_model_animate', 'off') === 'on',
        'gallery' => $gallery, 'categories' => is_array($terms) ? wp_list_pluck($terms, 'name') : [],
    ]);
}
/** One query for all published work, then build every category row dynamically. */
function fex_rows(): array {
    $terms = get_terms(['taxonomy' => 'fex_category', 'hide_empty' => false]);
    if (is_wp_error($terms)) { $terms = []; }
    $term_positions = [];
    foreach ($terms as $term) { $term_positions[$term->term_id] = fex_position(fex_term_value($term->term_id, 'fex_position', 10)); }
    usort($terms, function ($a, $b) use ($term_positions) {
        $diff = $term_positions[$a->term_id] <=> $term_positions[$b->term_id];
        return $diff ?: strnatcasecmp($a->name, $b->name);
    });
    $posts = get_posts(['post_type' => 'fex_work', 'post_status' => 'publish', 'has_password' => false, 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC', 'suppress_filters' => false, 'update_post_meta_cache' => true, 'update_post_term_cache' => true, 'no_found_rows' => true]);
    $works = []; $assigned = [];
    foreach ($posts as $post) {
        $works[$post->ID] = fex_work_card_data($post->ID);
        // Reuse the taxonomy cache primed by the project query.
        $categories = get_the_terms($post->ID, 'fex_category');
        $assigned[$post->ID] = is_array($categories) ? array_map('intval', wp_list_pluck($categories, 'term_id')) : [];
    }
    uasort($works, function ($a, $b) { return ($a['position'] <=> $b['position']) ?: (strnatcasecmp($a['title'], $b['title']) ?: ($a['id'] <=> $b['id'])); });
    $rows = [];
    foreach ($terms as $term) {
        $items = array_filter($works, fn ($work) => in_array((int) $term->term_id, $assigned[$work['id']], true));
        if (!$items) { continue; } // Empty categories do not create empty bands.
        $rows[] = ['id' => 'kategorie-' . $term->term_id, 'title' => $term->name, 'note' => (string) fex_term_value($term->term_id, 'fex_row_note', wp_strip_all_tags($term->description)), 'works' => array_values($items)];
    }
    $uncategorized = array_filter($works, fn ($work) => empty($assigned[$work['id']]));
    if ($uncategorized) { $rows[] = ['id' => 'weitere-arbeiten', 'title' => 'More work', 'note' => '', 'works' => array_values($uncategorized)]; }
    return $rows;
}
