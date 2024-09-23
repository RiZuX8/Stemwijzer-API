<?php
require_once 'Student.php';

class StudentController
{
    public static function handleRequest($method, $path)
    {
// Split de URL op basis van '/'
        $parts = explode('/', trim($path, '/'));
        if ($parts[0] === 'students') {
            switch ($method) {
                case 'GET':
                    if (isset($parts[1])) {
                        self::getStudent($parts[1]);
                    } else {
                        self::getAllStudents();
                    }
                    break;
                case 'POST':
                    self::addStudent();
                    break;
                case 'PUT':
                    if (isset($parts[1])) {
                        self::updateStudent($parts[1]);
                    }
                    break;
                case 'DELETE':
                    if (isset($parts[1])) {
                        self::deleteStudent($parts[1]);
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

// Haal alle studenten op

    private static function getStudent($id)
    {
        $student = Student::getById($id);
        if ($student) {
            self::sendResponse(200, $student);
        } else {
            self::sendResponse(404, ["message" => "Student not found"]);
        }
    }

// Haal een specifieke student op

    private static function sendResponse($statusCode, $data)
    {
        header("Content-Type: application/json");
        http_response_code($statusCode);
        if ($data !== null) {
            echo json_encode($data);
        }
    }

// Voeg een nieuwe student toe

    private static function getAllStudents()
    {
        $students = Student::getAll();
        self::sendResponse(200, $students);
    }

// Werk een bestaande student bij

    private static function addStudent()
    {
        $input = json_decode(file_get_contents("php://input"), true);
        if (isset($input['name']) && !empty($input['name'])) {
            $newStudent = Student::add($input['name']);
            self::sendResponse(201, $newStudent);
        } else {
            self::sendResponse(400, ["message" => "Invalid input"]);
        }
    }

// Verwijder een student

    private static function updateStudent($id)
    {
        $input = json_decode(file_get_contents("php://input"), true);
        if (isset($input['name']) && !empty($input['name'])) {
            $updatedStudent = Student::update($id, $input['name']);
            if ($updatedStudent) {
                self::sendResponse(200, $updatedStudent);
            } else {
                self::sendResponse(404, ["message" => "Student not found"]);
            }
        } else {
            self::sendResponse(400, ["message" => "Invalid input"]);
        }
    }

// Algemene functie om JSON-responses te versturen

    private static function deleteStudent($id)
    {
        if (Student::delete($id)) {
            self::sendResponse(204, null); // 204 betekent "No Content"
        } else {
            self::sendResponse(404, ["message" => "Student not found"]);
        }
    }
}
