<?php

class SuperAdmin
{
    private $conn;
    private $table = 'superAdmins';

    public $superAdminID;
    public $email;
    public $password;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function getAll()
    {
        $query = "SELECT superAdminID, email FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $query = "SELECT superAdminID, email FROM " . $this->table . " WHERE superAdminID = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function add()
    {
        $query = "INSERT INTO " . $this->table . " (email, password) VALUES (:email, :password)";
        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = password_hash($this->password, PASSWORD_DEFAULT); // Hash the password

        // Bind parameters
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);

        if ($stmt->execute()) {
            $this->superAdminID = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function update()
    {
        $query = "UPDATE " . $this->table . " SET email = :email WHERE superAdminID = :id";
        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $this->superAdminID = htmlspecialchars(strip_tags($this->superAdminID));
        $this->email = htmlspecialchars(strip_tags($this->email));

        // Bind parameters
        $stmt->bindParam(":id", $this->superAdminID);
        $stmt->bindParam(":email", $this->email);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete($id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE superAdminID = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function updatePassword($id, $newPassword)
    {
        $query = "UPDATE " . $this->table . " SET password = :password WHERE superAdminID = :id";
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

        $superAdmin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($superAdmin && password_verify($password, $superAdmin['password'])) {
            return $superAdmin;
        }

        return false;
    }
}