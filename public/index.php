<?php
require_once '../src/StudentController.php';
// Haal het HTTP-verzoek op (method en path)
$method = $_SERVER['REQUEST_METHOD'];
$path = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/';

// Roep de controller aan om het verzoek te verwerken
StudentController::handleRequest($method, $path);
