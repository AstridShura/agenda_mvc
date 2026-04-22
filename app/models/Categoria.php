<?php

/**
 * ============================================================
 * MODEL — Categoria
 * ============================================================
 * Maneja toda la lógica de datos para categorías.
 * Hereda de Model → tiene acceso a $this->db (PDO)
 *
 * Métodos:
 *   getAll()         → todas las categorías con conteo
 *   getById()        → una categoría por ID
 *   crear()          → INSERT nueva categoría
 *   actualizar()     → UPDATE categoría existente
 *   eliminar()       → DELETE categoría por ID
 *   tieneContactos() → verifica si tiene contactos
 *                      asociados antes de eliminar
 * ============================================================
 */

class Categoria extends Model
{
    private string $tabla = 'categorias';

    // ─────────────────────────────────────────────────────────
    /**
     * Retorna TODAS las categorías con la cantidad
     * de contactos que tiene cada una.
     *
     * Usa LEFT JOIN para incluir categorías sin contactos.
     * COUNT(c.id) retorna 0 si no tiene contactos.
     */
    public function getAll(): array
    {
        $sql = "SELECT
                    cat.id,
                    cat.nombre,
                    cat.color,
                    COUNT(c.id) AS total_contactos
                FROM {$this->tabla} cat
                LEFT JOIN contactos c ON cat.id = c.id_categoria
                GROUP BY cat.id, cat.nombre, cat.color
                ORDER BY cat.nombre";

        return $this->db->query($sql)->fetchAll();
    }

    // ─────────────────────────────────────────────────────────
    /**
     * Retorna una categoría por su ID.
     * Retorna false si no existe.
     *
     * @param  int $id ID de la categoría
     * @return array|false
     */
    public function getById(int $id): array|false
    {
        return $this->db
            ->query(
                "SELECT * FROM {$this->tabla} WHERE id = ?",
                [$id]
            )
            ->fetch();
    }

    // ─────────────────────────────────────────────────────────
    /**
     * Inserta una nueva categoría en la BD.
     * Retorna el ID del registro insertado.
     *
     * @param  array $datos ['nombre' => '', 'color' => '']
     * @return int  ID del nuevo registro
     */
    public function crear(array $datos): int
    {
        $this->db->query(
            "INSERT INTO {$this->tabla} (nombre, color)
             VALUES (?, ?)",
            [
                $datos['nombre'],
                $datos['color']
            ]
        );

        return (int) $this->db->lastInsertId();
    }

    // ─────────────────────────────────────────────────────────
    /**
     * Actualiza una categoría existente.
     *
     * @param int   $id    ID de la categoría a actualizar
     * @param array $datos ['nombre' => '', 'color' => '']
     */
    public function actualizar(int $id, array $datos): void
    {
        $this->db->query(
            "UPDATE {$this->tabla}
             SET nombre = ?,
                 color  = ?
             WHERE id   = ?",
            [
                $datos['nombre'],
                $datos['color'],
                $id
            ]
        );
    }

    // ─────────────────────────────────────────────────────────
    /**
     * Elimina una categoría por ID.
     *
     * IMPORTANTE: Antes de llamar este método siempre
     * verificar con tieneContactos() para no dejar
     * contactos sin categoría de forma inesperada.
     *
     * @param int $id ID de la categoría a eliminar
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
     * Verifica si una categoría tiene contactos asociados.
     *
     * Esto es una VALIDACIÓN DE NEGOCIO — no debemos
     * eliminar una categoría que tenga contactos porque
     * quedarían con id_categoria huérfano.
     *
     * Retorna true si tiene contactos → NO eliminar.
     * Retorna false si está vacía → SE PUEDE eliminar.
     *
     * @param  int  $id ID de la categoría
     * @return bool
     */
    public function tieneContactos(int $id): bool
    {
        $result = $this->db->query(
            "SELECT COUNT(*) AS total
             FROM contactos
             WHERE id_categoria = ?",
            [$id]
        )->fetch();

        return (int) $result['total'] > 0;
    }
}
