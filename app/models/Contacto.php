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
                    (nombre, apellido, email, direccion, alias, id_categoria)
                VALUES (?, ?, ?, ?, ?, ?)";

        $this->db->query($sql, [
            $datos['nombre'],
            $datos['apellido'],
            $datos['email'],
            $datos['direccion'],
            $datos['alias'],
            $datos['id_categoria'] ?: null
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
                    id_categoria = ?
                WHERE id = ?";

        $this->db->query($sql, [
            $datos['nombre'],
            $datos['apellido'],
            $datos['email'],
            $datos['direccion'],
            $datos['alias'],
            $datos['id_categoria'] ?: null,
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

}
?>