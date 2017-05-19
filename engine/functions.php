<?php

function redirect($uri = '') {
    header('Location:' . $uri);
}

function getPathController($controller_name) {
    $path = ROOT_DIR . "engine" . DS . "Controllers" . DS . $controller_name . DS . "Controller.php";
    return $path;
}