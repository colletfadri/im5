<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;
defined('ABSPATH') || exit;
Container::make('post_meta', 'Page content')->where('post_type', '=', 'page')->add_fields([
    Field::make('complex', 'modules', 'Content modules')->set_layout('tabbed-vertical')
        ->add_fields('text', 'Text', [Field::make('rich_text', 'text', 'Content')])
        ->add_fields('example', 'Image with title', [Field::make('text', 'title', 'Title'), Field::make('image', 'image', 'Image')]),
]);
function render_modules(): void {
    if (!function_exists('carbon_get_the_post_meta')) { return; }
    foreach ((array) carbon_get_the_post_meta('modules') as $module) {
        $type = $module['_type'] ?? '';
        if (!in_array($type, ['text', 'example'], true)) { continue; }
        if ($type === 'example') { $module['image'] = get_media_by_id((int) ($module['image'] ?? 0), 'full'); }
        get_template_part('frontend/views/modules/' . $type, null, ['data' => $module]);
    }
}
