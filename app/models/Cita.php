<?php

/**
 * ============================================================
 * MODEL — Cita
 * ============================================================
 * Maneja toda la lógica de datos para citas.
 * Hereda de Model → tiene acceso a $this->db (PDO)
 *
 * Métodos:
 *   getAll()            → todas las citas con contacto
 *   getPorEstado()      → filtradas por estado
 *   getById()           → una cita por ID
 *   getParaCalendario() → formato JSON para FullCalendar
 *   crear()             → INSERT nueva cita
 *   actualizar()        → UPDATE cita existente
 *   eliminar()          → DELETE cita por ID
 *   cambiarEstado()     → solo actualiza el estado
 *   buscar()            → búsqueda AJAX
 * ============================================================
 */

class Cita extends Model
{
    private string $tabla = 'citas';

    // Colores por estado para FullCalendar y badges
    public static array $coloresEstado = [
        'Pendiente'  => '#ffc107',
        'Confirmada' => '#28a745',
        'Cancelada'  => '#dc3545',
    ];

    // Íconos por tipo de cita
    public static array $iconosTipo = [
        'Reunion'  => 'bi-people-fill',
        'Llamada'  => 'bi-telephone-fill',
        'Visita'   => 'bi-geo-alt-fill',
        'Otro'     => 'bi-calendar-event-fill',
    ];

    // ─────────────────────────────────────────────────────────
    /**
     * Retorna TODAS las citas con datos del contacto.
     * Incluye JOIN a contactos para mostrar el nombre.
     */
    public function getAll(): array
    {
        $sql = "SELECT
                    ci.id,
                    ci.titulo,
                    ci.descripcion,
                    ci.fecha_cita,
                    ci.hora_inicio,
                    ci.hora_fin,
                    ci.tipo,
                    ci.estado,
                    ci.fecha_alta,
                    ci.id_contacto,
                    co.nombre   AS contacto_nombre,
                    co.apellido AS contacto_apellido
                FROM {$this->tabla} ci
                INNER JOIN contactos co ON ci.id_contacto = co.id
                ORDER BY ci.fecha_cita, ci.hora_inicio";

        return $this->db->query($sql)->fetchAll();
    }

    // ─────────────────────────────────────────────────────────
    /**
     * Retorna citas filtradas por estado.
     * Estado: Pendiente | Confirmada | Cancelada
     *
     * @param  string $estado Estado a filtrar
     * @return array
     */
    public function getPorEstado(string $estado): array
    {
        $sql = "SELECT
                    ci.id,
                    ci.titulo,
                    ci.fecha_cita,
                    ci.hora_inicio,
                    ci.hora_fin,
                    ci.tipo,
                    ci.estado,
                    ci.id_contacto,
                    co.nombre   AS contacto_nombre,
                    co.apellido AS contacto_apellido
                FROM {$this->tabla} ci
                INNER JOIN contactos co ON ci.id_contacto = co.id
                WHERE ci.estado = ?
                ORDER BY ci.fecha_cita, ci.hora_inicio";

        return $this->db->query($sql, [$estado])->fetchAll();
    }

    // ─────────────────────────────────────────────────────────
    /**
     * Retorna una cita por ID con datos del contacto.
     *
     * @param  int $id
     * @return array|false
     */
    public function getById(int $id): array|false
    {
        $sql = "SELECT
                    ci.*,
                    co.nombre   AS contacto_nombre,
                    co.apellido AS contacto_apellido
                FROM {$this->tabla} ci
                INNER JOIN contactos co ON ci.id_contacto = co.id
                WHERE ci.id = ?";

        return $this->db->query($sql, [$id])->fetch();
    }

