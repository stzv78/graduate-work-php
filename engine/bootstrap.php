<?php
require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../constants.php";
require_once __DIR__ . "/functions.php";

use Engine\Core\Router\Router;

try {
    $db = new \Engine\Core\Database\Connection();
    $router = new Router();
    $router->start();
} catch (\ErrorException $e) {
    echo $e->getMessage();
}