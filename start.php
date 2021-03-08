<?php
require 'vendor/autoload.php';
use Dotenv\Dotenv;

use Src\Database;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$dbConnection = (new Database())->connect();
