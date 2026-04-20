<?php

/**
 * ============================================================
 * MODEL — Categoria
 * ============================================================
 * Hereda de Model → tiene acceso a $this->db
 * Solo se encarga de datos de categorías.
 * ============================================================
 */

class Categoria extends Model
{
    private string $tabla = 'categorias';

    // ─────────────────────────────────────────────────────────
    /**
     * Retorna todas las categorías.
     */
    public function getAll(): array
    {
        return $this->db
            ->query("SELECT * FROM {$this->tabla} ORDER BY nombre")
            ->fetchAll();
    }

    // ─────────────────────────────────────────────────────────
    /**
     * Retorna una categoría por ID.
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
}
?>