<?php

namespace Engine\Controllers;

use Engine\Core\View\View;
/**
 * Class Controllers
 * @package Engine\Controllers
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

    abstract protected function setModel();

    abstract public function actionIndex();
}