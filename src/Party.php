<?php

class Party
{
    private $conn;
    private $table = 'sw_parties';

    public $partyID;
    public $name;
    public $description;
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
        $parties = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($parties) {
            return [
                'status' => 200,
                'data' => $parties
            ];
        } else {
            return [
                'status' => 404,
                'message' => 'No parties found'
            ];
        }
    }

    public function getById($id)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE partyID = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $party = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($party) {
            return [
                'status' => 200,
                'data' => $party
            ];
        } else {
            return [
                'status' => 404,
                'message' => 'Party not found'
            ];
        }
    }

    public function add()
    {
        $query = "INSERT INTO " . $this->table . " (name, description, image) VALUES (:name, :description, :image)";
        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->image = htmlspecialchars(strip_tags($this->image));

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":image", $this->image);

        if ($stmt->execute()) {
            $this->partyID = $this->conn->lastInsertId();
            return [
                'status' => 201,
                'message' => 'Party created',
                'id' => $this->partyID
            ];
        }
        return [
            'status' => 500,
            'message' => 'Failed to create party'
        ];
    }

    public function update()
    {
        $query = "UPDATE " . $this->table . " SET name = :name, description = :description, image = :image WHERE partyID = :id";
        $stmt = $this->conn->prepare($query);

        $this->partyID = htmlspecialchars(strip_tags($this->partyID));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->image = htmlspecialchars(strip_tags($this->image));

        $stmt->bindParam(":id", $this->partyID);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":image", $this->image);

        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                return [
                    'status' => 200,
                    'message' => 'Party updated'
                ];
            } else {
                return [
                    'status' => 404,
                    'message' => 'Party not found or no changes made'
                ];
            }
        }
        return [
            'status' => 500,
            'message' => 'Failed to update party'
        ];
    }

    public function delete($id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE partyID = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);

        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                return [
                    'status' => 204,
                    'message' => 'Party deleted'
                ];
            } else {
                return [
                    'status' => 404,
                    'message' => 'Party not found'
                ];
            }
        }
        return [
            'status' => 500,
            'message' => 'Failed to delete party'
        ];
    }
}