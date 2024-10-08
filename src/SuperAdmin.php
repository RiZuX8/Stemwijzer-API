<?php

class SuperAdmin
{
    private $conn;
    private $table = 'sw_superAdmins';

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
        $superAdmins = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($superAdmins) {
            return [
                'status' => 200,
                'data' => $superAdmins
            ];
        } else {
            return [
                'status' => 404,
                'message' => 'No SuperAdmins found'
            ];
        }
    }

    public function getById($id)
    {
        $query = "SELECT superAdminID, email FROM " . $this->table . " WHERE superAdminID = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $superAdmin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($superAdmin) {
            return [
                'status' => 200,
                'data' => $superAdmin
            ];
        } else {
            return [
                'status' => 404,
                'message' => 'SuperAdmin not found'
            ];
        }
    }

    public function getByEmail($email)
    {
        $query = "SELECT superAdminID, email FROM " . $this->table . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        $superAdmin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($superAdmin) {
            return [
                'status' => 200,
                'data' => $superAdmin
            ];
        } else {
            return [
                'status' => 404,
                'message' => 'SuperAdmin not found'
            ];
        }
    }

    public function add()
    {
        $query = "INSERT INTO " . $this->table . " (email, password) VALUES (:email, :password)";
        $stmt = $this->conn->prepare($query);

        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);

        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);

        if ($stmt->execute()) {
            $this->superAdminID = $this->conn->lastInsertId();
            return [
                'status' => 201,
                'message' => 'SuperAdmin created',
                'id' => $this->superAdminID
            ];
        }
        return [
            'status' => 500,
            'message' => 'Failed to create SuperAdmin'
        ];
    }

    public function update()
    {
        $query = "UPDATE " . $this->table . " SET email = :email WHERE superAdminID = :id";
        $stmt = $this->conn->prepare($query);

        $this->superAdminID = htmlspecialchars(strip_tags($this->superAdminID));
        $this->email = htmlspecialchars(strip_tags($this->email));

        $stmt->bindParam(":id", $this->superAdminID);
        $stmt->bindParam(":email", $this->email);

        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                return [
                    'status' => 200,
                    'message' => 'SuperAdmin updated'
                ];
            } else {
                return [
                    'status' => 404,
                    'message' => 'SuperAdmin not found or no changes made'
                ];
            }
        }
        return [
            'status' => 500,
            'message' => 'Failed to update SuperAdmin'
        ];
    }

    public function delete($id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE superAdminID = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);

        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                return [
                    'status' => 204,
                    'message' => 'SuperAdmin deleted'
                ];
            } else {
                return [
                    'status' => 404,
                    'message' => 'SuperAdmin not found'
                ];
            }
        }
        return [
            'status' => 500,
            'message' => 'Failed to delete SuperAdmin'
        ];
    }

    public function updatePassword($id, $newPassword)
    {
        $query = "UPDATE " . $this->table . " SET password = :password WHERE superAdminID = :id";
        $stmt = $this->conn->prepare($query);

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":password", $hashedPassword);

        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                return [
                    'status' => 200,
                    'message' => 'Password updated'
                ];
            } else {
                return [
                    'status' => 404,
                    'message' => 'SuperAdmin not found or no changes made'
                ];
            }
        }
        return [
            'status' => 500,
            'message' => 'Failed to update password'
        ];
    }

    public function login($email, $password)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        $superAdmin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($superAdmin && password_verify($password, $superAdmin['password'])) {
            return [
                'status' => 200,
                'message' => 'Login successful',
                'superAdmin' => [
                    'id' => $superAdmin['superAdminID'],
                    'email' => $superAdmin['email']
                ]
            ];
        }

        return [
            'status' => 401,
            'message' => 'Invalid credentials'
        ];
    }
}