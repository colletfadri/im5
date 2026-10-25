<?php
$cfg = fex_settings(); $feedback = fex_contact_feedback(); $footer_nav = get_menu_by_slug('footer');
$number = (int) ($args['number'] ?? 3);
?>
<section class="contact" id="kontakt" aria-labelledby="contact-title">
<div class="contact-top"><p class="eyebrow red"><?php echo esc_html(sprintf('%02d / CONTACT', $number)); ?></p><p><?php echo nl2br(esc_html($cfg['fex_contact_identity'])); ?></p></div>
<h2 id="contact-title"><?php echo esc_html($cfg['fex_contact_title']); ?><span class="contact-arrow" aria-hidden="true">↗</span></h2>
<div class="contact-grid"><div class="contact-description"><p><?php echo esc_html($cfg['fex_contact_text']); ?></p><a class="email-link" href="mailto:<?php echo esc_attr($cfg['fex_email']); ?>"><?php echo esc_html($cfg['fex_email']); ?></a></div>
<form class="contact-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
<?php if ($feedback): ?><p class="form-feedback <?php echo $feedback['success'] ? 'success' : 'error'; ?>" role="<?php echo $feedback['success'] ? 'status' : 'alert'; ?>"><?php echo esc_html($feedback['message']); ?></p><?php endif; ?>
<input type="hidden" name="action" value="fex_contact"><?php wp_nonce_field('fex_contact', 'fex_nonce'); ?>
<div class="form-pair"><label>Name<input name="name" type="text" autocomplete="name" maxlength="150" required></label><label>Email<input name="email" type="email" autocomplete="email" maxlength="254" required></label></div>
<label>Message<textarea name="message" rows="4" maxlength="10000" required></textarea></label>
<div class="honeypot" aria-hidden="true"><label>Website<input name="website" type="text" tabindex="-1" autocomplete="off"></label></div>
<label class="consent"><input name="consent" type="checkbox" value="1" required><span>I consent to my details being used to reply to my enquiry by email.<?php if ($cfg['fex_privacy_url']): ?> <a href="<?php echo esc_url($cfg['fex_privacy_url']); ?>">Privacy</a><?php endif; ?></span></label>
<button class="button" type="submit">Send message <span aria-hidden="true">↗</span></button>
</form></div>
<footer data-layout="footer">
<span class="footer-mark">
<a class="logo footer-home" href="<?php echo esc_url(home_url('/')); ?>"><?php echo esc_html($cfg['fex_brand']); ?><span>.</span></a>
<button class="logo footer-fish" type="button" data-footer-fish hidden title="Make the fish jump" aria-label="<?php echo esc_attr($cfg['fex_brand'] . ': Make the fish jump'); ?>">
<span class="footer-name-mask" aria-hidden="true"><span class="footer-brand-name"><?php echo esc_html($cfg['fex_brand']); ?><span class="footer-brand-dot">.</span></span></span>
<span class="footer-fish-stage" aria-hidden="true"><span class="footer-fish-jump"><img data-src="<?php echo esc_url(get_template_directory_uri() . '/frontend/assets/images/footer-fish.png'); ?>" width="1536" height="1024" alt="" decoding="async" draggable="false"></span></span>
</button>
</span>
<span>© <?php echo esc_html(wp_date('Y') . ' · ' . $cfg['fex_footer_text']); ?></span><nav aria-label="Footer"><?php foreach ($footer_nav as $item): ?><a href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($item['title']); ?></a><?php endforeach; ?><?php if (!$footer_nav && $cfg['fex_privacy_url']): ?><a href="<?php echo esc_url($cfg['fex_privacy_url']); ?>">Privacy</a><?php endif; ?><a href="<?php echo esc_url(home_url('/#start')); ?>">Back to top ↑</a></nav></footer>
</section>
