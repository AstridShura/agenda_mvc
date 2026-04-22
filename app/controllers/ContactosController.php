<?php

/**
 * ============================================================
 * ContactosController
 * ============================================================
 * Maneja todas las peticiones HTTP relacionadas a Contactos.
 * Hereda de Controller: view(), model(), redirect(), flash()
 *
 * URLs manejadas:
 *   GET  /contactos              → index()
 *   GET  /contactos/ver/5        → ver(5)
 *   GET  /contactos/crear        → crear()
 *   POST /contactos/crear        → crear()
 *   GET  /contactos/editar/5     → editar(5)
 *   POST /contactos/editar/5     → editar(5)
 *   GET  /contactos/eliminar/5   → eliminar(5)
 *   GET  /contactos/buscadorajax → buscadorajax() ← AJAX
 * ============================================================
 */

class ContactosController extends Controller
{
    private object $contactoModel;
    private object $telefonoModel;
    private object $categoriaModel;

    // ─────────────────────────────────────────────────────────
    /**
     * Constructor
     * Se ejecuta automáticamente al instanciar el Controller.
     * - Llama al padre para iniciar la sesión (flash messages)
     * - Carga los 3 modelos que usará este controller
     */
    public function __construct()
    {
        parent::__construct();
        $this->contactoModel  = $this->model('Contacto');
        $this->telefonoModel  = $this->model('Telefono');
        $this->categoriaModel = $this->model('Categoria');
    }

    // ─────────────────────────────────────────────────────────
    /**
     * INDEX — Lista todos los contactos
     * ─────────────────────────────────
     * URL    : GET /contactos
     * Vista  : app/views/contactos/index.php
     *
     * Obtiene todos los contactos con su categoría y
     * cantidad de teléfonos, los pasa a la vista para
     * mostrarlos en una tabla con acciones CRUD.
     */
    public function index(): void
    {
        $contactos = $this->contactoModel->getAll();

        $this->view('contactos/index', [
            'titulo'    => 'Mi Agenda',
            'contactos' => $contactos
        ]);
    }

    // ─────────────────────────────────────────────────────────
    /**
     * VER — Detalle completo de un contacto
     * ───────────────────────────────────────
     * URL    : GET /contactos/ver/5
     * Vista  : app/views/contactos/ver.php
     * Param  : $id → ID del contacto en la BD
     *
     * Carga el contacto y todos sus teléfonos.
     * Si el ID no existe redirige con mensaje warning.
     */
    public function ver(int $id): void
    {
        // Busca el contacto por ID con JOIN a categorías
        $contacto  = $this->contactoModel->getById($id);

        // Si no existe → flash warning y vuelve al listado
        if (!$contacto) {
            $this->flash('warning', 'El contacto no existe o fue eliminado.');
            $this->redirect('contactos');
        }

        // Obtiene todos los teléfonos del contacto
        $telefonos = $this->telefonoModel->getPorContacto($id);

        $this->view('contactos/ver', [
            'titulo'    => $contacto['nombre'] . ' ' . $contacto['apellido'],
            'contacto'  => $contacto,
            'telefonos' => $telefonos
        ]);
    }

