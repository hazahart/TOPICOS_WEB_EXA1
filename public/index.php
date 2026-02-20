<?php
require_once __DIR__ . '/../vendor/autoload.php';

// headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../core/Router.php';
require_once '../resources/v1/QrResource.php';

$basePath = dirname($_SERVER['SCRIPT_NAME']);

$router = new Router('v1', $basePath);
$qrResource = new QrResource();

// Endpoints
$router->addRoute('POST', '/qr/text', [$qrResource, 'generarTexto']);
$router->addRoute('POST', '/qr/url', [$qrResource, 'generarURL']);
$router->addRoute('POST', '/qr/wifi', [$qrResource, 'generarWifi']);
$router->addRoute('POST', '/qr/geo', [$qrResource, 'generarGeo']);

$router->dispatch();
?>