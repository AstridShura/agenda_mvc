<?php

/**
 * ============================================================
 * ContactosController — CRUD completo
 * ============================================================
 */

class ContactosController extends Controller
{
    private object $contactoModel;
    private object $telefonoModel;
    private object $categoriaModel;

    // ─────────────────────────────────────────────────────────
    public function __construct()
    {
        // Carga los 3 modelos que usará este controller
        $this->contactoModel  = $this->model('Contacto');
        $this->telefonoModel  = $this->model('Telefono');
        $this->categoriaModel = $this->model('Categoria');
    }

    // ─────────────────────────────────────────────────────────
    /**
     * INDEX — Lista todos los contactos
     * URL: /contactos
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
     * VER — Detalle de un contacto con sus teléfonos
     * URL: /contactos/ver/5
     */
    public function ver(int $id): void
    {
        $contacto  = $this->contactoModel->getById($id);
        $telefonos = $this->telefonoModel->getPorContacto($id);

        if (!$contacto) {
            $this->redirect('contactos');
        }

        $this->view('contactos/ver', [
            'titulo'    => $contacto['nombre'] . ' ' . $contacto['apellido'],
            'contacto'  => $contacto,
            'telefonos' => $telefonos
        ]);
    }

    // ─────────────────────────────────────────────────────────
    /**
     * CREAR — Formulario y procesamiento
     * URL GET : /contactos/crear       → muestra formulario
     * URL POST: /contactos/crear       → guarda datos
     */
    public function crear(): void
    {
        $categorias = $this->categoriaModel->getAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Recoger y sanitizar datos del formulario
            $datos = [
                'nombre'       => trim($_POST['nombre'] ?? ''),
                'apellido'     => trim($_POST['apellido'] ?? ''),
                'email'        => trim($_POST['email'] ?? ''),
                'direccion'    => trim($_POST['direccion'] ?? ''),
                'id_categoria' => $_POST['id_categoria'] ?? null
            ];

            // Validación básica
            if (empty($datos['nombre']) || empty($datos['apellido'])) {
                $this->view('contactos/crear', [
                    'titulo'     => 'Nuevo Contacto',
                    'categorias' => $categorias,
                    'error'      => 'Nombre y apellido son obligatorios.',
                    'datos'      => $datos
                ]);
                return;
            }

            // Guardar contacto y obtener su nuevo ID
            $idContacto = $this->contactoModel->crear($datos);

            // Guardar teléfonos (pueden ser varios)
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

            $this->redirect('contactos');
        }

        // GET → mostrar formulario vacío
        $this->view('contactos/crear', [
            'titulo'     => 'Nuevo Contacto',
            'categorias' => $categorias
        ]);
    }

    // ─────────────────────────────────────────────────────────
    /**
     * EDITAR — Formulario y procesamiento
     * URL GET : /contactos/editar/5    → muestra formulario
     * URL POST: /contactos/editar/5    → guarda cambios
     */
    public function editar(int $id): void
    {
        $contacto   = $this->contactoModel->getById($id);
        $categorias = $this->categoriaModel->getAll();
        $telefonos  = $this->telefonoModel->getPorContacto($id);

        if (!$contacto) {
            $this->redirect('contactos');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $datos = [
                'nombre'       => trim($_POST['nombre'] ?? ''),
                'apellido'     => trim($_POST['apellido'] ?? ''),
                'email'        => trim($_POST['email'] ?? ''),
                'direccion'    => trim($_POST['direccion'] ?? ''),
                'id_categoria' => $_POST['id_categoria'] ?? null
            ];

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

            // Actualizar contacto
            $this->contactoModel->actualizar($id, $datos);

            // Teléfonos: borrar todos y reinsertar
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

            $this->redirect('contactos/ver/' . $id);
        }

        // GET → mostrar formulario con datos actuales
        $this->view('contactos/editar', [
            'titulo'     => 'Editar Contacto',
            'categorias' => $categorias,
            'contacto'   => $contacto,
            'telefonos'  => $telefonos
        ]);
    }

    // ─────────────────────────────────────────────────────────
    /**
     * ELIMINAR — Elimina un contacto
     * URL: /contactos/eliminar/5
     */
    public function eliminar(int $id): void
    {
        $this->contactoModel->eliminar($id);
        $this->redirect('contactos');
    }
}
?>