<?php

namespace Engine\Core\Router;

use Engine\Core\Response\Response;

/**
 * ======================================================
 * Class Router
 *  Запускает контроллеры и методы запрашиваемые пользователем через URL
 * ======================================================
 */
class Router
{

    /**
     * Определяет запрос и запускает контроллер и метод
     */
    public function start()
    {
        $routes = self::getRouters();
        self::setConstant($routes);

        $controllerName = ucfirst(CONTROLLER);

        self::checkFileExistsController($controllerName);

        $controller = self::getController($controllerName);

        self::checkMethodExistsController($controller);
    }


    /**
     * Вовращает массив с значениями из URL после ? знака
     * @return array
     */
    private function getRouters()
    {
        $uri = explode("?", $_SERVER['REQUEST_URI']);
        $uri[1] = isset($uri[1]) ? $uri[1] : '';

        $routes = explode('/', $uri[1]);

        return $routes;
    }

    /**
     * Создаёт константы с именем контроллера и дествия
     * @param $routes
     */
    private function setConstant($routes)
    {
        self::setConstantController($routes);
        self::setConstantAction($routes);
    }

    private function setConstantController($routes)
    {
        if (!empty($routes[1])) {
            define('CONTROLLER', $routes[1]);
        } else {
            define('CONTROLLER', 'main');
        }
    }

    private function setConstantAction($routes)
    {
        if (!empty($routes[2])) {
            define('ACTION', $routes[2]);
        } else {
            define('ACTION', 'index');
        }
    }

    /**
     * Вовращает объект контроллера
     * @param $controllerName
     * @return mixed
     */
    private function getController($controllerName)
    {
        $controllerName = 'Engine\Controllers\\' . $controllerName . '\\' . $controllerName . 'Controller';
        $controller = new $controllerName();

        return $controller;
    }


    /**
     * Проверяет на существования контроллера
     * @param $controllerName
     */
    private function checkFileExistsController($controllerName)
    {
        $pathController = self::getPathController($controllerName);

        if (!file_exists($pathController)) {
            self::ErrorPage404();
        }

    }

    /**
     * Возвращает путь до контроллера
     * @param $controllerName
     * @return string
     */
    private function getPathController($controllerName)
    {
        $pathToControllers = ROOT_DIR . "engine" . DS . "Controllers" . DS;
        $pathToController = $controllerName . DS . $controllerName . "Controller.php";
        $path =  $pathToControllers . $pathToController;

        return $path;
    }

    /**
     * Проверяет есть ди метод в контроллере
     * @param $controller
     */
    private function checkMethodExistsController($controller)
    {
        $actionName = 'action' . ucfirst(ACTION);

        if (method_exists($controller, $actionName)) {
            $controller->$actionName();
        } else {
            self::ErrorPage404();
        }
    }

    /**
     * Выдаёт 404 ошибку
     */
    private function ErrorPage404()
    {
        header('HTTP/1.1 404 Not Found');
        Response::redirect('?/main/404');
    }
}
