<?php

namespace Engine\Controllers;

use Engine\Core\View\View;
/**
 * Class Controllers
 * @package Engine\Controllers
 */
class Controllers
{
    /**
     * @var объект подключаемой модели
     */
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
     * @return mixed
     */
    protected function setModel()
    {

    }

    public function actionIndex() {

    }
}