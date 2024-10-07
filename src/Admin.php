<?php

class Admin
{
    private $conn;
    private $table = 'sw_admins';

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
        $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($admins) {
            return [
                'status' => 200,
                'data' => $admins
            ];
        } else {
            return [
                'status' => 404,
                'message' => 'No admins found'
            ];
        }
    }

    public function getById($id)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE adminID = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin) {
            return [
                'status' => 200,
                'data' => $admin
            ];
        } else {
            return [
                'status' => 404,
                'message' => 'Admin not found'
            ];
        }
    }

    public function getByEmail($email)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin) {
            return [
                'status' => 200,
                'data' => $admin
            ];
        } else {
            return [
                'status' => 404,
                'message' => 'Admin not found'
            ];
        }
    }

    public function add()
    {
        $query = "INSERT INTO " . $this->table . " (partyID, email, password) VALUES (:partyID, :email, :password)";
        $stmt = $this->conn->prepare($query);

        $this->partyID = htmlspecialchars(strip_tags($this->partyID));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);

        $stmt->bindParam(":partyID", $this->partyID);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);

        if ($stmt->execute()) {
            $this->adminID = $this->conn->lastInsertId();
            return [
                'status' => 201,
                'message' => 'Admin created',
                'id' => $this->adminID
            ];
        }
        return [
            'status' => 500,
            'message' => 'Failed to create admin'
        ];
    }

    public function update()
    {
        $query = "UPDATE " . $this->table . " SET partyID = :partyID, email = :email WHERE adminID = :id";
        $stmt = $this->conn->prepare($query);

        $this->adminID = htmlspecialchars(strip_tags($this->adminID));
        $this->partyID = htmlspecialchars(strip_tags($this->partyID));
        $this->email = htmlspecialchars(strip_tags($this->email));

        $stmt->bindParam(":id", $this->adminID);
        $stmt->bindParam(":partyID", $this->partyID);
        $stmt->bindParam(":email", $this->email);

        if ($stmt->execute()) {
            return [
                'status' => 200,
                'message' => 'Admin updated'
            ];
        }
        return [
            'status' => 404,
            'message' => 'Admin not found or not updated'
        ];
    }

    public function delete($id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE adminID = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);

        if ($stmt->execute()) {
            return [
                'status' => 204,
                'message' => 'Admin deleted'
            ];
        }
        return [
            'status' => 404,
            'message' => 'Admin not found'
        ];
    }

    public function updatePassword($id, $newPassword)
    {
        $query = "UPDATE " . $this->table . " SET password = :password WHERE adminID = :id";
        $stmt = $this->conn->prepare($query);

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":password", $hashedPassword);

        if ($stmt->execute()) {
            return [
                'status' => 200,
                'message' => 'Password updated'
            ];
        }
        return [
            'status' => 404,
            'message' => 'Admin not found or password not updated'
        ];
    }

    public function login($email, $password)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($password, $admin['password'])) {
            return [
                'status' => 200,
                'message' => 'Login successful',
                'admin' => [
                    'id' => $admin['adminID'],
                    'email' => $admin['email'],
                    'partyID' => $admin['partyID']
                ]
            ];
        }

        return [
            'status' => 401,
            'message' => 'Invalid credentials'
        ];
    }
}