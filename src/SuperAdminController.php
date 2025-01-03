<?php
require_once 'SuperAdmin.php';
require_once 'db.php';

class SuperAdminController
{
    private static $db;

    public static function handleRequest($method, $pathParts)
    {
        self::$db = new db();

        if (self::$db->conn === null) {
            self::sendResponse(500, ["message" => "Failed to connect to the database"]);
            return;
        }

        if ($pathParts[0] === 'superadmins') {
            switch ($method) {
                case 'GET':
                    if (isset($pathParts[1])) {
                        if ($pathParts[1] === 'id') {
                            self::getSuperAdminById($pathParts[2]);
                        } else if ($pathParts[1] === 'email') {
                            self::getSuperAdminByEmail($pathParts[2]);
                        } else {
                            self::sendResponse(404, ["message" => "Not Found"]);
                        }
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
            $result = $superAdmin->getAll();
            if ($result['status'] === 200) {
                self::sendResponse(200, $result);
            } else {
                self::sendResponse($result['status'], ["message" => $result['message']]);
            }
        } catch (PDOException $e) {
            self::sendResponse(500, ["message" => "Database error: " . $e->getMessage()]);
        }
    }

    private static function getSuperAdminById($id)
    {
        try {
            $superAdmin = new SuperAdmin(self::$db->conn);
            $result = $superAdmin->getById($id);
            if ($result['status'] === 200) {
                self::sendResponse(200, $result);
            } else {
                self::sendResponse($result['status'], ["message" => $result['message']]);
            }
        } catch (PDOException $e) {
            self::sendResponse(500, ["message" => "Database error: " . $e->getMessage()]);
        }
    }

    private static function getSuperAdminByEmail($email)
    {
        try {
            $superAdmin = new SuperAdmin(self::$db->conn);
            $result = $superAdmin->getByEmail($email);
            if ($result['status'] === 200) {
                self::sendResponse(200, $result);
            } else {
                self::sendResponse($result['status'], ["message" => $result['message']]);
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

                if ($superAdmin->add()['status'] === 201) {
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
                $result = $superAdmin->update();

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

    private static function deleteSuperAdmin($id)
    {
        try {
            $superAdmin = new SuperAdmin(self::$db->conn);
            $result = $superAdmin->delete($id);
            if ($result['status'] === 204) {
                self::sendResponse($result['status'], null); // 204 means "No Content"
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
                $result = $superAdmin->login($input['email'], $input['password']);
                if ($result['status'] === 200) {
                    self::sendResponse($result['status'], [
                        "message" => $result['message'],
                        "superAdmin" => [
                            "id" => $result['superAdmin']['id'],
                            "email" => $result['superAdmin']['email']
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