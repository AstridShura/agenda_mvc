<?php

/**
 * ============================================================
 * MODEL — Telefono
 * ============================================================
 * Maneja los teléfonos asociados a un contacto.
 * ============================================================
 */

class Telefono extends Model
{
    private string $tabla = 'telefonos';

    // ─────────────────────────────────────────────────────────
    /**
     * Retorna todos los teléfonos de un contacto.
     */
    public function getPorContacto(int $idContacto): array
    {
        return $this->db
            ->query(
                "SELECT * FROM {$this->tabla}
                 WHERE id_contacto = ?
                 ORDER BY tipo",
                [$idContacto]
            )
            ->fetchAll();
    }

    // ─────────────────────────────────────────────────────────
    /**
     * Agrega un teléfono a un contacto.
     */
    public function agregar(int $idContacto, string $numero, string $tipo): void
    {
        $this->db->query(
            "INSERT INTO {$this->tabla} (id_contacto, numero, tipo)
             VALUES (?, ?, ?)",
            [$idContacto, $numero, $tipo]
        );
    }

    // ─────────────────────────────────────────────────────────
    /**
     * Actualiza un teléfono existente.
     */
    public function actualizar(int $id, string $numero, string $tipo): void
    {
        $this->db->query(
            "UPDATE {$this->tabla}
             SET numero = ?, tipo = ?
             WHERE id = ?",
            [$numero, $tipo, $id]
        );
    }

    // ─────────────────────────────────────────────────────────
    /**
     * Elimina un teléfono por ID.
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
     * Elimina TODOS los teléfonos de un contacto.
     * Útil al editar — borra y vuelve a insertar.
     */
    public function eliminarPorContacto(int $idContacto): void
    {
        $this->db->query(
            "DELETE FROM {$this->tabla} WHERE id_contacto = ?",
            [$idContacto]
        );
    }
}
?>