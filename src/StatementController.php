<?php
require_once 'Statement.php';

class StatementController
{
    public static function handleRequest($method, $pathParts)
    {
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

    private static function sendResponse($statusCode, $data)
    {
        header("Content-Type: application/json");
        http_response_code($statusCode);
        if ($data !== null) {
            echo json_encode($data);
        }
    }
}