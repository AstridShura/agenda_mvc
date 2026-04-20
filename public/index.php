<?php

/**
 * FRONT CONTROLLER
 * Punto de entrada único de toda la aplicación.
 */

// Ruta raíz del proyecto
define('ROOT_PATH', dirname(__DIR__));

// Configuración
require_once ROOT_PATH . '/config/config.php';

// ── Cargar el CORE completo ────────────────────
require_once ROOT_PATH . '/core/Database.php';
require_once ROOT_PATH . '/core/Model.php';
require_once ROOT_PATH . '/core/Controller.php';
require_once ROOT_PATH . '/core/App.php';

// Iniciar la aplicación (Router)
$app = new App();
?>