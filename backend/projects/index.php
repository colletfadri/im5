<?php
defined('ABSPATH') || exit;

/** Only publicly visible, unprotected projects may be returned to the modal. */
function fex_project_access($request): bool|WP_Error {
    $post = get_post((int) $request['id']);
    if (!$post || $post->post_type !== 'fex_work' || $post->post_status !== 'publish' || $post->post_password !== '') {
        return new WP_Error('fex_project_unavailable', 'This project is unavailable.', ['status' => 404]);
    }
    return true;
}
function fex_project_response($request): WP_REST_Response|WP_Error {
    $allowed = fex_project_access($request);
    if (is_wp_error($allowed)) { return $allowed; }
    ob_start();
    get_template_part('frontend/views/pages/work', null, ['work' => fex_work_data((int) $request['id'])]);
    $html = ob_get_clean();
    return new WP_REST_Response(['html' => $html], 200);
}
add_action('rest_api_init', function () {
    register_rest_route('fex/v1', '/work/(?P<id>\d+)', [
        'methods' => 'GET',
        'permission_callback' => 'fex_project_access',
        'callback' => 'fex_project_response',
        'args' => ['id' => ['required' => true, 'type' => 'integer', 'minimum' => 1, 'sanitize_callback' => 'absint']],
    ]);
});
