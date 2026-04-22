<?php

/**
 * ============================================================
 * CategoriasController — CRUD completo de Categorías
 * ============================================================
 * Hereda de Controller: view(), model(), redirect(), flash()
 *
 * 🎓 LECCIÓN CLAVE: Este Controller es detectado
 * AUTOMÁTICAMENTE por el Router (App.php) cuando
 * el usuario accede a /categorias.
 * No fue necesario modificar App.php — así funciona
 * el patrón Front Controller.
 *
 * URLs manejadas:
 *   GET  /categorias              → index()
 *   GET  /categorias/crear        → crear()
 *   POST /categorias/crear        → crear()
 *   GET  /categorias/editar/2     → editar(2)
 *   POST /categorias/editar/2     → editar(2)
 *   GET  /categorias/eliminar/2   → eliminar(2)
 * ============================================================
 */

class CategoriasController extends Controller
{
    private object $categoriaModel;

    // ─────────────────────────────────────────────────────────
    /**
     * Constructor
     * Llama al padre para iniciar sesión (flash messages)
     * y carga el único modelo que este controller necesita.
     */
    public function __construct()
    {
        parent::__construct();
        $this->categoriaModel = $this->model('Categoria');
    }

    // ─────────────────────────────────────────────────────────
    /**
     * INDEX — Lista todas las categorías
     * ────────────────────────────────────
     * URL   : GET /categorias
     * Vista : app/views/categorias/index.php
     *
     * Muestra tabla con todas las categorías,
     * su color y la cantidad de contactos asociados.
     */
    public function index(): void
    {
        $categorias = $this->categoriaModel->getAll();

        $this->view('categorias/index', [
            'titulo'     => 'Categorías',
            'categorias' => $categorias
        ]);
    }

    // ─────────────────────────────────────────────────────────
    /**
     * CREAR — Formulario y procesamiento
     * ────────────────────────────────────
     * GET : /categorias/crear → muestra formulario vacío
     * POST: /categorias/crear → valida y guarda
     *
     * Validaciones:
     *   - nombre: obligatorio, mínimo 2 caracteres
     *   - color: obligatorio, formato HEX (#rrggbb)
     */
    public function crear(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // ── Recoger y sanitizar ─────────────────────────
            $datos = [
                'nombre' => trim($_POST['nombre'] ?? ''),
                'color'  => trim($_POST['color']  ?? '#6c757d')
            ];

            // ── Validar ─────────────────────────────────────
            $error = $this->validar($datos);

            if ($error) {
                $this->view('categorias/crear', [
                    'titulo' => 'Nueva Categoría',
                    'error'  => $error,
                    'datos'  => $datos
                ]);
                return;
            }

            // ── Guardar ─────────────────────────────────────
            $this->categoriaModel->crear($datos);

            $this->flash(
                'success',
                "Categoría <strong>{$datos['nombre']}</strong>
                 creada correctamente."
            );
            $this->redirect('categorias');
        }

