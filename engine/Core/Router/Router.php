<?php

namespace Engine\Core\Router;

class Router
{
    static function start()
    {
        $controller_name = 'Main';
        $action_name = 'Index';

        $routes = explode('/', $_SERVER['REQUEST_URI']);

        if (!empty($routes[2])) {
            $controller_name = ucfirst($routes[2]);
        }
        if (!empty($routes[3])) {
            $action_name = ucfirst($routes[3]);
        }

        $action_name = 'action' . $action_name;

        $controller_path = getPathController($controller_name);
        if (file_exists($controller_path)) {
            require_once $controller_path;
        } else {
            echo 404;
            #Router::ErrorPage404();
        }

        $controller_name = 'Engine\Controllers\\' . $controller_name . '\\Controller';
        $controller = new $controller_name();
        $action = $action_name;

        if (method_exists($controller, $action)) {
            $controller->$action();
        } else {
            echo 404;
            #Router::ErrorPage404();
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
