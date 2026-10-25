<?php
$cfg = fex_settings();
$nav = get_menu_by_slug('main');
if (!$nav) { $nav = [
    ['title' => 'Projects', 'url' => home_url('/#arbeiten'), 'target' => ''],
    ['title' => $cfg['fex_about_enabled'] === 'on' ? 'About me' : 'Gallery', 'url' => home_url($cfg['fex_about_enabled'] === 'on' ? '/#ueber-mich' : '/#galerie'), 'target' => ''],
    ['title' => 'Contact ↗', 'url' => home_url('/#kontakt'), 'target' => ''],
]; }
?>
<header class="site-header" data-layout="header">
<a class="logo" href="<?php echo esc_url(home_url('/')); ?>" aria-label="Back to home"><?php echo esc_html($cfg['fex_brand']); ?><span>.</span></a>
<nav aria-label="Main navigation">
<?php foreach ($nav as $item): ?>
<a href="<?php echo esc_url($item['url']); ?>"<?php if ($item['target'] === '_blank') { echo ' target="_blank" rel="noopener"'; } ?>><?php echo esc_html($item['title']); ?></a>
<?php endforeach; ?>
</nav>
</header>
