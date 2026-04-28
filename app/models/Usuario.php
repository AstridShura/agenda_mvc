<?php

/**
 * ============================================================
 * MODEL — Usuario
 * ============================================================
 * Maneja toda la lógica de datos para usuarios.
 * Incluye JOINs con categorias y telefonos.
 * ============================================================
 */

class Usuario extends Model
{
    private string $tabla = 'usuarios';

    // ─────────────────────────────────────────────────────────
    /**
     * Retorna todos los usuarios.
     */
    public function getAll(): array
    {
        $sql = "SELECT id, nombre, apellido, email, usuario, password, rol, activo, fecha_alta 
                FROM usuarios 
                ORDER BY nombre, apellido";

        return $this->db->query($sql)->fetchAll();
    }

    // ───────────────────────────────────────────────────────── 
    /**
     * Retorna un usuario por ID.
     */
    public function getById(int $id): array|false
    {
        $sql = "SELECT id, nombre, apellido, email, usuario, password, rol, activo, fecha_alta                 
                FROM usuarios 
                WHERE id = ?";

        return $this->db->query($sql, [$id])->fetch();
    }

    // ─────────────────────────────────────────────────────────
    /**
     * Busca un usuario por su email. 27/04/26
     * Usado por AuthController para el login.
     * Retorna false si no existe.
     *
     * @param  string $email
     * @return array|false
     */
    public function getByEmail(string $email): array|false
    {
        return $this->db->query(
            "SELECT id, nombre, apellido, email,
                    password, rol, activo, tema 
            FROM {$this->tabla}
            WHERE email = ?",
            [$email]
        )->fetch();
    }    

    // ─────────────────────────────────────────────────────────
    /**
     * Crea un nuevo usuario.
     * Retorna el ID del registro insertado.
     */
    public function crear(array $datos): int
    {
        $sql = "INSERT INTO {$this->tabla}
                    (nombre, apellido, email, usuario, password, rol, activo)
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $this->db->query($sql, [
            $datos['nombre'],
            $datos['apellido'],
            $datos['email'],
            $datos['usuario'],
            // Hash seguro del password — NUNCA texto plano
            password_hash($datos['password'], PASSWORD_BCRYPT),
            $datos['rol'],
            $datos['activo']
            // fecha_alta usa DEFAULT GETDATE() — SQL Server la pone solo 
        ]);

        return (int) $this->db->lastInsertId();
    }

    // ─────────────────────────────────────────────────────────
    /**
     * Actualiza un usuario existente.
     */
    public function actualizar(int $id, array $datos): void
    {
        $sql = "UPDATE {$this->tabla}
                SET nombre       = ?,
                    apellido     = ?,
                    email        = ?,
                    usuario      = ?,
                    rol          = ?,
                    activo       = ?
                WHERE id = ?";
    // Sin password (se cambia en formulario separado)
    // Sin fecha_alta (no debe cambiar nunca)
        $this->db->query($sql, [
            $datos['nombre'],
            $datos['apellido'],
            $datos['email'],
            $datos['usuario'],
            $datos['rol'],
            $datos['activo'],
            $id
        ]);
    }

    // ─────────────────────────────────────────────────────────
    /**
     * Elimina un usuario por ID.
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
     * Busca usuarios por nombre o apellido.
     */
    public function buscar(string $termino): array
    {
        $sql = "SELECT nombre, apellido, email, usuario, password, rol, activo, fecha_alta 
                FROM usuarios 
                WHERE nombre   LIKE ?
                   OR apellido LIKE ?
                ORDER BY nombre";

        $like = '%' . $termino . '%';
        return $this->db->query($sql, [$like, $like])->fetchAll();
    }

    // ─────────────────────────────────────────────────────────
    /**
     * Buscador de usuarios por nombre, apellido, email o usuario.
     * Retorna array listo para JSON.
     */
    public function buscajax(string $termino): array
    {
        $sql = "SELECT id, nombre, apellido, email, usuario, password, rol, activo 
                FROM {$this->tabla} 
                WHERE nombre   LIKE ?
                   OR apellido LIKE ?
                   OR email    LIKE ?
                   OR usuario  LIKE ?
                ORDER BY nombre, apellido";

        $like = '%' . $termino . '%';

        return $this->db
            ->query($sql, [$like, $like, $like, $like])
            ->fetchAll();
    }

    // ─────────────────────────────────────────────────────────
    /**
     * Actualiza el password de un usuario.
     * SIEMPRE aplica hash BCRYPT antes de guardar.
     * NUNCA guarda texto plano en la BD.
     *
     * @param int    $id    ID del usuario
     * @param string $nuevo Password nuevo en texto plano
     */
    public function cambiarpassword(int $id, string $nuevo): void
    {
        $this->db->query(
            "UPDATE {$this->tabla}
            SET password = ?
            WHERE id     = ?",
            [
                password_hash($nuevo, PASSWORD_BCRYPT),
                $id
            ]
        );
    }
    //Para Paginador 27/04/26
    // ─────────────────────────────────────────────────────────
    /**
     * Cuenta el total de usuarios en BD.
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
     * Retorna usuarios paginados.
     * OFFSET/FETCH NEXT es la sintaxis correcta en SQL Server.
     *
     * @param  int $limite  Registros por página
     * @param  int $offset  Desde qué registro empezar
     * @return array
     */
    public function getAllPaginado(int $limite, int $offset): array
    {
        $sql = "SELECT
                    id, nombre, apellido, email,
                    usuario, rol, activo, fecha_alta
                FROM {$this->tabla}
                ORDER BY nombre, apellido
                OFFSET " . (int)$offset . " ROWS
                FETCH NEXT " . (int)$limite . " ROWS ONLY";

        return $this->db->query($sql)->fetchAll();
    }

    //Para cambio de Tema 28/04/26
    // ─────────────────────────────────────────────────────────
    /**
     * Guarda la preferencia de tema del usuario.
     * Se llama cuando el usuario hace clic en ☀️/🌙
     *
     * @param int    $id   ID del usuario
     * @param string $tema 'claro' o 'oscuro'
     */
    public function guardarTema(int $id, string $tema): void
    {
        // Validar que solo sean valores permitidos
        if (!in_array($tema, ['claro', 'oscuro'])) {
            $tema = 'claro';
        }

        $this->db->query(
            "UPDATE {$this->tabla}
            SET tema = ?
            WHERE id = ?",
            [$tema, $id]
        );
    }
}