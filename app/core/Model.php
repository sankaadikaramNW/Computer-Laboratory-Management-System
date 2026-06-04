<?php
/**
 * Base Model Class
 * Instantiates the Database PDO wrapper.
 */
class Model {
    protected $db;

    public function __construct() {
        $this->db = new Database();
    }
}
