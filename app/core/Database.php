<?php
/**
 * PDO Database Class
 * Connects to the database and provides helpers for safe, prepared queries.
 */
class Database {
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;

    private $dbh;
    private $stmt;
    private $error;

    public function __construct() {
        // Set DSN
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname . ';charset=utf8mb4';
        $options = [
            PDO::ATTR_PERSISTENT => false, // Persistent connections not supported on shared hosts (InfinityFree)
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
        ];

        // Create PDO instance
        try {
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);

            // Sync database connection timezone with PHP timezone
            $now = new DateTime('now', new DateTimeZone(date_default_timezone_get()));
            $offset = $now->getOffset();
            $hours = intval($offset / 3600);
            $minutes = abs(intval(($offset % 3600) / 60));
            $mysql_offset = sprintf('%+03d:%02d', $hours, $minutes);
            $this->dbh->exec("SET time_zone = '{$mysql_offset}'");
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            throw $e;
        }
    }

    /**
     * Prepare statement with SQL query
     */
    public function query($sql) {
        $this->stmt = $this->dbh->prepare($sql);
    }

    /**
     * Bind variables to prepared parameters
     */
    public function bind($param, $value, $type = null) {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
    }

    /**
     * Execute the prepared statement
     */
    public function execute() {
        return $this->stmt->execute();
    }

    /**
     * Get result set as an array of objects
     */
    public function resultSet() {
        $this->execute();
        return $this->stmt->fetchAll();
    }

    /**
     * Get single record as an object
     */
    public function single() {
        $this->execute();
        return $this->stmt->fetch();
    }

    /**
     * Get row count of last operation
     */
    public function rowCount() {
        return $this->stmt->rowCount();
    }

    /**
     * Get the ID of the last inserted row
     */
    public function lastInsertId() {
        return $this->dbh->lastInsertId();
    }
}
