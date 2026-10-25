<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;
defined('ABSPATH') || exit;
$interest_fields = [
    Field::make('select', 'fex_interests_enabled', 'Show interests before the personal introduction')->set_options(['on' => 'Show', 'off' => 'Hide'])->set_default_value('on')->set_help_text('Part of About me. The three sections appear after the projects.'),
    Field::make('select', 'fex_interests_motion', 'Scroll animation')->set_options(['on' => 'Follow scrolling in both directions', 'off' => 'Show everything immediately'])->set_default_value('on'),
];
foreach (fex_interest_defaults() as $slug => $interest) {
    $prefix = 'fex_interest_' . $slug . '_';
    $interest_fields[] = Field::make('textarea', $prefix . 'title', $interest['label'] . ' / Heading')->set_rows(2)->set_default_value($interest['title'])->set_help_text('Line breaks are preserved. Leave the heading empty to hide this section.');
    $interest_fields[] = Field::make('textarea', $prefix . 'text', $interest['label'] . ' / Text')->set_rows(3)->set_default_value($interest['text'])->set_help_text('You can edit this text at any time or leave it empty.');
    $interest_fields[] = Field::make('image', $prefix . 'image', $interest['label'] . ' / Image')->set_help_text('Choose your own image. Leave this empty for a text-only section.');
}
Container::make('theme_options', 'Portfolio settings')
    ->set_page_menu_title('Portfolio settings')
    ->set_page_file('fex-settings')
    ->add_tab('Hero', [
        Field::make('text', 'fex_name', 'Name')->set_default_value('Fadri Collet'),
        Field::make('text', 'fex_brand', 'Logo text')->set_default_value('FEX'),
        Field::make('text', 'fex_eyebrow', 'Small line above the title')->set_default_value('FADRI COLLET / PORTFOLIO'),
        Field::make('textarea', 'fex_title', 'Large title / white line')->set_rows(2)->set_default_value('MY'),
        Field::make('textarea', 'fex_title_accent', 'Large title / red line')->set_rows(2)->set_default_value('WORK.'),
        Field::make('textarea', 'fex_intro', 'Introduction')->set_rows(3)->set_default_value('I am Fadri. I work in 3D, animation, film and photography. Here is a selection of my projects.'),
        Field::make('image', 'fex_background', 'Background image / GIF')->set_help_text('Choose your own image or GIF. Without an image, the hero stays dark. For a background video, this image is its poster.'),
        Field::make('file', 'fex_background_video', 'Background video (optional)')->set_type('video')->set_help_text('MP4 or WebM. Plays silently on a loop. Add a static background image for loading and reduced-motion mode.'),
        Field::make('text', 'fex_focus', 'Areas of focus')->set_default_value('3D / ANIMATION / FILM / PHOTOGRAPHY'),
        Field::make('text', 'fex_location', 'Location')->set_default_value('SWITZERLAND'),
    ])
    ->add_tab('Projects', [
        Field::make('text', 'fex_work_label', 'Small label above the project rows')->set_default_value('SELECTED WORK')->set_help_text('For example PROJECTS or SELECTED WORK. Leave empty to hide it. Edit the large row headings under Projects > Categories.'),
    ])
    ->add_tab('About me', [
        Field::make('select', 'fex_about_enabled', 'Show section')->set_options(['on' => 'Show', 'off' => 'Hide'])->set_default_value('on'),
        Field::make('text', 'fex_about_title', 'Heading')->set_default_value('ABOUT ME.'),
        Field::make('textarea', 'fex_about_text', 'Personal introduction')->set_rows(5)->set_default_value('I am Fadri. I work in 3D, animation, film and photography.'),
        Field::make('text', 'fex_about_tools', 'Tools / areas of focus (optional)')->set_help_text('Separate entries with a comma, e.g. Blender, After Effects, Premiere Pro.'),
        Field::make('image', 'fex_about_image', 'Image / portrait / 3D poster (optional)'),
        Field::make('file', 'fex_about_model', 'Interactive 3D model (optional, .glb)')->set_type('model/gltf-binary')->set_help_text('Export from Blender as glTF Binary (.glb) with embedded textures. The model loads after clicking View in 3D.'),
        Field::make('select', 'fex_about_model_rotate', 'Slowly rotate the 3D model')->set_options(['on' => 'Rotate automatically', 'off' => 'Rotate manually'])->set_default_value('on'),
        Field::make('select', 'fex_about_model_animate', 'Play the embedded model animation')->set_options(['off' => 'Off', 'on' => 'Play'])->set_default_value('off'),
    ])
    ->add_tab('Interests', $interest_fields)
    ->add_tab('Interaction', [
        Field::make('select', 'fex_hover_preview', 'Large preview beside the cursor')->set_options(['on' => 'On', 'off' => 'Off'])->set_default_value('on')->set_help_text('Shows a larger preview that follows the mouse. Click a project to open its details.'),
        Field::make('select', 'fex_hero_parallax', 'Background follows the mouse')->set_options(['on' => 'On', 'off' => 'Off'])->set_default_value('on')->set_help_text('Gentle background movement in the hero.'),
        Field::make('select', 'fex_hero_glow', 'White spotlight in the hero')->set_options(['on' => 'On', 'off' => 'Off'])->set_default_value('on')->set_help_text('Dims the background and reveals details around the cursor. Touch devices and reduced-motion mode use the normal background.'),
        Field::make('select', 'fex_hover_arrows', 'Move arrows on hover')->set_options(['on' => 'On', 'off' => 'Off'])->set_default_value('on'),
    ])
    ->add_tab('Contact', [
        Field::make('text', 'fex_email', 'Displayed email and form recipient')->set_attribute('type', 'email')->set_default_value('fadri.collet@gmail.com'),
        Field::make('text', 'fex_contact_title', 'Contact heading')->set_default_value('CONTACT.'),
        Field::make('textarea', 'fex_contact_text', 'Short contact text')->set_rows(2)->set_default_value('Have a question about a project or want to work together?'),
        Field::make('textarea', 'fex_contact_identity', 'Contact identity line')->set_rows(2)->set_default_value("Fadri Collet\n3D / Animation / Film / Photography"),
    ])
    ->add_tab('Footer', [
        Field::make('text', 'fex_footer_text', 'Footer text')->set_default_value('Fadri Collet'),
        Field::make('text', 'fex_privacy_url', 'Privacy page (optional)')->set_attribute('type', 'url')->set_help_text('Alternatively, select a page under Settings > Privacy.'),
    ]);
