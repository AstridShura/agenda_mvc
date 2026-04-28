<?php

/**
 * FRONT CONTROLLER
 * Punto de entrada único de toda la aplicación.
 */

define('ROOT_PATH', dirname(__DIR__));

// ── 1. Configuración global ────────────────────────────────
require_once ROOT_PATH . '/config/config.php';

// ── 2. Composer autoload ───────────────────────────────────
// DEBE ir antes de cualquier clase que use namespaces
// PhpSpreadsheet y TCPDF lo requieren
if (file_exists(ROOT_PATH . '/vendor/autoload.php')) {
    require_once ROOT_PATH . '/vendor/autoload.php';
}

// ── 3. Core del framework ──────────────────────────────────
require_once ROOT_PATH . '/core/Database.php';
require_once ROOT_PATH . '/core/Model.php';
require_once ROOT_PATH . '/core/Controller.php';
require_once ROOT_PATH . '/core/Paginador.php';

// Exportador solo si Composer está instalado
if (file_exists(ROOT_PATH . '/vendor/autoload.php')) {
    require_once ROOT_PATH . '/core/Exportador.php';
}

// ── 4. Router — siempre al final ──────────────────────────
require_once ROOT_PATH . '/core/App.php';

// ── 5. Iniciar aplicación ──────────────────────────────────
$app = new App();