    // ─────────────────────────────────────────────────────────
    /**
     * CREAR — Formulario y procesamiento de nuevo contacto
     * ──────────────────────────────────────────────────────
     * URL GET : /contactos/crear  → muestra formulario vacío
     * URL POST: /contactos/crear  → procesa y guarda datos
     * Vista   : app/views/contactos/crear.php
     *
     * Flujo POST:
     *   1. Recoge y sanitiza datos del formulario
     *   2. Valida campos obligatorios
     *   3. Guarda el contacto → obtiene nuevo ID
     *   4. Guarda los teléfonos asociados al nuevo ID
     *   5. Flash success + redirect al listado
     */
    public function crear(): void
    {
        // Siempre necesitamos las categorías para el select
        $categorias = $this->categoriaModel->getAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // ── 1. Recoger y sanitizar ──────────────────────
            $datos = [
                'nombre'       => trim($_POST['nombre']       ?? ''),
                'apellido'     => trim($_POST['apellido']     ?? ''),
                'alias'        => trim($_POST['alias']        ?? ''),
                'email'        => trim($_POST['email']        ?? ''),
                'direccion'    => trim($_POST['direccion']    ?? ''),
                'id_categoria' => $_POST['id_categoria']      ?? null
            ];

            // ── 2. Validar campos obligatorios ──────────────
            if (empty($datos['nombre']) || empty($datos['apellido'])) {
                // Recarga el formulario con el error y los datos
                // que ya había escrito el usuario
                $this->view('contactos/crear', [
                    'titulo'     => 'Nuevo Contacto',
                    'categorias' => $categorias,
                    'error'      => 'Nombre y apellido son obligatorios.',
                    'datos'      => $datos
                ]);
                return;
            }

            // ── 3. Guardar contacto → obtener nuevo ID ──────
            $idContacto = $this->contactoModel->crear($datos);

            // ── 4. Guardar teléfonos ─────────────────────────
            // Los teléfonos vienen como arrays paralelos:
            // numeros[] = ['555-1001', '555-1002']
            // tipos[]   = ['Personal', 'Trabajo']
            $numeros = $_POST['numeros'] ?? [];
            $tipos   = $_POST['tipos']   ?? [];

            foreach ($numeros as $i => $numero) {
                $numero = trim($numero);
                if (!empty($numero)) {
                    $this->telefonoModel->agregar(
                        $idContacto,
                        $numero,
                        $tipos[$i] ?? 'Personal'
                    );
                }
            }

            // ── 5. Flash + redirect ─────────────────────────
            $this->flash(
                'success',
                "Contacto <strong>{$datos['nombre']} {$datos['apellido']}</strong>
                 creado correctamente."
            );
            $this->redirect('contactos');
        }

