<?php

class Statement
{
    private $conn;
    private $table = 'sw_statements';

    public $statementID;
    public $name;
    public $description;
    public $image;
    public $xValue;
    public $yValue;
    public $priority;

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
        $query = "SELECT * FROM " . $this->table . " WHERE statementID = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function add()
    {
        $query = "INSERT INTO " . $this->table . " 
                  (name, description, image, xValue, yValue, priority) 
                  VALUES 
                  (:name, :description, :image, :xValue, :yValue, :priority)";

        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->image = htmlspecialchars(strip_tags($this->image));
        $this->xValue = htmlspecialchars(strip_tags($this->xValue));
        $this->yValue = htmlspecialchars(strip_tags($this->yValue));
        $this->priority = htmlspecialchars(strip_tags($this->priority));

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":image", $this->image);
        $stmt->bindParam(":xValue", $this->xValue);
        $stmt->bindParam(":yValue", $this->yValue);
        $stmt->bindParam(":priority", $this->priority);

        if ($stmt->execute()) {
            $this->statementID = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function update()
    {
        $query = "UPDATE " . $this->table . " 
                  SET name = :name, description = :description, image = :image, 
                      xValue = :xValue, yValue = :yValue, priority = :priority 
                  WHERE statementID = :id";

        $stmt = $this->conn->prepare($query);

        $this->statementID = htmlspecialchars(strip_tags($this->statementID));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->image = htmlspecialchars(strip_tags($this->image));
        $this->xValue = htmlspecialchars(strip_tags($this->xValue));
        $this->yValue = htmlspecialchars(strip_tags($this->yValue));
        $this->priority = htmlspecialchars(strip_tags($this->priority));

        $stmt->bindParam(":id", $this->statementID);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":image", $this->image);
        $stmt->bindParam(":xValue", $this->xValue);
        $stmt->bindParam(":yValue", $this->yValue);
        $stmt->bindParam(":priority", $this->priority);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete($id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE statementID = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}