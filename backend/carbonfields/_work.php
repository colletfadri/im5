<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;
defined('ABSPATH') || exit;
Container::make('post_meta', 'Project content')->where('post_type', '=', 'fex_work')->add_fields([
    Field::make('text', 'fex_subtitle', 'Short line below the title')->set_help_text('For example: Blender / 3D animation'),
    Field::make('text', 'fex_position', 'Position')->set_attribute('type', 'number')->set_attribute('min', '0')->set_default_value('10')->set_help_text('Lower numbers appear first. You can change these at any time, e.g. 10, 20, 30.'),
    Field::make('image', 'fex_preview', 'Preview image')->set_help_text('You can also use the WordPress featured image.'),
    Field::make('file', 'fex_media', 'Main media (image, GIF or video)')->set_help_text('Videos: MP4 or WebM. Leave empty to use the preview image.'),
    Field::make('file', 'fex_preview_video', 'Muted video preview on hover (optional)')->set_type('video'),
    Field::make('file', 'fex_model', 'Interactive 3D model (optional, .glb)')->set_type('model/gltf-binary')->set_help_text('Appears above the main media in the project view. Export from Blender as glTF Binary (.glb) and add a poster image.'),
    Field::make('select', 'fex_model_rotate', 'Slowly rotate the 3D model')->set_options(['on' => 'Rotate automatically', 'off' => 'Rotate manually'])->set_default_value('on'),
    Field::make('select', 'fex_model_animate', 'Play the embedded model animation')->set_options(['off' => 'Off', 'on' => 'Play'])->set_default_value('off'),
    Field::make('select', 'fex_fit', 'Preview image fit')->set_options(['cover' => 'Fill the frame', 'contain' => 'Show the whole image'])->set_default_value('cover'),
    Field::make('rich_text', 'fex_description', 'Description / project details'),
    Field::make('complex', 'fex_gallery', 'More images, GIFs, videos and PDFs')
        ->set_layout('tabbed-vertical')
        ->add_fields([
            Field::make('text', 'caption', 'Caption'),
            Field::make('file', 'file', 'Media file')->set_required(),
        ])->set_header_template('<%- caption || "Media file" %>'),
]);
