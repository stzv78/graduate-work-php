<?php

function redirect($uri = '') {
    header('Location:' . HOST . $uri);
    exit;
}

function getPathController($controllerName) {
    $path = ROOT_DIR . "engine" . DS . "Controllers" . DS . $controllerName . DS . "Controller.php";
    return $path;
}

function logAdmin($action) {
    $file = ROOT_DIR . 'log/log.txt';
    $time = date("Y-m-d H:i:s");
    $admin = $_SESSION['adminLogin'];
    $log = "[$time] $admin $action \n";
    file_put_contents($file, $log, FILE_APPEND | LOCK_EX);
}