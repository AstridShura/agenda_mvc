<?php

/**
 * ============================================================
 * CLASE BASE — Controller
 * ============================================================
 * Todos los controllers de la app heredan de esta clase.
 * Provee métodos comunes como cargar vistas y modelos.
 * ============================================================
 */

class Controller
{
    // ─────────────────────────────────────────────────────────
    /**
     * Carga y renderiza una Vista.
     *
     * Uso desde un Controller hijo:
     *   $this->view('contactos/index', ['contactos' => $data]);
     *
     * @param string $view   Ruta relativa a /app/views/
     * @param array  $data   Variables que estarán disponibles en la vista
     */


    protected function view(string $view, array $data = []): void
    {
        extract($data);

        $viewFile = ROOT_PATH . '/app/views/' . $view . '.php';

        if (!file_exists($viewFile)) {
            die("<h3>Vista no encontrada: <code>{$view}.php</code></h3>");
        }

        // 1. Captura el HTML de la vista en una variable
        ob_start();
        require_once $viewFile;
        $contenido = ob_get_clean();

        // 2. Inyecta $contenido dentro del layout
        $layoutFile = ROOT_PATH . '/app/views/layouts/main.php';
        require_once $layoutFile;
    }

    // ─────────────────────────────────────────────────────────
    /**
     * Carga e instancia un Modelo.
     *
     * Uso desde un Controller hijo:
     *   $this->model('Contacto');
     *   → carga /app/models/Contacto.php
     *   → retorna instancia de la clase Contacto
     *
     * @param  string $model  Nombre de la clase Model
     * @return object         Instancia del modelo
     */
    protected function model(string $model): object
    {
        $modelFile = ROOT_PATH . '/app/models/' . $model . '.php';

        if (file_exists($modelFile)) {
            require_once $modelFile;
            return new $model();
        } else {
            die("<h3>Modelo no encontrado: <code>{$model}.php</code></h3>");
        }
    }

    // ─────────────────────────────────────────────────────────
    /**
     * Redirecciona a otra URL de la app.
     *
     * Uso:
     *   $this->redirect('contactos/index');
     *
     * @param string $url  Ruta relativa desde BASE_URL
     */
    protected function redirect(string $url): void
    {
        header('Location: ' . BASE_URL . '/' . $url);
        exit();
    }
}
?>