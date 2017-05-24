<?php

namespace Engine\Controllers;

use Engine\Core\View\View;

/**
 * ======================================================
 * Родительский контроллер Controller
 *  подключает класс View для рендеренга страниц
 * ======================================================
 */
abstract class Controllers
{
    protected $model;
    protected $view;

    /**
     * Controllers constructor.
     */
    public function __construct()
    {
        $this->view = new View();
        $this->setModel();
    }

    /**
     * Метод для подключения модели
     */
    abstract protected function setModel();

    /**
     * Метод запуска Index-ной страници модели
     */
    abstract public function actionIndex();
}