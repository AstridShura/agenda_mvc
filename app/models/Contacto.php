<?php

/**
 * ============================================================
 * MODEL — Contacto
 * ============================================================
 * Maneja toda la lógica de datos para contactos.
 * Incluye JOINs con categorias y telefonos.
 * ============================================================
 */

class Contacto extends Model
{
    private string $tabla = 'contactos';

    // ─────────────────────────────────────────────────────────
    /**
     * Retorna todos los contactos con su categoría
     * y la cantidad de teléfonos que tienen.
     */
    public function getAll(): array
    {
        $sql = "SELECT
                    c.id,
                    c.nombre,
                    c.apellido,
                    c.email,
                    c.alias, 
                    c.fecha_alta,
                    cat.nombre   AS categoria,
                    cat.color    AS categoria_color,
                    COUNT(t.id)  AS total_telefonos
                FROM contactos c
                LEFT JOIN categorias cat ON c.id_categoria = cat.id
                LEFT JOIN telefonos  t   ON c.id = t.id_contacto
                GROUP BY
                    c.id, c.nombre, c.apellido,
                    c.email, c.alias, c.fecha_alta,
                    cat.nombre, cat.color
                ORDER BY c.apellido, c.nombre";

        return $this->db->query($sql)->fetchAll();
    }

    // ─────────────────────────────────────────────────────────
    /**
     * Retorna un contacto por ID con su categoría.
     */
    public function getById(int $id): array|false
    {
        $sql = "SELECT
                    c.*,
                    cat.nombre AS categoria,
                    cat.color  AS categoria_color
                FROM contactos c
                LEFT JOIN categorias cat ON c.id_categoria = cat.id
                WHERE c.id = ?";

        return $this->db->query($sql, [$id])->fetch();
    }

    // ─────────────────────────────────────────────────────────
    /**
     * Crea un nuevo contacto.
     * Retorna el ID del registro insertado.
     */
    public function crear(array $datos): int
    {
        $sql = "INSERT INTO {$this->tabla}
                    (nombre, apellido, email, direccion, alias, id_categoria, latitud, longitud)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $this->db->query($sql, [
            $datos['nombre'],
            $datos['apellido'],
            $datos['email'],
            $datos['direccion'],
            $datos['alias']        ?: null,
            $datos['id_categoria'] ?: null,
            $datos['latitud']      ?: null,
            $datos['longitud']     ?: null,
        ]);

        return (int) $this->db->lastInsertId();
    }

    // ─────────────────────────────────────────────────────────
    /**
     * Actualiza un contacto existente.
     */
    public function actualizar(int $id, array $datos): void
    {
        $sql = "UPDATE {$this->tabla}
                SET nombre       = ?,
                    apellido     = ?,
                    email        = ?,
                    direccion    = ?,
                    alias        = ?,
                    id_categoria = ?,
                    latitud      = ?,
                    longitud     = ?
                WHERE id = ?";

        $this->db->query($sql, [
            $datos['nombre'],
            $datos['apellido'],
            $datos['email'],
            $datos['direccion'],
            $datos['alias']        ?: null,
            $datos['id_categoria'] ?: null,
            $datos['latitud']      ?: null,
            $datos['longitud']     ?: null,
            $id
        ]);
    }

    // ─────────────────────────────────────────────────────────
    /**
     * Elimina un contacto por ID.
     * Los teléfonos se eliminan solos por CASCADE.
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
     * Busca contactos por nombre o apellido.
     */
    public function buscar(string $termino): array
    {
        $sql = "SELECT
                    c.id, c.nombre, c.apellido, c.email, c.alias, 
                    cat.nombre AS categoria,
                    cat.color  AS categoria_color
                FROM contactos c
                LEFT JOIN categorias cat ON c.id_categoria = cat.id
                WHERE c.nombre   LIKE ?
                   OR c.apellido LIKE ?
                ORDER BY c.apellido";

        $like = '%' . $termino . '%';
        return $this->db->query($sql, [$like, $like])->fetchAll();
    }

