<?php
/**
 * ======================================================
 *
 *  Запускает приложение
 *
 * ======================================================
 */

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../constants.php";

use Engine\Core\Router\Router;
use Engine\Core\Database\Connection;

try {
    session_start();

    $db = new Connection();

    $router = new Router();
    $router->start();

} catch (\ErrorException $e) {
    echo $e->getMessage();
}