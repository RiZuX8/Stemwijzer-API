<?php
require_once 'Party.php';
require_once 'db.php';

class PartyController
{
    private static $db;

    public static function handleRequest($method, $pathParts)
    {
        self::$db = new db();

        if ($pathParts[0] === 'parties') {
            switch ($method) {
                case 'GET':
                    if (isset($pathParts[1])) {
                        self::getParty($pathParts[1]);
                    } else {
                        self::getAllParties();
                    }
                    break;
                case 'POST':
                    self::addParty();
                    break;
                case 'PUT':
                    if (isset($pathParts[1])) {
                        self::updateParty($pathParts[1]);
                    }
                    break;
                case 'DELETE':
                    if (isset($pathParts[1])) {
                        self::deleteParty($pathParts[1]);
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

    private static function getAllParties()
    {
        try {
            $party = new Party(self::$db->conn);
            $parties = $party->getAll();
            self::sendResponse(200, $parties);
        } catch (PDOException $e) {
            self::sendResponse(500, ["message" => "Database error: " . $e->getMessage()]);
        }
    }

    private static function getParty($id)
    {
        try {
            $party = new Party(self::$db->conn);
            $result = $party->getById($id);
            if ($result) {
                self::sendResponse(200, $result);
            } else {
                self::sendResponse(404, ["message" => "Party not found"]);
            }
        } catch (PDOException $e) {
            self::sendResponse(500, ["message" => "Database error: " . $e->getMessage()]);
        }
    }

    private static function addParty()
    {
        $input = json_decode(file_get_contents("php://input"), true);
        if (self::validatePartyInput($input)) {
            try {
                $party = new Party(self::$db->conn);
                $party->name = $input['name'];
                $party->image = $input['image'];

                if ($party->add()) {
                    self::sendResponse(201, [
                        "message" => "Party created",
                        "id" => $party->partyID
                    ]);
                } else {
                    self::sendResponse(500, ["message" => "Failed to create party"]);
                }
            } catch (PDOException $e) {
                self::sendResponse(500, ["message" => "Database error: " . $e->getMessage()]);
            }
        } else {
            self::sendResponse(400, ["message" => "Invalid input"]);
        }
    }

    private static function updateParty($id)
    {
        $input = json_decode(file_get_contents("php://input"), true);
        if (self::validatePartyInput($input)) {
            try {
                $party = new Party(self::$db->conn);
                $party->partyID = $id;
                $party->name = $input['name'];
                $party->image = $input['image'];

                if ($party->update()) {
                    self::sendResponse(200, ["message" => "Party updated"]);
                } else {
                    self::sendResponse(404, ["message" => "Party not found or not updated"]);
                }
            } catch (PDOException $e) {
                self::sendResponse(500, ["message" => "Database error: " . $e->getMessage()]);
            }
        } else {
            self::sendResponse(400, ["message" => "Invalid input"]);
        }
    }

    private static function deleteParty($id)
    {
        try {
            $party = new Party(self::$db->conn);
            if ($party->delete($id)) {
                self::sendResponse(204, null); // 204 means "No Content"
            } else {
                self::sendResponse(404, ["message" => "Party not found"]);
            }
        } catch (PDOException $e) {
            self::sendResponse(500, ["message" => "Database error: " . $e->getMessage()]);
        }
    }

    private static function validatePartyInput($input)
    {
        return isset($input['name']) && !empty($input['name']) &&
               isset($input['image']);
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