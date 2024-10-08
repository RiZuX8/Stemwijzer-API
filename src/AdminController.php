<?php
require_once 'Admin.php';
require_once 'db.php';

class AdminController
{
    private static $db;

    public static function handleRequest($method, $pathParts)
    {
        self::$db = new db();

        if (self::$db->conn === null) {
            self::sendResponse(500, ["message" => "Failed to connect to the database"]);
            return;
        }

        if ($pathParts[0] === 'admins') {
            switch ($method) {
                case 'GET':
                    if ($pathParts[1] === 'id') {
                        self::getAdminById($pathParts[2]);
                    } else if ($pathParts[1] === 'email') {
                        self::getAdminByEmail($pathParts[2]);
                    } else {
                        self::getAllAdmins();
                    }
                    break;
                case 'POST':
                    if (isset($pathParts[1]) && $pathParts[1] === 'login') {
                        self::loginAdmin();
                    } else {
                        self::addAdmin();
                    }
                    break;
                case 'PUT':
                    if (isset($pathParts[1])) {
                        self::updateAdmin($pathParts[1]);
                    }
                    break;
                case 'DELETE':
                    if (isset($pathParts[1])) {
                        self::deleteAdmin($pathParts[1]);
                    }
                    break;
                default:
                    self::sendResponse(405, ["message" => "Method Not Allowed"]);
                    break;
            }
        } else {
            self::sendResponse(404, ["message" => "Not Found"]);
        }
    }

    private static function getAllAdmins()
    {
        try {
            $admin = new Admin(self::$db->conn);
            $result = $admin->getAll();
            if ($result['status'] === 200) {
                self::sendResponse($result['status'], $result['data']);
            } else {
                self::sendResponse($result['status'], ["message" => $result['message']]);
            }
        } catch (PDOException $e) {
            self::sendResponse(500, ["message" => "Database error: " . $e->getMessage()]);
        }
    }

    private static function getAdminById($id)
    {
        try {
            $admin = new Admin(self::$db->conn);
            $result = $admin->getById($id);
            if ($result['status'] === 200) {
                self::sendResponse($result['status'], $result['data']);
            } else {
                self::sendResponse($result['status'], ["message" => $result['message']]);
            }
        } catch (PDOException $e) {
            self::sendResponse(500, ["message" => "Database error: " . $e->getMessage()]);
        }
    }

    private static function getAdminByEmail($email)
    {
        try {
            $admin = new Admin(self::$db->conn);
            $result = $admin->getByEmail($email);
            if ($result['status'] === 200) {
                self::sendResponse($result['status'], $result['data']);
            } else {
                self::sendResponse($result['status'], ["message" => $result['message']]);
            }
        } catch (PDOException $e) {
            self::sendResponse(500, ["message" => "Database error: " . $e->getMessage()]);
        }
    }

    private static function addAdmin()
    {
        $input = json_decode(file_get_contents("php://input"), true);
        if (self::validateAdminInput($input)) {
            try {
                $admin = new Admin(self::$db->conn);
                $admin->partyID = $input['partyID'];
                $admin->email = $input['email'];
                $admin->password = $input['password'];

                if ($admin->add()['status'] === 201) {
                    self::sendResponse(201, [
                        "message" => "Admin created",
                        "id" => $admin->adminID
                    ]);
                } else {
                    self::sendResponse(500, ["message" => "Failed to create admin"]);
                }
            } catch (PDOException $e) {
                self::sendResponse(500, ["message" => "Database error: " . $e->getMessage()]);
            }
        } else {
            self::sendResponse(400, ["message" => "Invalid input"]);
        }
    }

    private static function updateAdmin($id)
    {
        $input = json_decode(file_get_contents("php://input"), true);
        if (self::validateAdminUpdateInput($input)) {
            try {
                $admin = new Admin(self::$db->conn);
                $admin->adminID = $id;
                $admin->partyID = $input['partyID'];
                $admin->email = $input['email'];

                $result = $admin->$admin->update();
                if ($result) {
                    self::sendResponse($result['status'], ["message" => $result['message']]);
                }
            } catch (PDOException $e) {
                self::sendResponse(500, ["message" => "Database error: " . $e->getMessage()]);
            }
        } else {
            self::sendResponse(400, ["message" => "Invalid input"]);
        }
    }

    private static function deleteAdmin($id)
    {
        try {
            $admin = new Admin(self::$db->conn);
            $result = $admin->delete($id);
            if ($result['status'] === 204) {
                self::sendResponse($result['status'], null); // 204 means "No Content"
            } else {
                self::sendResponse(404, ["message" => "Admin not found"]);
            }
        } catch (PDOException $e) {
            self::sendResponse(500, ["message" => "Database error: " . $e->getMessage()]);
        }
    }

    private static function loginAdmin()
    {
        $input = json_decode(file_get_contents("php://input"), true);
        if (isset($input['email']) && isset($input['password'])) {
            try {
                $admin = new Admin(self::$db->conn);
                $result = $admin->login($input['email'], $input['password']);
                if ($result['status'] === 200) {
                    self::sendResponse($result['status'], [
                        "message" => $result['message'],
                        "admin" => [
                            "id" => $result['admin']['id'],
                            "partyID" => $result['admin']['partyID'],
                            "email" => $result['admin']['email']
                        ]
                    ]);
                } else {
                    self::sendResponse(401, ["message" => "Invalid credentials"]);
                }
            } catch (PDOException $e) {
                self::sendResponse(500, ["message" => "Database error: " . $e->getMessage()]);
            }
        } else {
            self::sendResponse(400, ["message" => "Invalid input"]);
        }
    }

    private static function validateAdminInput($input): bool
    {
        return !empty($input['partyID']) &&
               !empty($input['email']) &&
               !empty($input['password']) &&
               filter_var($input['email'], FILTER_VALIDATE_EMAIL);
    }

    private static function validateAdminUpdateInput($input): bool
    {
        return !empty($input['partyID']) &&
               !empty($input['email']) &&
               filter_var($input['email'], FILTER_VALIDATE_EMAIL);
    }

    private static function sendResponse($statusCode, $data)
    {
        header("Content-Type: application/json");
        http_response_code($statusCode);
        if ($data !== null) {
            echo json_encode($data);
        }
    }
}