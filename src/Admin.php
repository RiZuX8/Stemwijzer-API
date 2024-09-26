<?php

class Admin
{
    private $conn;
    private $table = 'admins';

    public $adminID;
    public $partyID;
    public $email;
    public $password;

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
        $query = "SELECT * FROM " . $this->table . " WHERE adminID = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function add()
    {
        $query = "INSERT INTO " . $this->table . " (partyID, email, password) VALUES (:partyID, :email, :password)";
        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $this->partyID = htmlspecialchars(strip_tags($this->partyID));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = password_hash($this->password, PASSWORD_DEFAULT); // Hash the password

        // Bind parameters
        $stmt->bindParam(":partyID", $this->partyID);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);

        if ($stmt->execute()) {
            $this->adminID = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function update()
    {
        $query = "UPDATE " . $this->table . " SET partyID = :partyID, email = :email WHERE adminID = :id";
        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $this->adminID = htmlspecialchars(strip_tags($this->adminID));
        $this->partyID = htmlspecialchars(strip_tags($this->partyID));
        $this->email = htmlspecialchars(strip_tags($this->email));

        // Bind parameters
        $stmt->bindParam(":id", $this->adminID);
        $stmt->bindParam(":partyID", $this->partyID);
        $stmt->bindParam(":email", $this->email);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete($id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE adminID = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function updatePassword($id, $newPassword)
    {
        $query = "UPDATE " . $this->table . " SET password = :password WHERE adminID = :id";
        $stmt = $this->conn->prepare($query);

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":password", $hashedPassword);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function checkPassword($email, $password)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($password, $admin['password'])) {
            return $admin;
        }

        return false;
    }
}