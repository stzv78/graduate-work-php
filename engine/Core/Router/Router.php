<?php

namespace Engine\Core\Router;

/**
 * ======================================================
 * Class Router
 *  Запускает контроллеры и методы запрашиваемые пользователем через URL
 *
 * ======================================================
 */
class Router
{

    /**
     * Определяет запрос и запускает контроллер и метод
     */
    public function start()
    {
        $controllerName = 'Main';
        $actionName = 'Index';
        $uri = explode("?", $_SERVER['REQUEST_URI']);

        $uri[1] = isset($uri[1]) ? $uri[1] : '';
        $routes = explode('/', $uri[1]);

        if (!empty($routes[1])) {
            $controllerName = ucfirst($routes[1]);
            define('CONTROLLER', $routes[1]);
        }
        if (!empty($routes[2])) {
            $actionName = ucfirst($routes[2]);
            define('ACTION', $routes[2]);
        }

        $actionName = 'action' . $actionName;

        $pathController = self::getPathController($controllerName);

        if (!file_exists($pathController)) {
            self::ErrorPage404();
        }

        $controllerName = 'Engine\Controllers\\' . $controllerName . '\\' . $controllerName . 'Controller';
        $controller = new $controllerName();
        $action = $actionName;

        if (method_exists($controller, $action)) {
            $controller->$action();
        } else {
            self::ErrorPage404();
        }

    }

    private function getPathController($controllerName)
    {
        $pathToControllers = ROOT_DIR . "engine" . DS . "Controllers" . DS;
        $pathToController = $controllerName . DS . $controllerName . "Controller.php";
        $path =  $pathToControllers . $pathToController;

        return $path;
    }

    private function ErrorPage404()
    {
        header('HTTP/1.1 404 Not Found');
        redirect('?/main/404');
    }
}
