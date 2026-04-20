<?php

/**
 * CONFIGURACIÓN GLOBAL
 * Base de datos, URL base, constantes de la app.
 */

// ── Base de datos ──────────────────────────────
define('DB_SERVER',   'AIORIAL');
define('DB_USER',     'sa');
define('DB_PASS',     'aioria');
define('DB_NAME',     'agenda_db');

// ── URL base ───────────────────────────────────
define('BASE_URL', 'http://agenda.local');

// ── Entorno ────────────────────────────────────
define('APP_ENV', 'development'); // development | production

// Mostrar errores en desarrollo
if (APP_ENV === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}
?>