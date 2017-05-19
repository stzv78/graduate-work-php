<?php

function redirect($uri = '') {
    header('Location:' . $uri);
}

function getPathController($controllerName) {
    $path = ROOT_DIR . "engine" . DS . "Controllers" . DS . $controllerName . DS . "Controller.php";
    return $path;
}
