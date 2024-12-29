<?php
class db
{
    public $conn;
    private $host = "ohzoomer.nl";
    private $db_name = "lsibm1cbac_stemwijzer";
    private $username = "lsibm1cbac_stemwijzer";
    private $password = "5bGjXLxZBMEKbCWFKBX9";

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
                $mycon->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $exception) {
                error_log("Connection error: " . $exception->getMessage());
                echo "Connection error: " . $exception->getMessage();
            }
        }
        return $mycon;
    }
}
?>
