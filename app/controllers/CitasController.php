<?php

/**
 * ============================================================
 * CitasController — CRUD completo de Citas
 * ============================================================
 * URLs manejadas:
 *   GET  /citas                  → index()
 *   GET  /citas/ver/5            → ver(5)
 *   GET  /citas/crear            → crear()
 *   POST /citas/crear            → crear()
 *   GET  /citas/editar/5         → editar(5)
 *   POST /citas/editar/5         → editar(5)
 *   GET  /citas/eliminar/5       → eliminar(5)
 *   GET  /citas/cambiarEstado/5  → cambiarEstado(5)
 *   GET  /citas/calendario       → calendario() AJAX JSON
 *   GET  /citas/buscadorcitas    → buscadorcitas() AJAX
 * ============================================================
 */

class CitasController extends Controller
{
    private object $citaModel;
    private object $contactoModel;

    // ─────────────────────────────────────────────────────────
    public function __construct()
    {
        parent::__construct();
        $this->citaModel     = $this->model('Cita');
        $this->contactoModel = $this->model('Contacto');
    }

    // ─────────────────────────────────────────────────────────
    /**
     * INDEX — Lista + Calendario
     * URL: GET /citas
     *
     * Pasa las citas a la vista para la tabla.
     * FullCalendar carga sus eventos via AJAX
     * llamando a /citas/calendario
     */
    public function index(): void
    {
        $citas = $this->citaModel->getAll();

        // Conteo por estado para los badges del tab
        $conteos = [
            'todas'      => count($citas),
            'Pendiente'  => 0,
            'Confirmada' => 0,
            'Cancelada'  => 0,
        ];
        foreach ($citas as $c) {
            $conteos[$c['estado']]++;
        }

        $this->view('citas/index', [
            'titulo'  => 'Citas',
            'citas'   => $citas,
            'conteos' => $conteos
        ]);
    }

    // ─────────────────────────────────────────────────────────
    /**
     * VER — Detalle completo de una cita
     * URL: GET /citas/ver/5
     */
    public function ver(int $id): void
    {
        $cita = $this->citaModel->getById($id);

        if (!$cita) {
            $this->flash('warning', 'La cita no existe o fue eliminada.');
            $this->redirect('citas');
        }

        $this->view('citas/ver', [
            'titulo' => $cita['titulo'],
            'cita'   => $cita
        ]);
    }

    // ─────────────────────────────────────────────────────────
    /**
     * CREAR — Formulario y procesamiento
     * GET : /citas/crear → formulario con Flatpickr + TomSelect
     * POST: /citas/crear → valida y guarda
     */
    public function crear(): void
    {
        // Necesitamos la lista de contactos para el selector
        $contactos = $this->contactoModel->getAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $datos = [
                'id_contacto' => (int) ($_POST['id_contacto'] ?? 0),
                'titulo'      => trim($_POST['titulo']      ?? ''),
                'descripcion' => trim($_POST['descripcion'] ?? ''),
                'fecha_cita'  => trim($_POST['fecha_cita']  ?? ''),
                'hora_inicio' => trim($_POST['hora_inicio'] ?? ''),
                'hora_fin'    => trim($_POST['hora_fin']    ?? ''),
                'tipo'        => trim($_POST['tipo']        ?? 'Reunion'),
                'estado'      => trim($_POST['estado']      ?? 'Pendiente'),
            ];

            // ── Validaciones ────────────────────────────────
            $error = $this->validar($datos);

            if ($error) {
                $this->view('citas/crear', [
                    'titulo'     => 'Nueva Cita',
                    'contactos'  => $contactos,
                    'error'      => $error,
                    'datos'      => $datos
                ]);
                return;
            }

            $this->citaModel->crear($datos);

            $this->flash('success',
                "Cita <strong>{$datos['titulo']}</strong> creada correctamente."
            );
            $this->redirect('citas');
        }

