<?php
require_once 'Admin.php';
require_once 'db.php';

class AdminController
{
    private static $db;

    public static function handleRequest($method, $pathParts)
    {
        self::$db = new db();

        if ($pathParts[0] === 'admins') {
            switch ($method) {
                case 'GET':
                    if (isset($pathParts[1])) {
                        self::getAdmin($pathParts[1]);
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
            $admins = $admin->getAll();
            self::sendResponse(200, $admins);
        } catch (PDOException $e) {
            self::sendResponse(500, ["message" => "Database error: " . $e->getMessage()]);
        }
    }

    private static function getAdmin($id)
    {
        try {
            $admin = new Admin(self::$db->conn);
            $result = $admin->getById($id);
            if ($result) {
                self::sendResponse(200, $result);
            } else {
                self::sendResponse(404, ["message" => "Admin not found"]);
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

                if ($admin->add()) {
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

                if ($admin->update()) {
                    self::sendResponse(200, ["message" => "Admin updated"]);
                } else {
                    self::sendResponse(404, ["message" => "Admin not found or not updated"]);
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
            if ($admin->delete($id)) {
                self::sendResponse(204, null); // 204 means "No Content"
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
                $result = $admin->checkPassword($input['email'], $input['password']);
                if ($result) {
                    self::sendResponse(200, [
                        "message" => "Login successful",
                        "admin" => [
                            "id" => $result['adminID'],
                            "email" => $result['email'],
                            "partyID" => $result['partyID']
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