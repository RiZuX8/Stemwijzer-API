<?php
header("Access-Control-Allow-Origin: http://stemwijzer.local");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);

$pathParts = explode('/', trim($path, '/'));

foreach ($pathParts as $key => $part) {
    $pathParts[$key] = htmlspecialchars($part);
}

$controller = $pathParts[0];

switch ($controller) {
    case 'statements':
        require_once '../src/StatementController.php';
        StatementController::handleRequest($method, $pathParts);
        break;
    case 'parties':
        require_once '../src/PartyController.php';
        PartyController::handleRequest($method, $pathParts);
        break;
    case 'parties-statements':
        require_once '../src/PartyStatementController.php';
        PartyStatementController::handleRequest($method, $pathParts);
        break;
    case 'admins':
        require_once '../src/AdminController.php';
        AdminController::handleRequest($method, $pathParts);
        break;
    case 'superadmins':
        require_once '../src/SuperAdminController.php';
        SuperAdminController::handleRequest($method, $pathParts);
        break;
    default:
        header("Content-Type: application/json");
        http_response_code(404);
        echo json_encode("Not found " . $pathParts[0]);
        break;
}
?>
