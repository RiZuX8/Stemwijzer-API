<?php
// Haal het HTTP-verzoek op (method en path)
$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);

$pathParts = explode('/', trim($path, '/'));

if ($pathParts[0] === 'students') {
    require_once '../src/StudentController.php';
    StudentController::handleRequest($method, $pathParts);
} else {
    header("Content-Type: application/json");
    http_response_code(404);
    echo json_encode("Not found");
}
?>
