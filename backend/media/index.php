<?php
defined('ABSPATH') || exit;

/** Accept only glTF 2 binary files, even if the host reports application/octet-stream. */
function fex_valid_glb(string $path): bool {
    if (!is_file($path) || !is_readable($path)) { return false; }
    $stream = fopen($path, 'rb');
    if (!$stream) { return false; }
    try {
        $header = fread($stream, 20);
        if (strlen($header) !== 20 || substr($header, 0, 4) !== 'glTF' || substr($header, 16, 4) !== 'JSON') { return false; }
        $data = unpack('Vversion/Vlength/Vjson_length', substr($header, 4, 12));
        $length = (int) $data['json_length'];
        if ($data['version'] !== 2 || $data['length'] !== filesize($path) || $length < 4 || $length % 4 !== 0 || $length > 16 * 1024 * 1024 || 20 + $length > $data['length']) { return false; }
        $json = json_decode(fread($stream, $length), true);
        return is_array($json) && ($json['asset']['version'] ?? '') === '2.0';
    } finally {
        fclose($stream);
    }
}
add_filter('upload_mimes', function (array $mimes): array {
    $mimes['glb'] = 'model/gltf-binary';
    return $mimes;
});
add_filter('wp_check_filetype_and_ext', function (array $data, string $file, string $filename, ?array $mimes): array {
    if (strtolower(pathinfo($filename, PATHINFO_EXTENSION)) !== 'glb') { return $data; }
    $allowed = wp_check_filetype($filename, $mimes);
    if (($allowed['type'] ?? '') !== 'model/gltf-binary') { return $data; }
    $valid = fex_valid_glb($file);
    $data['ext'] = $valid ? 'glb' : false;
    $data['type'] = $valid ? 'model/gltf-binary' : false;
    $data['proper_filename'] = false;
    return $data;
}, 10, 4);
add_filter('wp_handle_upload_prefilter', function (array $file): array {
    if (strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION)) === 'glb' && empty($file['error']) && !fex_valid_glb($file['tmp_name'] ?? '')) {
        $file['error'] = 'This file is not a valid glTF 2.0 model. Export it from Blender as glTF Binary (.glb).';
    }
    return $file;
});
