<?php
class db
{
    public $conn;
    private $host = "576459.klas4s22.mid-ica.nl";
    private $db_name = "klas4s22_576459";
    private $username = "klas4s22_576459";
    private $password = "0yiNAPaW";

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
