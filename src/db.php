<?php
class db
{
    public $conn;
    private $host = "ohzoomer.nl";
    private $db_name = "ohzoomer_school";
    private $username = "ohzoomer_school";
    private $password = "u2wohstCY";

    function __construct()
    {
        $this->conn = $this->getConnection();
    }

    public function getConnection()
    {
        static $mycon;
        if (!isset($mycon)) {
            try {
                $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name;
                $mycon = new PDO($dsn, $this->username, $this->password);
                $mycon->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $exception) {
                error_log("Connection error: " . $exception->getMessage());
                echo "Connection error: " . $exception->getMessage();
            }
        }
        return $mycon;
    }
}
?>
