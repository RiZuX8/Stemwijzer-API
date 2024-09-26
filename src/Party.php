<?php

class Party
{
    private $conn;
    private $table = 'parties';

    public $partyID;
    public $name;
    public $image;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function getAll()
    {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE partyID = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function add()
    {
        $query = "INSERT INTO " . $this->table . " (name, image) VALUES (:name, :image)";
        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->image = htmlspecialchars(strip_tags($this->image));

        // Bind parameters
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":image", $this->image);

        if ($stmt->execute()) {
            $this->partyID = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function update()
    {
        $query = "UPDATE " . $this->table . " SET name = :name, image = :image WHERE partyID = :id";
        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $this->partyID = htmlspecialchars(strip_tags($this->partyID));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->image = htmlspecialchars(strip_tags($this->image));

        // Bind parameters
        $stmt->bindParam(":id", $this->partyID);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":image", $this->image);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete($id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE partyID = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}