    //21/04/2026 
    // ─────────────────────────────────────────────────────────
    /**
     * Buscador de contactos por nombre, apellido, alias o email.
     * Retorna array listo para JSON.
     */
    public function buscardorajax(string $termino): array
    {
        $sql = "SELECT
                    c.id,
                    c.nombre,
                    c.apellido,
                    c.email,
                    c.alias,                     
                    cat.nombre AS categoria,
                    cat.color  AS categoria_color
                FROM contactos c
                LEFT JOIN categorias cat ON c.id_categoria = cat.id
                WHERE c.nombre   LIKE ?
                OR c.apellido LIKE ?
                OR c.alias    LIKE ?
                OR c.email    LIKE ?
                ORDER BY c.apellido, c.nombre";

        $like = '%' . $termino . '%';

        return $this->db
            ->query($sql, [$like, $like, $like, $like])
            ->fetchAll();
    }

    //27/04/26 PAra paginador
    // ─────────────────────────────────────────────────────────
    /**
     * Cuenta el total de contactos en BD.
     * Usado por el Paginador para calcular páginas.
     */
    public function contarTodos(): int
    {
        $result = $this->db->query(
            "SELECT COUNT(*) AS total FROM {$this->tabla}"
        )->fetch();

        return (int) $result['total'];
    }

    // ─────────────────────────────────────────────────────────
    /**
     * Retorna contactos paginados.
     * Usa OFFSET/FETCH NEXT — sintaxis de SQL Server.
     *
     * @param  int $limite  Registros por página
     * @param  int $offset  Desde qué registro empezar
     * @return array
     */
    public function getAllPaginado(int $limite, int $offset): array
    {
        $sql = "SELECT
                    c.id,
                    c.nombre,
                    c.apellido,
                    c.alias,
                    c.email,
                    c.fecha_alta,
                    cat.nombre  AS categoria,
                    cat.color   AS categoria_color,
                    COUNT(t.id) AS total_telefonos
                FROM contactos c
                LEFT JOIN categorias cat ON c.id_categoria = cat.id
                LEFT JOIN telefonos  t   ON c.id = t.id_contacto
                GROUP BY
                    c.id, c.nombre, c.apellido, c.alias,
                    c.email, c.fecha_alta,
                    cat.nombre, cat.color
                ORDER BY c.apellido, c.nombre
                OFFSET " . (int)$offset . " ROWS
                FETCH NEXT " . (int)$limite . " ROWS ONLY";

        // Sin parámetros PDO para OFFSET/FETCH
        // Los valores van directamente en la query como enteros
        return $this->db->query($sql)->fetchAll();
    }

// 30/04/26 Para Geolocalizacion de Citas
/**
 * Guarda las coordenadas de un contacto.
 * Se llama desde el formulario crear/editar
 * cuando el usuario marca una ubicación en el mapa.
 *
 * @param int        $id       ID del contacto
 * @param float|null $latitud  Latitud decimal
 * @param float|null $longitud Longitud decimal
 */
public function guardarUbicacion(int $id, ?float $latitud, ?float $longitud): void 
{
    $this->db->query(
        "UPDATE {$this->tabla}
         SET latitud  = ?,
             longitud = ?
         WHERE id = ?",
        [$latitud, $longitud, $id]
    );
}

    // ─────────────────────────────────────────────────────────
    /**
     * Retorna todos los contactos que tienen
     * coordenadas asignadas.
     * Usado para el mapa general con todos los marcadores.
     */
    public function getConUbicacion(): array
    {
        $sql = "SELECT
                    c.id,
                    c.nombre,
                    c.apellido,
                    c.alias,
                    c.email,
                    c.direccion,
                    c.latitud,
                    c.longitud,
                    cat.nombre AS categoria,
                    cat.color  AS categoria_color
                FROM {$this->tabla} c
                LEFT JOIN categorias cat ON c.id_categoria = cat.id
                WHERE c.latitud  IS NOT NULL
                AND c.longitud IS NOT NULL
                ORDER BY c.apellido, c.nombre";

        return $this->db->query($sql)->fetchAll();
    }

}