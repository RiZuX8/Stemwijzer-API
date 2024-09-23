<?php

class Statement
{
    public $statementID;
    public $name;
    public $description;
    public $image;
    public $xValue;
    public $yValue;

    private $conn;
    private $table = "statements";

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }
}
