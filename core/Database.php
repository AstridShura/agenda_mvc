<?php

/**
 * ============================================================
 * CLASE Database — Conexión a SQL Server
 * ============================================================
 * Implementa el patrón Singleton:
 * Solo existe UNA conexión durante toda la petición.
 *
 * Uso desde cualquier Model:
 *   $db = Database::getInstance();
 *   $db->query('SELECT * FROM contactos');
 * ============================================================
 */

class Database
{
    // Única instancia de la clase (Singleton)
    private static ?Database $instance = null;

    // Conexión PDO
    private PDO $connection;

    // ─────────────────────────────────────────────────────────
    /**
     * Constructor privado — nadie puede hacer new Database()
     * desde afuera. Solo getInstance() puede crearlo.
     */
    private function __construct()
    {
        try {
            $dsn = 'sqlsrv:Server=' . DB_SERVER . ';Database=' . DB_NAME;

            $this->connection = new PDO($dsn, DB_USER, DB_PASS, [
                // Lanza excepciones en errores
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                // Retorna arrays asociativos por defecto
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);

        } catch (PDOException $e) {
            die('<h3>❌ Error de conexión a SQL Server: '
                . $e->getMessage() . '</h3>');
        }
    }

    // ─────────────────────────────────────────────────────────
    /**
     * Retorna la única instancia de Database.
     * Si no existe, la crea.
     */
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    // ─────────────────────────────────────────────────────────
    /**
     * Ejecuta una consulta SQL con parámetros.
     *
     * Uso:
     *   $db->query('SELECT * FROM contactos WHERE id = ?', [5]);
     *
     * @param  string $sql     Consulta SQL con placeholders ?
     * @param  array  $params  Valores para los placeholders
     * @return PDOStatement
     */
    public function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    // ─────────────────────────────────────────────────────────
    /**
     * Retorna el ID del último registro insertado.
     */
    public function lastInsertId(): string
    {
        return $this->connection->lastInsertId();
    }

    // ─────────────────────────────────────────────────────────
    /**
     * Inicia una transacción.
     */
    public function beginTransaction(): void
    {
        $this->connection->beginTransaction();
    }

    /**
     * Confirma una transacción.
     */
    public function commit(): void
    {
        $this->connection->commit();
    }

    /**
     * Revierte una transacción.
     */
    public function rollBack(): void
    {
        $this->connection->rollBack();
    }
}
?>