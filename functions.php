<?php
/** Bootstrap. Backend-Funktionen bleiben gemäss IM5-Struktur im Ordner backend. */
defined('ABSPATH') || exit;
require_once __DIR__ . '/backend/init/index.php';
require_once __DIR__ . '/backend/helpers/index.php';
require_once __DIR__ . '/backend/media/index.php';
require_once __DIR__ . '/backend/nav/index.php';
require_once __DIR__ . '/backend/cpt/index.php';
require_once __DIR__ . '/backend/tax/index.php';
require_once __DIR__ . '/backend/carbonfields/index.php';
require_once __DIR__ . '/backend/getter/index.php';
require_once __DIR__ . '/backend/admin/index.php';
require_once __DIR__ . '/backend/contact/index.php';
require_once __DIR__ . '/backend/projects/index.php';
require_once __DIR__ . '/backend/english/index.php';
