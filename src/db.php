<?php
class db
{
    public $conn;
    private $host = "";
    private $db_name = "";
    private $username = "";
    private $password = "";

    function __construct()
    {
        $this->getConnection();
    }

    public function getConnection()
    {
        static $mycon;
        $this->conn = $mycon;
        if (!isset($this->conn)) {
            try {
                $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name;
                $this->conn = new PDO($dsn, $this->username, $this->password);
                // Set PDO error mode to exception
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $exception) {
                echo "Connection error: " . $exception->getMessage();
            }
        }
        return $this->conn;
    }
}
