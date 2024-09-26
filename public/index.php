<?php
// Haal het HTTP-verzoek op (method en path)
$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);

$pathParts = explode('/', trim($path, '/'));

foreach ($pathParts as $key => $part) {
    $pathParts[$key] = htmlspecialchars($part);
}

$controller = $pathParts[0];

if ($controller === 'statements') {
    require_once '../src/StatementController.php';
    StatementController::handleRequest($method, $pathParts);
} else if ($controller === 'parties') {
    require_once '../src/PartyController.php';
    PartyController::handleRequest($method, $pathParts);
} else if ($controller === 'parties-statements') {
    require_once '../src/PartyStatementController.php';
    PartyStatementController::handleRequest($method, $pathParts);
} else if ($controller === 'admins') {
    require_once '../src/AdminController.php';
    AdminController::handleRequest($method, $pathParts);
} else if ($controller === 'superAdmins') {
    require_once '../src/SuperAdminController.php';
    SuperAdminController::handleRequest($method, $pathParts);

} else {
    header("Content-Type: application/json");
    http_response_code(404);
    echo json_encode("Not found " . $pathParts[0]);
}
?>
