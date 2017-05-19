<?php
require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../constants.php";
require_once __DIR__ . "/functions.php";

use Engine\DI\DI;
use Engine\Session;

try {
    session_start();

    $di = new DI();

    $services = require __DIR__ . "/Config/Service.php";
    foreach ($services as $service) {
        $provider = new $service($di);
        $provider->init();
    }

    $session = new Session($di);
    $session->run();
} catch (\ErrorException $e) {
    echo $e->getMessage();
}