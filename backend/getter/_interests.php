<?php
defined('ABSPATH') || exit;

/** Shared defaults keep the settings form and the frontend in sync. */
function fex_interest_defaults(): array {
    return [
        'worlds' => [
            'label' => '1. Worlds and ideas',
            'title' => "Building worlds.\nDeveloping ideas.",
            'text' => 'I enjoy creating worlds of my own. An initial idea grows into characters, places and stories that I develop through 3D and animation.',
        ],
        'film' => [
            'label' => '2. Film and photography',
            'title' => "Film and\nPhotography.",
            'text' => 'Through film and photography, I capture what catches my attention. I explore how light, perspective and editing turn individual shots into a story.',
        ],
        'horror' => [
            'label' => '3. Horror',
            'title' => 'Horror.',
            'text' => 'I enjoy horror, especially Resident Evil. I am interested in how a space, a sound or a single light can create tension.',
        ],
    ];
}

function fex_interest_data(array $cfg): array {
    if ($cfg['fex_about_enabled'] !== 'on' || $cfg['fex_interests_enabled'] !== 'on') { return []; }
    $items = [];
    foreach (fex_interest_defaults() as $slug => $default) {
        $prefix = 'fex_interest_' . $slug . '_';
        $title = trim((string) $cfg[$prefix . 'title']);
        if ($title === '') { continue; }
        $image = get_media_by_id((int) $cfg[$prefix . 'image'], 'full');
        if ($image && $image['type'] !== 'image') { $image = false; }
        $items[] = ['slug' => $slug, 'title' => $title, 'text' => (string) $cfg[$prefix . 'text'], 'image' => $image];
    }
    return $items;
}
