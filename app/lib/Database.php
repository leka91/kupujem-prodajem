<?php

class Database 
{
    private $host   = DB_HOST;
    private $user   = DB_USER;
    private $pass   = DB_PASS;
    private $dbname = DB_NAME;

    private static $instance = null;
    private $conn;

    public function __construct() 
    {
        $this->conn = new mysqli(
            $this->host,
            $this->user,
            $this->pass,
            $this->dbname
        );

        if ($this->conn->connect_error) {
            responseError('DB_error: ' . $this->conn->connect_error);
        }
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new Database;
        }

        return self::$instance;
    }

    public function getConnection()
    {
        return $this->conn;
    }
}