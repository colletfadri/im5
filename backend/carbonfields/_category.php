<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;
defined('ABSPATH') || exit;
Container::make('term_meta', 'Project row')->where('term_taxonomy', '=', 'fex_category')->add_fields([
    Field::make('text', 'fex_position', 'Row order')->set_attribute('type', 'number')->set_attribute('min', '0')->set_default_value('10')->set_help_text('Lower numbers appear first. Each category automatically becomes a horizontal row.'),
    Field::make('text', 'fex_row_note', 'Short note beside the title')->set_help_text('Optional, e.g. 3D scenes, renders and animation.'),
]);
