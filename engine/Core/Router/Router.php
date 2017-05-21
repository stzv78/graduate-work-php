<?php

namespace Engine\Core\Router;

class Router
{
    static function start()
    {
        $controllerName = 'Main';
        $actionName = 'Index';

        $routes = explode('/', $_SERVER['REQUEST_URI']);

        if (!empty($routes[2])) {
            $controllerName = ucfirst($routes[2]);
        }
        if (!empty($routes[3])) {
            $actionName = ucfirst($routes[3]);
        }

        $actionName = 'action' . $actionName;

        $pathController = getPathController($controllerName);
        if (!file_exists($pathController)) {
            echo 404;
            #self::ErrorPage404();
        }

        $controllerName = 'Engine\Controllers\\' . $controllerName . '\\Controller';
        $controller = new $controllerName();
        $action = $actionName;

        if (method_exists($controller, $action)) {
            $controller->$action();
        } else {
            echo 404;
            #self::ErrorPage404();
        }

    }

    static function ErrorPage404()
    {
        $host = 'http://' . $_SERVER['HTTP_HOST'] . '/';
        header('HTTP/1.1 404 Not Found');
        header("Status: 404 Not Found");
        header('Location:' . $host . '?/404');
    }
}
