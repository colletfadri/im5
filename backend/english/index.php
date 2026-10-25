<?php
/** A previewed, optional conversion of the original saved German copy. */
defined('ABSPATH') || exit;

add_action('admin_menu', function () {
    add_management_page('English content', 'English content', 'manage_options', 'fex-english', 'fex_english_page');
});
add_action('admin_post_fex_apply_english', 'fex_english_handle');

function fex_english_ready(): bool {
    foreach (['carbon_get_theme_option', 'carbon_set_theme_option', 'carbon_get_post_meta', 'carbon_set_post_meta', 'carbon_get_term_meta', 'carbon_set_term_meta'] as $function) {
        if (!function_exists($function)) { return false; }
    }
    return true;
}

function fex_english_translation(string $text): ?string {
    static $catalogue;
    if ($catalogue === null) {
        $catalogue = json_decode((string) file_get_contents(__DIR__ . '/translations.json'), true) ?: [];
    }
    $normal = trim(str_replace(["\r\n", "\r"], "\n", $text));
    if (!isset($catalogue[$normal])) { $normal = html_entity_decode($normal, ENT_QUOTES | ENT_HTML5, 'UTF-8'); }
    return isset($catalogue[$normal]) && $catalogue[$normal] !== $text ? $catalogue[$normal] : null;
}

function fex_english_needs_review(string $text): bool {
    // A hint, not language detection. Names and embedded artwork may stay original.
    return preg_match('/[äöüÄÖÜß]|\b(ich|mein|meine|meinen|mich|und|oder|eine|einer|einem|einen|das|dass|mit|für|über|hier|arbeiten|kontakt|fotografie|welten|erschaffen)\b/ui', wp_strip_all_tags($text)) === 1;
}

function fex_english_add_text(array &$plan, string $kind, int $id, string $field, $value, string $label, string $url): void {
    if (!is_string($value) || trim($value) === '') { return; }
    $translation = fex_english_translation($value);
    $entry = ['kind' => $kind, 'id' => $id, 'field' => $field, 'before' => $value, 'label' => $label, 'url' => $url];
    if ($translation !== null) {
        $entry['after'] = $translation;
        $plan['changes'][] = $entry;
    } elseif (fex_english_needs_review($value)) {
        $plan['review'][] = $entry;
    }
}

