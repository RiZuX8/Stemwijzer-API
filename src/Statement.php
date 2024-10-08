<?php

class Statement
{
    private $conn;
    private $table = 'sw_statements';

    public $statementID;
    public $name;
    public $description;
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
        $statements = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($statements) {
            return [
                'status' => 200,
                'data' => $statements
            ];
        } else {
            return [
                'status' => 404,
                'message' => 'No statements found'
            ];
        }
    }

    public function getById($id)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE statementID = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $statement = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($statement) {
            return [
                'status' => 200,
                'data' => $statement
            ];
        } else {
            return [
                'status' => 404,
                'message' => 'Statement not found'
            ];
        }
    }

    public function add()
    {
        $query = "INSERT INTO " . $this->table . " 
                  (name, description, xValue, yValue, priority) 
                  VALUES 
                  (:name, :description, :xValue, :yValue, :priority)";

        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->xValue = htmlspecialchars(strip_tags($this->xValue));
        $this->yValue = htmlspecialchars(strip_tags($this->yValue));
        $this->priority = htmlspecialchars(strip_tags($this->priority));

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":xValue", $this->xValue);
        $stmt->bindParam(":yValue", $this->yValue);
        $stmt->bindParam(":priority", $this->priority);

        if ($stmt->execute()) {
            $this->statementID = $this->conn->lastInsertId();
            return [
                'status' => 201,
                'message' => 'Statement created',
                'id' => $this->statementID
            ];
        }
        return [
            'status' => 500,
            'message' => 'Failed to create statement'
        ];
    }

    public function update()
    {
        $query = "UPDATE " . $this->table . " 
                  SET name = :name, description = :description, 
                      xValue = :xValue, yValue = :yValue, priority = :priority 
                  WHERE statementID = :id";

        $stmt = $this->conn->prepare($query);

        $this->statementID = htmlspecialchars(strip_tags($this->statementID));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->xValue = htmlspecialchars(strip_tags($this->xValue));
        $this->yValue = htmlspecialchars(strip_tags($this->yValue));
        $this->priority = htmlspecialchars(strip_tags($this->priority));

        $stmt->bindParam(":id", $this->statementID);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":xValue", $this->xValue);
        $stmt->bindParam(":yValue", $this->yValue);
        $stmt->bindParam(":priority", $this->priority);

        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                return [
                    'status' => 200,
                    'message' => 'Statement updated'
                ];
            } else {
                return [
                    'status' => 404,
                    'message' => 'Statement not found'
                ];
            }
        }
        return [
            'status' => 500,
            'message' => 'Failed to update statement'
        ];
    }

    public function delete($id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE statementID = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);

        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                return [
                    'status' => 204,
                    'message' => 'Statement deleted'
                ];
            } else {
                return [
                    'status' => 404,
                    'message' => 'Statement not found'
                ];
            }
        }
        return [
            'status' => 500,
            'message' => 'Failed to delete statement'
        ];
    }
}