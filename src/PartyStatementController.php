<?php
require_once 'PartyStatement.php';
require_once 'db.php';

class PartyStatementController
{
    private static $db;

    public static function handleRequest($method, $pathParts)
    {
        self::$db = new db();

        if (self::$db->conn === null) {
            self::sendResponse(500, ["message" => "Failed to connect to the database"]);
            return;
        }

        if ($pathParts[0] === 'party-statements') {
            switch ($method) {
                case 'GET':
                    if (isset($pathParts[1]) && $pathParts[1] === 'party') {
                        self::getByParty($pathParts[2]);
                    } elseif (isset($pathParts[1]) && $pathParts[1] === 'statement') {
                        self::getByStatement($pathParts[2]);
                    } else {
                        self::getAllPartyStatements();
                    }
                    break;
                case 'POST':
                    self::addPartyStatement();
                    break;
                case 'PUT':
                    self::updatePartyStatement();
                    break;
                case 'DELETE':
                    self::deletePartyStatement();
                    break;
                default:
                    self::sendResponse(405, ["message" => "Method Not Allowed"]);
                    break;
            }
        } else {
            self::sendResponse(404, ["message" => "Not Found"]);
        }
    }

    private static function getAllPartyStatements()
    {
        try {
            $partyStatement = new PartyStatement(self::$db->conn);
            $partyStatements = $partyStatement->getAll();
            self::sendResponse(200, $partyStatements);
        } catch (PDOException $e) {
            self::sendResponse(500, ["message" => "Database error: " . $e->getMessage()]);
        }
    }

    private static function getByParty($partyID)
    {
        try {
            $partyStatement = new PartyStatement(self::$db->conn);
            $results = $partyStatement->getBypartyID($partyID);
            self::sendResponse(200, $results);
        } catch (PDOException $e) {
            self::sendResponse(500, ["message" => "Database error: " . $e->getMessage()]);
        }
    }

    private static function getByStatement($statementId)
    {
        try {
            $partyStatement = new PartyStatement(self::$db->conn);
            $results = $partyStatement->getByStatementId($statementId);
            self::sendResponse(200, $results);
        } catch (PDOException $e) {
            self::sendResponse(500, ["message" => "Database error: " . $e->getMessage()]);
        }
    }

    private static function addPartyStatement()
    {
        $input = json_decode(file_get_contents("php://input"), true);
        $input['answerValue'] = strtolower($input['answerValue']);
        if (self::validatePartyStatementInput($input)) {
            try {
                $partyStatement = new PartyStatement(self::$db->conn);
                $partyStatement->partyID = $input['partyID'];
                $partyStatement->statementID = $input['statementID'];
                $partyStatement->answerValue = $input['answerValue'];

                if ($partyStatement->add()) {
                    self::sendResponse(201, ["message" => "PartyStatement created"]);
                } else {
                    self::sendResponse(500, ["message" => "Failed to create PartyStatement"]);
                }
            } catch (PDOException $e) {
                self::sendResponse(500, ["message" => "Database error: " . $e->getMessage()]);
            }
        } else {
            self::sendResponse(400, ["message" => "Invalid input"]);
        }
    }

    private static function updatePartyStatement()
    {
        $input = json_decode(file_get_contents("php://input"), true);
        if (self::validatePartyStatementInput($input)) {
            try {
                $partyStatement = new PartyStatement(self::$db->conn);
                $partyStatement->partyID = $input['partyID'];
                $partyStatement->statementID = $input['statementID'];
                $partyStatement->answerValue = $input['answerValue'];

                if ($partyStatement->update()) {
                    self::sendResponse(200, ["message" => "PartyStatement updated"]);
                } else {
                    self::sendResponse(404, ["message" => "PartyStatement not found or not updated"]);
                }
            } catch (PDOException $e) {
                self::sendResponse(500, ["message" => "Database error: " . $e->getMessage()]);
            }
        } else {
            self::sendResponse(400, ["message" => "Invalid input"]);
        }
    }

    private static function deletePartyStatement()
    {
        $input = json_decode(file_get_contents("php://input"), true);
        if (isset($input['partyID']) && isset($input['statementID'])) {
            try {
                $partyStatement = new PartyStatement(self::$db->conn);
                $partyStatement->partyID = $input['partyID'];
                $partyStatement->statementID = $input['statementID'];

                if ($partyStatement->delete()) {
                    self::sendResponse(204, null); // 204 means "No Content"
                } else {
                    self::sendResponse(404, ["message" => "PartyStatement not found"]);
                }
            } catch (PDOException $e) {
                self::sendResponse(500, ["message" => "Database error: " . $e->getMessage()]);
            }
        } else {
            self::sendResponse(400, ["message" => "Invalid input"]);
        }
    }

    private static function validatePartyStatementInput($input): bool
    {
        return !empty($input['partyID']) && !empty($input['statementID']) &&
               isset($input['answerValue']) && in_array($input['answerValue'], ['agree', 'neither', 'disagree']);
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