function fex_english_plan(): array {
    $plan = ['changes' => [], 'review' => []];
    if (!fex_english_ready()) { return $plan; }
    $settings_url = admin_url('admin.php?page=fex-settings');
    foreach (array_keys(fex_settings()) as $field) {
        if (in_array($field, ['fex_name', 'fex_brand', 'fex_email', 'fex_privacy_url'], true)) { continue; }
        $label = 'Portfolio settings / ' . str_replace('_', ' ', substr($field, 4));
        fex_english_add_text($plan, 'option', 0, $field, carbon_get_theme_option($field), $label, $settings_url);
    }
    $posts = get_posts(['post_type' => 'fex_work', 'post_status' => 'any', 'posts_per_page' => -1, 'orderby' => 'ID', 'order' => 'ASC']);
    foreach ($posts as $post) {
        $id = (int) $post->ID;
        $label = 'Project: ' . $post->post_title;
        $url = (string) get_edit_post_link($id, 'raw');
        foreach (['fex_subtitle' => 'short line', 'fex_description' => 'description'] as $field => $name) {
            fex_english_add_text($plan, 'post_meta', $id, $field, carbon_get_post_meta($id, $field), $label . ' / ' . $name, $url);
        }
        $gallery = carbon_get_post_meta($id, 'fex_gallery');
        if (!is_array($gallery)) { continue; }
        $translated = $gallery;
        foreach ($gallery as $index => $item) {
            if (!is_array($item) || !isset($item['caption']) || !is_string($item['caption'])) { continue; }
            $text = $item['caption'];
            $translation = fex_english_translation($text);
            if ($translation !== null) {
                $translated[$index]['caption'] = $translation;
            } elseif (trim($text) !== '' && fex_english_needs_review($text)) {
                $plan['review'][] = ['label' => $label . ' / gallery caption', 'before' => $text, 'url' => $url];
            }
        }
        if ($translated !== $gallery) {
            $plan['changes'][] = ['kind' => 'post_meta', 'id' => $id, 'field' => 'fex_gallery', 'before' => $gallery, 'after' => $translated, 'label' => $label . ' / gallery captions', 'url' => $url];
        }
    }
    $terms = get_terms(['taxonomy' => 'fex_category', 'hide_empty' => false, 'orderby' => 'term_id', 'order' => 'ASC']);
    if (!is_wp_error($terms)) {
        foreach ($terms as $term) {
            $id = (int) $term->term_id;
            $url = (string) get_edit_term_link($id, 'fex_category');
            $label = 'Category: ' . $term->name;
            foreach (['name', 'description'] as $field) {
                fex_english_add_text($plan, 'term', $id, $field, $term->$field, $label . ' / ' . $field, $url);
            }
            fex_english_add_text($plan, 'term_meta', $id, 'fex_row_note', carbon_get_term_meta($id, 'fex_row_note'), $label . ' / row note', $url);
        }
    }
    $locations = get_nav_menu_locations();
    $menus = array_unique(array_filter([(int) ($locations['main'] ?? 0), (int) ($locations['footer'] ?? 0)]));
    foreach ($menus as $menu) {
        foreach ((array) wp_get_nav_menu_items($menu) as $item) {
            $id = (int) $item->ID;
            // Raw stored title: generated titles may belong to a linked page.
            $title = get_post_field('post_title', $id, 'raw');
            fex_english_add_text($plan, 'post', $id, 'post_title', $title, 'Menu label: ' . $title, admin_url('nav-menus.php?menu=' . $menu));
        }
    }
    // Pages are reviewed manually so legal or independently written copy is never replaced.
    foreach (get_posts(['post_type' => 'page', 'post_status' => 'any', 'posts_per_page' => -1]) as $page) {
        if (fex_english_needs_review($page->post_title . ' ' . $page->post_content)) {
            $plan['review'][] = ['label' => 'Page: ' . $page->post_title, 'before' => $page->post_title, 'url' => (string) get_edit_post_link($page->ID, 'raw')];
        }
    }
    return $plan;
}

function fex_english_fingerprint(array $changes): string {
    return hash('sha256', (string) wp_json_encode($changes));
}

function fex_english_current(array $change) {
    switch ($change['kind']) {
        case 'option': return carbon_get_theme_option($change['field']);
        case 'post_meta': return carbon_get_post_meta($change['id'], $change['field']);
        case 'term_meta': return carbon_get_term_meta($change['id'], $change['field']);
        case 'post': return get_post_field($change['field'], $change['id'], 'raw');
        case 'term':
            $term = get_term($change['id'], 'fex_category');
            return $term && !is_wp_error($term) ? $term->{$change['field']} : null;
    }
    return null;
}