        // GET → formulario vacío
        $this->view('categorias/crear', [
            'titulo' => 'Nueva Categoría'
        ]);
    }

    // ─────────────────────────────────────────────────────────
    /**
     * EDITAR — Formulario y procesamiento
     * ─────────────────────────────────────
     * GET : /categorias/editar/2 → formulario con datos
     * POST: /categorias/editar/2 → valida y actualiza
     *
     * @param int $id ID de la categoría a editar
     */
    public function editar(int $id): void
    {
        // Verificar que existe
        $categoria = $this->categoriaModel->getById($id);

        if (!$categoria) {
            $this->flash('warning', 'La categoría no existe o fue eliminada.');
            $this->redirect('categorias');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // ── Recoger y sanitizar ─────────────────────────
            $datos = [
                'nombre' => trim($_POST['nombre'] ?? ''),
                'color'  => trim($_POST['color']  ?? '#6c757d')
            ];

            // ── Validar ─────────────────────────────────────
            $error = $this->validar($datos);

            if ($error) {
                $this->view('categorias/editar', [
                    'titulo'    => 'Editar Categoría',
                    'error'     => $error,
                    'categoria' => array_merge($categoria, $datos)
                ]);
                return;
            }

            // ── Actualizar ──────────────────────────────────
            $this->categoriaModel->actualizar($id, $datos);

            $this->flash(
                'info',
                "Categoría <strong>{$datos['nombre']}</strong>
                 actualizada correctamente."
            );
            $this->redirect('categorias');
        }

        // GET → formulario con datos actuales
        $this->view('categorias/editar', [
            'titulo'    => 'Editar Categoría',
            'categoria' => $categoria
        ]);
    }

    // ─────────────────────────────────────────────────────────
    /**
     * ELIMINAR — Elimina una categoría
     * ──────────────────────────────────
     * URL: GET /categorias/eliminar/2
     *
     * PROTECCIÓN: Si la categoría tiene contactos
     * asociados NO se elimina y se muestra un warning.
     * Esto protege la integridad de los datos.
     *
     * @param int $id ID de la categoría a eliminar
     */
    public function eliminar(int $id): void
    {
        $categoria = $this->categoriaModel->getById($id);

        if (!$categoria) {
            $this->flash('warning', 'La categoría no existe o ya fue eliminada.');
            $this->redirect('categorias');
        }

        // ── Protección de integridad ────────────────────────
        // No eliminar si tiene contactos asociados
        if ($this->categoriaModel->tieneContactos($id)) {
            $this->flash(
                'warning',
                "No se puede eliminar <strong>{$categoria['nombre']}</strong>
                 porque tiene contactos asociados.
                 Reasigna o elimina esos contactos primero."
            );
            $this->redirect('categorias');
        }

        // ── Eliminar ────────────────────────────────────────
        $this->categoriaModel->eliminar($id);

        $this->flash(
            'danger',
            "Categoría <strong>{$categoria['nombre']}</strong>
             eliminada correctamente."
        );
        $this->redirect('categorias');
    }

    // ─────────────────────────────────────────────────────────
    /**
     * VALIDAR — Método privado de validación
     * ───────────────────────────────────────
     * Centraliza las validaciones comunes de crear y editar.
     * Retorna string con el error o null si todo está bien.
     *
     * 🎓 BUENA PRÁCTICA: Extraer validaciones a un método
     * privado evita duplicar código entre crear() y editar().
     *
     * @param  array       $datos Datos del formulario
     * @return string|null Error encontrado o null
     */
    private function validar(array $datos): ?string
    {
        if (empty($datos['nombre'])) {
            return 'El nombre de la categoría es obligatorio.';
        }

        if (strlen($datos['nombre']) < 2) {
            return 'El nombre debe tener al menos 2 caracteres.';
        }

        if (empty($datos['color']) ||
            !preg_match('/^#[0-9A-Fa-f]{6}$/', $datos['color'])) {
            return 'El color debe ser un valor HEX válido (ej: #28a745).';
        }

        return null; // Sin errores ✅
    }
    //22/04/26
    // ─────────────────────────────────────────────────────────
    /**
     * BUSCAR — Endpoint AJAX para autocompletado
     * URL: /categorias/buscadorcat?q=familia
     *
     * Retorna JSON — no carga ninguna vista
     */
    public function buscadorcat(): void
    {
        // Solo acepta peticiones AJAX
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) ||
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
            http_response_code(403);
            exit();
        }

        $termino = trim($_GET['q'] ?? '');

        // Mínimo 2 caracteres para buscar
        if (strlen($termino) < 2) {
            header('Content-Type: application/json');
            echo json_encode([]);
            exit();
        }

        $resultados = $this->categoriaModel->buscar($termino);

        header('Content-Type: application/json');
        echo json_encode($resultados);
        exit();
    }    
}