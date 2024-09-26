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