function fex_english_apply(array $changes): array {
    $report = ['applied' => 0, 'skipped' => 0];
    foreach ($changes as $change) {
        if (fex_english_current($change) !== $change['before']) { $report['skipped']++; continue; }
        $result = true;
        switch ($change['kind']) {
            case 'option': carbon_set_theme_option($change['field'], $change['after']); break;
            case 'post_meta': carbon_set_post_meta($change['id'], $change['field'], $change['after']); break;
            case 'term_meta': carbon_set_term_meta($change['id'], $change['field'], $change['after']); break;
            case 'post': $result = wp_update_post(wp_slash(['ID' => $change['id'], $change['field'] => $change['after']]), true); break;
            case 'term': $result = wp_update_term($change['id'], 'fex_category', [$change['field'] => $change['after']]); break;
        }
        $saved = fex_english_current($change);
        $matches = $saved === $change['after'];
        if (!$matches && is_string($saved) && is_string($change['after'])) {
            $matches = html_entity_decode($saved, ENT_QUOTES | ENT_HTML5, 'UTF-8') === html_entity_decode($change['after'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
        if (is_wp_error($result) || !$matches) { $report['skipped']++; }
        else { $report['applied']++; }
    }
    return $report;
}

function fex_english_handle(): void {
    if (!current_user_can('manage_options')) { wp_die('You do not have permission.', '', ['response' => 403]); }
    check_admin_referer('fex_apply_english');
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || !fex_english_ready()) { wp_die('Activate Carbon Fields and open the English content preview first.'); }
    $plan = fex_english_plan();
    $fingerprint = isset($_POST['fingerprint']) && is_string($_POST['fingerprint']) ? wp_unslash($_POST['fingerprint']) : '';
    if (!hash_equals(fex_english_fingerprint($plan['changes']), $fingerprint)) {
        $report = ['message' => 'Content has changed since the preview. Review the refreshed list and apply again.'];
    } else {
        $result = fex_english_apply($plan['changes']);
        $report = ['message' => sprintf('%d entries translated. %d entries could not be changed; review the remaining list.', $result['applied'], $result['skipped'])];
    }
    set_transient('fex_english_report_' . get_current_user_id(), $report, 120);
    wp_safe_redirect(admin_url('tools.php?page=fex-english'));
    exit;
}

function fex_english_preview_text($value): string {
    if (is_array($value)) { return implode("\n", array_column($value, 'caption')); }
    return wp_strip_all_tags((string) $value);
}

function fex_english_page(): void {
    if (!current_user_can('manage_options')) { return; }
    echo '<div class="wrap"><h1>English content</h1><p>This preview finds the original German portfolio copy saved in WordPress. Only exact matches to that supplied copy can be translated.</p><p>Your own text, project names, files, links and order are preserved. Review all custom content separately, including image alt text and text inside images, videos or PDFs. This tool does not change your WordPress admin language.</p>';
    $report = get_transient('fex_english_report_' . get_current_user_id());
    if (is_array($report)) {
        echo '<div class="notice notice-info inline"><p>' . esc_html($report['message']) . '</p></div>';
        delete_transient('fex_english_report_' . get_current_user_id());
    }
    if (!fex_english_ready()) { echo '<p><strong>Activate Carbon Fields first.</strong></p></div>'; return; }
    $plan = fex_english_plan();
    echo '<h2>Ready to translate (' . count($plan['changes']) . ')</h2>';
    if (!$plan['changes']) { echo '<p>No saved original German copy was found.</p>'; }
    else {
        echo '<table class="widefat striped"><thead><tr><th>Location</th><th>Current</th><th>English</th></tr></thead><tbody>';
        foreach ($plan['changes'] as $change) {
            echo '<tr><td><a href="' . esc_url($change['url']) . '">' . esc_html($change['label']) . '</a></td><td style="white-space:pre-line">' . esc_html(fex_english_preview_text($change['before'])) . '</td><td style="white-space:pre-line">' . esc_html(fex_english_preview_text($change['after'])) . '</td></tr>';
        }
        echo '</tbody></table><form method="post" action="' . esc_url(admin_url('admin-post.php')) . '"><input type="hidden" name="action" value="fex_apply_english"><input type="hidden" name="fingerprint" value="' . esc_attr(fex_english_fingerprint($plan['changes'])) . '">';
        wp_nonce_field('fex_apply_english');
        submit_button('Apply English translations');
        echo '</form>';
    }
    echo '<h2>Custom content to review (' . count($plan['review']) . ')</h2><p>These language hints may miss some text or include names you want to keep. Review every project and the portfolio settings once after translating.</p><ul>';
    foreach ($plan['review'] as $review) {
        echo '<li><a href="' . esc_url($review['url']) . '">' . esc_html($review['label']) . '</a> — ' . esc_html(wp_trim_words(fex_english_preview_text($review['before']), 24)) . '</li>';
    }
    echo '</ul><p><a href="' . esc_url(admin_url('admin.php?page=fex-settings')) . '">Open portfolio settings</a> · <a href="' . esc_url(admin_url('edit.php?post_type=fex_work')) . '">Review projects</a></p></div>';
}
