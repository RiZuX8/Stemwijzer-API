<?php
require_once 'SuperAdmin.php';
require_once 'db.php';

class SuperAdminController
{
    private static $db;

    public static function handleRequest($method, $pathParts)
    {
        self::$db = new db();

        if ($pathParts[0] === 'superadmins') {
            switch ($method) {
                case 'GET':
                    if (isset($pathParts[1])) {
                        self::getSuperAdmin($pathParts[1]);
                    } else {
                        self::getAllSuperAdmins();
                    }
                    break;
                case 'POST':
                    if (isset($pathParts[1]) && $pathParts[1] === 'login') {
                        self::loginSuperAdmin();
                    } else {
                        self::addSuperAdmin();
                    }
                    break;
                case 'PUT':
                    if (isset($pathParts[1])) {
                        self::updateSuperAdmin($pathParts[1]);
                    }
                    break;
                case 'DELETE':
                    if (isset($pathParts[1])) {
                        self::deleteSuperAdmin($pathParts[1]);
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

    private static function getAllSuperAdmins()
    {
        try {
            $superAdmin = new SuperAdmin(self::$db->conn);
            $superAdmins = $superAdmin->getAll();
            self::sendResponse(200, $superAdmins);
        } catch (PDOException $e) {
            self::sendResponse(500, ["message" => "Database error: " . $e->getMessage()]);
        }
    }

    private static function getSuperAdmin($id)
    {
        try {
            $superAdmin = new SuperAdmin(self::$db->conn);
            $result = $superAdmin->getById($id);
            if ($result) {
                self::sendResponse(200, $result);
            } else {
                self::sendResponse(404, ["message" => "SuperAdmin not found"]);
            }
        } catch (PDOException $e) {
            self::sendResponse(500, ["message" => "Database error: " . $e->getMessage()]);
        }
    }

    private static function addSuperAdmin()
    {
        $input = json_decode(file_get_contents("php://input"), true);
        if (self::validateSuperAdminInput($input)) {
            try {
                $superAdmin = new SuperAdmin(self::$db->conn);
                $superAdmin->email = $input['email'];
                $superAdmin->password = $input['password'];

                if ($superAdmin->add()) {
                    self::sendResponse(201, [
                        "message" => "SuperAdmin created",
                        "id" => $superAdmin->superAdminID
                    ]);
                } else {
                    self::sendResponse(500, ["message" => "Failed to create SuperAdmin"]);
                }
            } catch (PDOException $e) {
                self::sendResponse(500, ["message" => "Database error: " . $e->getMessage()]);
            }
        } else {
            self::sendResponse(400, ["message" => "Invalid input"]);
        }
    }

    private static function updateSuperAdmin($id)
    {
        $input = json_decode(file_get_contents("php://input"), true);
        if (self::validateSuperAdminUpdateInput($input)) {
            try {
                $superAdmin = new SuperAdmin(self::$db->conn);
                $superAdmin->superAdminID = $id;
                $superAdmin->email = $input['email'];

                if ($superAdmin->update()) {
                    self::sendResponse(200, ["message" => "SuperAdmin updated"]);
                } else {
                    self::sendResponse(404, ["message" => "SuperAdmin not found or not updated"]);
                }
            } catch (PDOException $e) {
                self::sendResponse(500, ["message" => "Database error: " . $e->getMessage()]);
            }
        } else {
            self::sendResponse(400, ["message" => "Invalid input"]);
        }
    }

    private static function deleteSuperAdmin($id)
    {
        try {
            $superAdmin = new SuperAdmin(self::$db->conn);
            if ($superAdmin->delete($id)) {
                self::sendResponse(204, null); // 204 means "No Content"
            } else {
                self::sendResponse(404, ["message" => "SuperAdmin not found"]);
            }
        } catch (PDOException $e) {
            self::sendResponse(500, ["message" => "Database error: " . $e->getMessage()]);
        }
    }

    private static function loginSuperAdmin()
    {
        $input = json_decode(file_get_contents("php://input"), true);
        if (isset($input['email']) && isset($input['password'])) {
            try {
                $superAdmin = new SuperAdmin(self::$db->conn);
                $result = $superAdmin->checkPassword($input['email'], $input['password']);
                if ($result) {
                    self::sendResponse(200, [
                        "message" => "Login successful",
                        "superAdmin" => [
                            "id" => $result['superAdminID'],
                            "email" => $result['email']
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

    private static function validateSuperAdminInput($input): bool
    {
        return !empty($input['email']) &&
               !empty($input['password']) &&
               filter_var($input['email'], FILTER_VALIDATE_EMAIL);
    }

    private static function validateSuperAdminUpdateInput($input): bool
    {
        return !empty($input['email']) &&
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