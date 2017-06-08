<?php
/**
 * ======================================================
 *
 *  Собственные константы
 *
 * ======================================================
 */

$host = explode("?", $_SERVER['REQUEST_URI']);

define('HOST', $host[0]);
define('DS', DIRECTORY_SEPARATOR);
define('TWIG_CACHE', __DIR__ . DS . "cache");
define('TWIG_TEMPLATES', __DIR__ . DS . "content");
define('DATABASE_CONFIG', __DIR__ . DS . "engine" . DS . "Config" . DS . "Database.php");
define('ROOT_DIR', __DIR__ . DS);
