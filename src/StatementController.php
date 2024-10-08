<?php
require_once 'Statement.php';
require_once 'db.php';

class StatementController
{
    private static $db;

    public static function handleRequest($method, $pathParts)
    {
        self::$db = new db();

        if (self::$db->conn === null) {
            self::sendResponse(500, ["message" => "Failed to connect to the database"]);
            return;
        }

        if ($pathParts[0] === 'statements') {
            switch ($method) {
                case 'GET':
                    if (isset($pathParts[1])) {
                        self::getStatement($pathParts[1]);
                    } else {
                        self::getAllStatements();
                    }
                    break;
                case 'POST':
                    self::addStatement();
                    break;
                case 'PUT':
                    if (isset($pathParts[1])) {
                        self::updateStatement($pathParts[1]);
                    }
                    break;
                case 'DELETE':
                    if (isset($pathParts[1])) {
                        self::deleteStatement($pathParts[1]);
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

    private static function getAllStatements()
    {
        try {
            $statement = new Statement(self::$db->conn);
            $result = $statement->getAll();
            if ($result['status'] === 200) {
                self::sendResponse($result['status'], $result['data']);
            } else {
                self::sendResponse($result['status'], ["message" => $result['message']]);
            }
        } catch (PDOException $e) {
            self::sendResponse(500, ["message" => "Database error: " . $e->getMessage()]);
        }
    }

    private static function getStatement($id)
    {
        try {
            $statement = new Statement(self::$db->conn);
            $result = $statement->getById($id);
            if ($result['status'] === 200) {
                self::sendResponse($result['status'], $result['data']);
            } else {
                self::sendResponse($result['status'], ["message" => $result['message']]);
            }
        } catch (PDOException $e) {
            self::sendResponse(500, ["message" => "Database error: " . $e->getMessage()]);
        }
    }

    private static function addStatement()
    {
        $input = json_decode(file_get_contents("php://input"), true);
        if (self::validateStatementInput($input)) {
            try {
                $statement = new Statement(self::$db->conn);
                $statement->name = $input['name'];
                $statement->description = $input['description'];
                $statement->xValue = $input['xValue'];
                $statement->yValue = $input['yValue'];
                $statement->priority = $input['priority'];

                $result = $statement->add();
                if ($result['status'] === 201) {
                    self::sendResponse(201, [
                        "message" => "Statement created",
                        "id" => $statement->statementID
                    ]);
                } else {
                    self::sendResponse($result['status'], ["message" => $result['message']]);
                }
            } catch (PDOException $e) {
                self::sendResponse(500, ["message" => "Database error: " . $e->getMessage()]);
            }
        } else {
            self::sendResponse(400, ["message" => "Invalid input"]);
        }
    }

    private static function updateStatement($id)
    {
        $input = json_decode(file_get_contents("php://input"), true);
        if (self::validateStatementInput($input)) {
            try {
                $statement = new Statement(self::$db->conn);
                $statement->statementID = $id;
                $statement->name = $input['name'];
                $statement->description = $input['description'];
                $statement->xValue = $input['xValue'];
                $statement->yValue = $input['yValue'];
                $statement->priority = $input['priority'];

                $result = $statement->update();
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

    private static function deleteStatement($id)
    {
        try {
            $statement = new Statement(self::$db->conn);
            $result = $statement->delete($id);
            if ($result['status'] === 204) {
                self::sendResponse($result['status'], null); // 204 means "No Content"
            } else {
                self::sendResponse($result['status'], ["message" => $result['message']]);
            }
        } catch (PDOException $e) {
            self::sendResponse(500, ["message" => "Database error: " . $e->getMessage()]);
        }
    }

    private static function validateStatementInput($input): bool
    {
        return !empty($input['name']) &&
               isset($input['description']) &&
               isset($input['xValue']) && is_numeric($input['xValue']) &&
               isset($input['yValue']) && is_numeric($input['yValue']) &&
               isset($input['priority']) && is_numeric($input['priority']);
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