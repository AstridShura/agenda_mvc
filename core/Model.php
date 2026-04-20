<?php

/**
 * ============================================================
 * CLASE BASE — Model
 * ============================================================
 * Todos los modelos de la app heredan de esta clase.
 * Provee acceso directo a la base de datos.
 *
 * Uso en un Model hijo:
 *   class Contacto extends Model { ... }
 *   $this->db->query('SELECT ...');
 * ============================================================
 */

class Model
{
    // Instancia de Database disponible en todos los modelos
    protected Database $db;

    public function __construct()
    {
        // Obtiene la conexión Singleton
        $this->db = Database::getInstance();
    }
}
?>