        $this->view('citas/crear', [
            'titulo'    => 'Nueva Cita',
            'contactos' => $contactos
        ]);
    }

    // ─────────────────────────────────────────────────────────
    /**
     * EDITAR — Formulario y procesamiento
     * GET : /citas/editar/5
     * POST: /citas/editar/5
     */
    public function editar(int $id): void
    {
        $cita      = $this->citaModel->getById($id);
        $contactos = $this->contactoModel->getAll();

        if (!$cita) {
            $this->flash('warning', 'La cita no existe o fue eliminada.');
            $this->redirect('citas');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $datos = [
                'id_contacto' => (int) ($_POST['id_contacto'] ?? 0),
                'titulo'      => trim($_POST['titulo']      ?? ''),
                'descripcion' => trim($_POST['descripcion'] ?? ''),
                'fecha_cita'  => trim($_POST['fecha_cita']  ?? ''),
                'hora_inicio' => trim($_POST['hora_inicio'] ?? ''),
                'hora_fin'    => trim($_POST['hora_fin']    ?? ''),
                'tipo'        => trim($_POST['tipo']        ?? 'Reunion'),
                'estado'      => trim($_POST['estado']      ?? 'Pendiente'),
            ];

            $error = $this->validar($datos);

            if ($error) {
                $this->view('citas/editar', [
                    'titulo'    => 'Editar Cita',
                    'contactos' => $contactos,
                    'cita'      => $cita,
                    'error'     => $error
                ]);
                return;
            }

            $this->citaModel->actualizar($id, $datos);

            $this->flash('info',
                "Cita <strong>{$datos['titulo']}</strong> actualizada correctamente."
            );
            $this->redirect('citas/ver/' . $id);
        }

        $this->view('citas/editar', [
            'titulo'    => 'Editar Cita',
            'contactos' => $contactos,
            'cita'      => $cita
        ]);
    }

    // ─────────────────────────────────────────────────────────
    /**
     * ELIMINAR
     * URL: GET /citas/eliminar/5
     */
    public function eliminar(int $id): void
    {
        $cita = $this->citaModel->getById($id);

        if ($cita) {
            $this->citaModel->eliminar($id);
            $this->flash('danger',
                "Cita <strong>{$cita['titulo']}</strong> eliminada correctamente."
            );
        } else {
            $this->flash('warning', 'La cita no existe o ya fue eliminada.');
        }

        $this->redirect('citas');
    }

    // ─────────────────────────────────────────────────────────
    /**
     * CAMBIAR ESTADO — Cambia entre Pendiente/Confirmada/Cancelada
     * URL: GET /citas/cambiarEstado/5?estado=Confirmada
     *
     * Permite cambiar el estado directamente desde la lista
     * sin entrar al formulario de edición completo.
     */
    public function cambiarEstado(int $id): void
    {
        $cita   = $this->citaModel->getById($id);
        $estado = trim($_GET['estado'] ?? '');

        if ($cita && $estado) {
            $this->citaModel->cambiarEstado($id, $estado);
            $this->flash('info',
                "Cita <strong>{$cita['titulo']}</strong>
                 marcada como <strong>{$estado}</strong>."
            );
        }

        $this->redirect('citas');
    }

    // ─────────────────────────────────────────────────────────
    /**
     * CALENDARIO — Endpoint JSON para FullCalendar
     * URL: GET /citas/calendario
     *
     * NO valida X-Requested-With porque FullCalendar
     * no envía ese header en sus peticiones fetch().
     * Retorna array de eventos en formato FullCalendar.
     */
    public function calendario(): void
    {
        $eventos = $this->citaModel->getParaCalendario();

        header('Content-Type: application/json');
        echo json_encode($eventos);
        exit();
    }

    // ─────────────────────────────────────────────────────────
    /**
     * BUSCADORCITAS — Endpoint AJAX para autocompletado
     * URL: GET /citas/buscadorcitas?q=reunion
     */
    public function buscadorcitas(): void
    {
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) ||
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
            http_response_code(403);
            exit();
        }

        $termino = trim($_GET['q'] ?? '');

        if (strlen($termino) < 2) {
            header('Content-Type: application/json');
            echo json_encode([]);
            exit();
        }

        $resultados = $this->citaModel->buscar($termino);

        header('Content-Type: application/json');
        echo json_encode($resultados);
        exit();
    }

    // ─────────────────────────────────────────────────────────
    /**
     * VALIDAR — Método privado compartido
     * Evita duplicar validaciones entre crear() y editar()
     *
     * @param  array       $datos
     * @return string|null Error o null si todo OK
     */
    private function validar(array $datos): ?string
    {
        if (empty($datos['titulo'])) {
            return 'El título de la cita es obligatorio.';
        }
        if ($datos['id_contacto'] <= 0) {
            return 'Debes seleccionar un contacto.';
        }
        if (empty($datos['fecha_cita'])) {
            return 'La fecha de la cita es obligatoria.';
        }
        if (empty($datos['hora_inicio'])) {
            return 'La hora de inicio es obligatoria.';
        }
        if ($datos['hora_fin'] && $datos['hora_fin'] <= $datos['hora_inicio']) {
            return 'La hora de fin debe ser posterior a la hora de inicio.';
        }
        return null;
    }

    //28/04/26 Metodos para Exportador
    // ─────────────────────────────────────────────────────────
    /**
     * EXPORTAR EXCEL — Todas las citas
     * ─────────────────────────────────
     * URL: GET /citas/exportarexcel
     *
     * Exporta TODAS las citas con datos del contacto.
     * Incluye: título, contacto, fecha, horario,
     *          tipo, estado y descripción.
     */
    public function exportarexcel(): void
    {
        $citas = $this->citaModel->getAll();

        Exportador::excel(
            $citas,
            [
                'titulo'            => 'Título',
                'contacto_apellido' => 'Apellido Contacto',
                'contacto_nombre'   => 'Nombre Contacto',
                'fecha_cita'        => 'Fecha',
                'hora_inicio'       => 'Hora Inicio',
                'hora_fin'          => 'Hora Fin',
                'tipo'              => 'Tipo',
                'estado'            => 'Estado',
                'descripcion'       => 'Descripción',
            ],
            'Listado de Citas — Agenda MVC',
            'citas_' . date('Ymd_His')
        );
    }

    // ─────────────────────────────────────────────────────────
    /**
     * EXPORTAR PDF — Todas las citas
     * ────────────────────────────────
     * URL: GET /citas/exportarpdf
     */
    public function exportarpdf(): void
    {
        $citas = $this->citaModel->getAll();

        Exportador::pdf(
            $citas,
            [
                'titulo'            => 'Título',
                'contacto_apellido' => 'Apellido',
                'contacto_nombre'   => 'Nombre',
                'fecha_cita'        => 'Fecha',
                'hora_inicio'       => 'Hora Ini',
                'hora_fin'          => 'Hora Fin',
                'tipo'              => 'Tipo',
                'estado'            => 'Estado',
            ],
            'Listado de Citas — Agenda MVC',
            'citas_' . date('Ymd_His')
        );
    }
    
}