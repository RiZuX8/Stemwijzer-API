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
                    if (isset($pathParts[1])) {
                        if ($pathParts[1] === 'party') {
                            self::getByParty($pathParts[2]);
                        } elseif ($pathParts[1] === 'statement') {
                            self::getByStatement($pathParts[2]);
                        } else {
                            self::sendResponse(404, ["message" => "Not Found"]);
                        }
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
            $result = $partyStatement->getAll();
            if ($result['status'] === 200) {
                self::sendResponse($result['status'], $result['data']);
            } else {
                self::sendResponse($result['status'], ["message" => $result['message']]);
            }
        } catch (PDOException $e) {
            self::sendResponse(500, ["message" => "Database error: " . $e->getMessage()]);
        }
    }

    private static function getByParty($partyID)
    {
        try {
            $partyStatement = new PartyStatement(self::$db->conn);
            $result = $partyStatement->getByPartyID($partyID);
            if ($result['status'] === 200) {
                self::sendResponse($result['status'], $result['data']);
            } else {
                self::sendResponse($result['status'], ["message" => $result['message']]);
            }
        } catch (PDOException $e) {
            self::sendResponse(500, ["message" => "Database error: " . $e->getMessage()]);
        }
    }

    private static function getByStatement($statementID)
    {
        try {
            $partyStatement = new PartyStatement(self::$db->conn);
            $result = $partyStatement->getByStatementID($statementID);
            if ($result['status'] === 200) {
                self::sendResponse($result['status'], $result['data']);
            } else {
                self::sendResponse($result['status'], ["message" => $result['message']]);
            }
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

                $result = $partyStatement->add();
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

    private static function updatePartyStatement()
    {
        $input = json_decode(file_get_contents("php://input"), true);
        if (self::validatePartyStatementInput($input)) {
            try {
                $partyStatement = new PartyStatement(self::$db->conn);
                $partyStatement->partyID = $input['partyID'];
                $partyStatement->statementID = $input['statementID'];
                $partyStatement->answerValue = $input['answerValue'];

                $result = $partyStatement->update();
                if ($result['status'] === 200) {
                    self::sendResponse($result['status'], ["message" => $result['message']]);
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

                $result = $partyStatement->delete();
                if ($result['status'] === 204) {
                    self::sendResponse($result['status'], null); // 204 means "No Content"
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