        // ── GET: mostrar formulario vacío ───────────────────
        $this->view('contactos/crear', [
            'titulo'     => 'Nuevo Contacto',
            'categorias' => $categorias
        ]);
    }

    // ─────────────────────────────────────────────────────────
    /**
     * EDITAR — Formulario y procesamiento de edición
     * ────────────────────────────────────────────────
     * URL GET : /contactos/editar/5  → muestra formulario
     * URL POST: /contactos/editar/5  → procesa y guarda
     * Vista   : app/views/contactos/editar.php
     * Param   : $id → ID del contacto a editar
     *
     * Flujo POST:
     *   1. Recoge y sanitiza datos del formulario
     *   2. Valida campos obligatorios
     *   3. Actualiza el contacto en BD
     *   4. Borra todos sus teléfonos y los reinserta
     *      (estrategia delete+insert es más simple que
     *       comparar cuáles cambiaron)
     *   5. Flash info + redirect al detalle
     */
    public function editar(int $id): void
    {
        // Verifica que el contacto existe
        $contacto   = $this->contactoModel->getById($id);

        if (!$contacto) {
            $this->flash('warning', 'El contacto no existe o fue eliminado.');
            $this->redirect('contactos');
        }

        $categorias = $this->categoriaModel->getAll();
        $telefonos  = $this->telefonoModel->getPorContacto($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // ── 1. Recoger y sanitizar ──────────────────────
            $datos = [
                'nombre'       => trim($_POST['nombre']       ?? ''),
                'apellido'     => trim($_POST['apellido']     ?? ''),
                'alias'        => trim($_POST['alias']        ?? ''),
                'email'        => trim($_POST['email']        ?? ''),
                'direccion'    => trim($_POST['direccion']    ?? ''),
                'id_categoria' => $_POST['id_categoria']      ?? null
            ];

            // ── 2. Validar ──────────────────────────────────
            if (empty($datos['nombre']) || empty($datos['apellido'])) {
                $this->view('contactos/editar', [
                    'titulo'     => 'Editar Contacto',
                    'categorias' => $categorias,
                    'contacto'   => $contacto,
                    'telefonos'  => $telefonos,
                    'error'      => 'Nombre y apellido son obligatorios.'
                ]);
                return;
            }

            // ── 3. Actualizar contacto ──────────────────────
            $this->contactoModel->actualizar($id, $datos);

            // ── 4. Teléfonos: borrar todos y reinsertar ─────
            $this->telefonoModel->eliminarPorContacto($id);

            $numeros = $_POST['numeros'] ?? [];
            $tipos   = $_POST['tipos']   ?? [];

            foreach ($numeros as $i => $numero) {
                $numero = trim($numero);
                if (!empty($numero)) {
                    $this->telefonoModel->agregar(
                        $id,
                        $numero,
                        $tipos[$i] ?? 'Personal'
                    );
                }
            }

            // ── 5. Flash + redirect al detalle ──────────────
            $this->flash(
                'info',
                "Contacto <strong>{$datos['nombre']} {$datos['apellido']}</strong>
                 actualizado correctamente."
            );
            $this->redirect('contactos/ver/' . $id);
        }

        // ── GET: mostrar formulario con datos actuales ──────
        $this->view('contactos/editar', [
            'titulo'     => 'Editar Contacto',
            'categorias' => $categorias,
            'contacto'   => $contacto,
            'telefonos'  => $telefonos
        ]);
    }

    // ─────────────────────────────────────────────────────────
    /**
     * ELIMINAR — Elimina un contacto y sus teléfonos
     * ────────────────────────────────────────────────
     * URL   : GET /contactos/eliminar/5
     * Param : $id → ID del contacto a eliminar
     *
     * Los teléfonos se eliminan automáticamente por la
     * restricción ON DELETE CASCADE definida en SQL Server.
     * Siempre redirige al listado con mensaje flash.
     */
    public function eliminar(int $id): void
    {
        // Obtiene el contacto antes de borrarlo
        // para poder usar su nombre en el flash
        $contacto = $this->contactoModel->getById($id);

        if ($contacto) {
            $this->contactoModel->eliminar($id);

            $this->flash(
                'danger',
                "Contacto <strong>{$contacto['nombre']} {$contacto['apellido']}</strong>
                 eliminado correctamente."
            );
        } else {
            $this->flash('warning', 'El contacto no existe o ya fue eliminado.');
        }

        $this->redirect('contactos');
    }

    // ─────────────────────────────────────────────────────────
    /**
     * BUSCADORAJAX — Endpoint AJAX para autocompletado
     * ──────────────────────────────────────────────────
     * URL    : GET /contactos/buscadorajax?q=juan
     * Retorna: JSON — NO carga ninguna vista
     *
     * Seguridad:
     *   - Solo acepta peticiones con header X-Requested-With
     *   - Mínimo 2 caracteres para ejecutar la búsqueda
     *   - Los parámetros van con LIKE en PDO (sin SQL injection)
     *
     * Flujo:
     *   JS escribe → fetch() con header AJAX →
     *   este método → Model::buscar() → SQL Server →
     *   json_encode() → JS pinta dropdown
     */
    public function buscadorajax(): void
    {
        // ── Seguridad: solo peticiones AJAX ─────────────────
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) ||
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
            http_response_code(403);
            exit();
        }

        // ── Obtener y validar término de búsqueda ───────────
        $termino = trim($_GET['q'] ?? '');

        // Mínimo 2 caracteres para evitar consultas masivas
        if (strlen($termino) < 2) {
            header('Content-Type: application/json');
            echo json_encode([]);
            exit();
        }

        // ── Ejecutar búsqueda en el Model ───────────────────
        // Busca en: nombre, apellido, alias, email
        $resultados = $this->contactoModel->buscar($termino);

        // ── Retornar JSON al JavaScript ─────────────────────
        header('Content-Type: application/json');
        echo json_encode($resultados);
        exit();
    }
}
?>