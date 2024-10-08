<?php

class PartyStatement
{
    private $conn;
    private $table = 'sw_parties_statements';

    public $partyID;
    public $statementID;
    public $answerValue;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function getAll()
    {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $partyStatements = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($partyStatements) {
            return [
                'status' => 200,
                'data' => $partyStatements
            ];
        } else {
            return [
                'status' => 404,
                'message' => 'No party statements found'
            ];
        }
    }

    public function getByPartyId($partyID)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE partyID = :partyID";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":partyID", $partyID);
        $stmt->execute();
        $partyStatements = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($partyStatements) {
            return [
                'status' => 200,
                'data' => $partyStatements
            ];
        } else {
            return [
                'status' => 404,
                'message' => 'No party statements found for this party'
            ];
        }
    }

    public function getByStatementId($statementId)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE statementID = :statementId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":statementId", $statementId);
        $stmt->execute();
        $partyStatements = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($partyStatements) {
            return [
                'status' => 200,
                'data' => $partyStatements
            ];
        } else {
            return [
                'status' => 404,
                'message' => 'No party statements found for this statement'
            ];
        }
    }

    public function add()
    {
        $query = "INSERT INTO " . $this->table . " (partyID, statementID, answerValue) VALUES (:partyID, :statementID, :answerValue)";
        $stmt = $this->conn->prepare($query);

        $this->partyID = htmlspecialchars(strip_tags($this->partyID));
        $this->statementID = htmlspecialchars(strip_tags($this->statementID));
        $this->answerValue = htmlspecialchars(strip_tags($this->answerValue));

        $stmt->bindParam(":partyID", $this->partyID);
        $stmt->bindParam(":statementID", $this->statementID);
        $stmt->bindParam(":answerValue", $this->answerValue);

        if ($stmt->execute()) {
            return [
                'status' => 201,
                'message' => 'Party statement created'
            ];
        }
        return [
            'status' => 500,
            'message' => 'Failed to create party statement'
        ];
    }

    public function update()
    {
        $query = "UPDATE " . $this->table . " SET answerValue = :answerValue WHERE partyID = :partyID AND statementID = :statementID";
        $stmt = $this->conn->prepare($query);

        $this->partyID = htmlspecialchars(strip_tags($this->partyID));
        $this->statementID = htmlspecialchars(strip_tags($this->statementID));
        $this->answerValue = htmlspecialchars(strip_tags($this->answerValue));

        $stmt->bindParam(":partyID", $this->partyID);
        $stmt->bindParam(":statementID", $this->statementID);
        $stmt->bindParam(":answerValue", $this->answerValue);

        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                return [
                    'status' => 200,
                    'message' => 'Party statement updated'
                ];
            } else {
                return [
                    'status' => 404,
                    'message' => 'Party statement not found'
                ];
            }
        }
        return [
            'status' => 500,
            'message' => 'Failed to update party statement'
        ];
    }

    public function delete()
    {
        $query = "DELETE FROM " . $this->table . " WHERE partyID = :partyID AND statementID = :statementID";
        $stmt = $this->conn->prepare($query);

        $this->partyID = htmlspecialchars(strip_tags($this->partyID));
        $this->statementID = htmlspecialchars(strip_tags($this->statementID));

        $stmt->bindParam(":partyID", $this->partyID);
        $stmt->bindParam(":statementID", $this->statementID);

        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                return [
                    'status' => 204,
                    'message' => 'Party statement deleted'
                ];
            } else {
                return [
                    'status' => 404,
                    'message' => 'Party statement not found'
                ];
            }
        }
        return [
            'status' => 500,
            'message' => 'Failed to delete party statement'
        ];
    }
}