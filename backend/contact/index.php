<?php
defined('ABSPATH') || exit;
add_action('admin_post_nopriv_fex_contact', 'fex_handle_contact');
add_action('admin_post_fex_contact', 'fex_handle_contact');
function fex_contact_redirect(string $status): void {
    wp_safe_redirect(add_query_arg('kontakt', $status, home_url('/')) . '#kontakt', 303);
    exit;
}
/** Validated contact mail; never use the visitor's address as the From address. */
function fex_handle_contact(): void {
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') { fex_contact_redirect('invalid'); }
    $nonce = isset($_POST['fex_nonce']) && is_string($_POST['fex_nonce']) ? sanitize_text_field(wp_unslash($_POST['fex_nonce'])) : '';
    if (!wp_verify_nonce($nonce, 'fex_contact')) { fex_contact_redirect('expired'); }
    $values = [];
    foreach (['name', 'email', 'message', 'website'] as $key) {
        if (isset($_POST[$key]) && !is_string($_POST[$key])) { fex_contact_redirect('invalid'); }
        $values[$key] = isset($_POST[$key]) ? wp_unslash($_POST[$key]) : '';
    }
    // Honeypot: silently accept automated junk without sending an email.
    if ($values['website'] !== '') { fex_contact_redirect('sent'); }
    $name = sanitize_text_field($values['name']);
    $email = sanitize_email($values['email']);
    $message = sanitize_textarea_field($values['message']);
    $consent = isset($_POST['consent']) && $_POST['consent'] === '1';
    if (!$name || strlen($name) > 150 || !is_email($email) || strlen($email) > 254 || !$message || strlen($message) > 10000 || !$consent) { fex_contact_redirect('invalid'); }
    $ip = (string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
    $key = 'fex_mail_' . substr(hash_hmac('sha256', $ip, wp_salt('nonce')), 0, 32);
    $attempts = (int) get_transient($key);
    if ($attempts >= 5) { fex_contact_redirect('limit'); }
    set_transient($key, $attempts + 1, 15 * MINUTE_IN_SECONDS);
    $settings = fex_settings();
    $subject = 'Portfolio enquiry from ' . $name;
    $body = "Name: " . $name . "\nEmail: " . $email . "\n\n" . $message . "\n\nSent using the contact form at " . home_url('/');
    $sent = wp_mail($settings['fex_email'], $subject, $body, ['Content-Type: text/plain; charset=UTF-8', 'Reply-To: ' . $email]);
    fex_contact_redirect($sent ? 'sent' : 'failed');
}
function fex_contact_feedback(): array|false {
    $code = isset($_GET['kontakt']) && is_string($_GET['kontakt']) ? sanitize_key(wp_unslash($_GET['kontakt'])) : '';
    $messages = [
        'sent' => 'Thank you. Your message has been submitted.',
        'invalid' => 'Please enter your name, a valid email, a message and your consent.',
        'expired' => 'This form has expired. Please reload the page and try again.',
        'limit' => 'Too many attempts. Please try again later or email me directly.',
        'failed' => 'Your message could not be sent. Please email me directly.',
    ];
    return isset($messages[$code]) ? ['success' => $code === 'sent', 'message' => $messages[$code]] : false;
}
