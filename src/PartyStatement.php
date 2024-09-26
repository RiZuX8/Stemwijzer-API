<?php

class PartyStatement
{
    private $conn;
    private $table = 'parties_statements';

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
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBypartyID($partyID)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE partyID = :partyID";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":partyID", $partyID);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByStatementId($statementId)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE statementID = :statementId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":statementId", $statementId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function add()
    {
        $query = "INSERT INTO " . $this->table . " (partyID, statementID, answerValue) VALUES (:partyID, :statementID, :answerValue)";
        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $this->partyID = htmlspecialchars(strip_tags($this->partyID));
        $this->statementID = htmlspecialchars(strip_tags($this->statementID));
        $this->answerValue = htmlspecialchars(strip_tags($this->answerValue));

        // Bind parameters
        $stmt->bindParam(":partyID", $this->partyID);
        $stmt->bindParam(":statementID", $this->statementID);
        $stmt->bindParam(":answerValue", $this->answerValue);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function update()
    {
        $query = "UPDATE " . $this->table . " SET answerValue = :answerValue WHERE partyID = :partyID AND statementID = :statementID";
        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $this->partyID = htmlspecialchars(strip_tags($this->partyID));
        $this->statementID = htmlspecialchars(strip_tags($this->statementID));
        $this->answerValue = htmlspecialchars(strip_tags($this->answerValue));

        // Bind parameters
        $stmt->bindParam(":partyID", $this->partyID);
        $stmt->bindParam(":statementID", $this->statementID);
        $stmt->bindParam(":answerValue", $this->answerValue);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete()
    {
        $query = "DELETE FROM " . $this->table . " WHERE partyID = :partyID AND statementID = :statementID";
        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $this->partyID = htmlspecialchars(strip_tags($this->partyID));
        $this->statementID = htmlspecialchars(strip_tags($this->statementID));

        // Bind parameters
        $stmt->bindParam(":partyID", $this->partyID);
        $stmt->bindParam(":statementID", $this->statementID);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}