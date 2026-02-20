<?php

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/UrlController.php';

$database = new Database();
$db = $database->getConnection();

$controller = new UrlController($db);

$requestMethod = $_SERVER["REQUEST_METHOD"];
$requestUri = $_SERVER["REQUEST_URI"];

// Quitar query strings
$requestUri = strtok($requestUri, '?');

if ($requestMethod === 'POST' && $requestUri === '/api/v1/urls') {
    $controller->store();
    exit;
}

http_response_code(404);
echo json_encode(["error" => "Ruta no encontrada"]);