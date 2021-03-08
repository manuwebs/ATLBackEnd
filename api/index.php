<?php
require "../start.php";
use Src\Contact;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = explode('/', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

if ($uri[1] !== 'contacts') {
    header("HTTP/1.1 404 Not Found");
    exit();
}

$id = isset($uri[2]) ? (int) $uri[2] : null;

$requestMethod = $_SERVER["REQUEST_METHOD"];

$controller = new Contact($dbConnection, $requestMethod, $id);
$controller->processRequest();
