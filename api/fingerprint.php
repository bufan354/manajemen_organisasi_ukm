<?php
date_default_timezone_set('Asia/Jakarta');
require_once __DIR__ . '/../core/helpers.php';
require_once __DIR__ . '/../core/controllers/FingerprintController.php';

$apiKey = getEnvVar('API_KEY');
$headers = array_change_key_case(getallheaders(), CASE_UPPER);
$providedKey = $headers['X-API-KEY'] ?? $_GET['api_key'] ?? null;

if (!$providedKey || $providedKey !== $apiKey) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized: Invalid API Key']);
    exit;
}

$action = $_GET['action'] ?? null;
$controller = new FingerprintController();

switch ($action) {
    case 'register':
        $controller->registerApi();
        break;
    case 'verify':
        $controller->verifyApi();
        break;
    case 'delete':
        $controller->deleteApi();
        break;
    case 'mode':
        $controller->getModeApi();
        break;
    default:
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Endpoint not found']);
        break;
}