    // ─────────────────────────────────────────────────────────
    /**
     * Retorna citas en formato que FullCalendar necesita.
     *
     * FullCalendar espera un array de objetos con:
     *   id, title, start, end, color, extendedProps
     *
     * @return array Listo para json_encode()
     */
    public function getParaCalendario(): array
    {
        $citas = $this->getAll();
        $eventos = [];

        foreach ($citas as $cita) {
            // Formato fecha para FullCalendar: YYYY-MM-DDTHH:MM
            $inicio = $cita['fecha_cita'] . 'T' .
                      substr($cita['hora_inicio'], 0, 5);

            $fin = $cita['hora_fin']
                ? $cita['fecha_cita'] . 'T' . substr($cita['hora_fin'], 0, 5)
                : null;

            $color = self::$coloresEstado[$cita['estado']] ?? '#6c757d';

            $eventos[] = [
                'id'    => $cita['id'],
                'title' => $cita['titulo'],
                'start' => $inicio,
                'end'   => $fin,
                'color' => $color,
                'extendedProps' => [
                    'contacto' => $cita['contacto_nombre'] . ' ' .
                                  $cita['contacto_apellido'],
                    'tipo'     => $cita['tipo'],
                    'estado'   => $cita['estado'],
                    'url'      => '/citas/ver/' . $cita['id'],
                ]
            ];
        }

        return $eventos;
    }

    // ─────────────────────────────────────────────────────────
    /**
     * Inserta una nueva cita.
     *
     * @param  array $datos Datos del formulario
     * @return int   ID del nuevo registro
     */
    public function crear(array $datos): int
    {
        $sql = "INSERT INTO {$this->tabla}
                    (id_contacto, titulo, descripcion,
                     fecha_cita, hora_inicio, hora_fin,
                     tipo, estado)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $this->db->query($sql, [
            $datos['id_contacto'],
            $datos['titulo'],
            $datos['descripcion'] ?: null,
            $datos['fecha_cita'],
            $datos['hora_inicio'],
            $datos['hora_fin']    ?: null,
            $datos['tipo'],
            $datos['estado']
        ]);

        return (int) $this->db->lastInsertId();
    }

    // ─────────────────────────────────────────────────────────
    /**
     * Actualiza una cita existente.
     *
     * @param int   $id
     * @param array $datos
     */
    public function actualizar(int $id, array $datos): void
    {
        $sql = "UPDATE {$this->tabla}
                SET id_contacto = ?,
                    titulo      = ?,
                    descripcion = ?,
                    fecha_cita  = ?,
                    hora_inicio = ?,
                    hora_fin    = ?,
                    tipo        = ?,
                    estado      = ?
                WHERE id = ?";

        $this->db->query($sql, [
            $datos['id_contacto'],
            $datos['titulo'],
            $datos['descripcion'] ?: null,
            $datos['fecha_cita'],
            $datos['hora_inicio'],
            $datos['hora_fin']    ?: null,
            $datos['tipo'],
            $datos['estado'],
            $id
        ]);
    }

    // ─────────────────────────────────────────────────────────
    /**
     * Elimina una cita por ID.
     *
     * @param int $id
     */
    public function eliminar(int $id): void
    {
        $this->db->query(
            "DELETE FROM {$this->tabla} WHERE id = ?",
            [$id]
        );
    }

    // ─────────────────────────────────────────────────────────
    /**
     * Cambia solo el estado de una cita.
     * Útil para confirmar/cancelar desde la lista.
     *
     * @param int    $id
     * @param string $estado Pendiente|Confirmada|Cancelada
     */
    public function cambiarEstado(int $id, string $estado): void
    {
        $estadosValidos = ['Pendiente', 'Confirmada', 'Cancelada'];

        if (!in_array($estado, $estadosValidos)) return;

        $this->db->query(
            "UPDATE {$this->tabla} SET estado = ? WHERE id = ?",
            [$estado, $id]
        );
    }

    // ─────────────────────────────────────────────────────────
    /**
     * Búsqueda AJAX por título o contacto.
     *
     * @param  string $termino
     * @return array
     */
    public function buscar(string $termino): array
    {
        $sql = "SELECT
                    ci.id,
                    ci.titulo,
                    ci.fecha_cita,
                    ci.hora_inicio,
                    ci.tipo,
                    ci.estado,
                    co.nombre   AS contacto_nombre,
                    co.apellido AS contacto_apellido
                FROM {$this->tabla} ci
                INNER JOIN contactos co ON ci.id_contacto = co.id
                WHERE ci.titulo      LIKE ?
                   OR co.nombre      LIKE ?
                   OR co.apellido    LIKE ?
                ORDER BY ci.fecha_cita DESC";

        $like = '%' . $termino . '%';
        return $this->db->query($sql, [$like, $like, $like])->fetchAll();
    }
}