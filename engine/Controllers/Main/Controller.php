<?php

namespace Engine\Controllers\Main;

use Engine\Controllers\Controllers;
use Engine\Core\Models\Main\Model;

/**
 * Class Controllers
 * @package Engine\Controllers
 */
class Controller extends Controllers
{
    protected function setModel()
    {
        $this->model = new Model();
    }

    public function actionIndex()
    {
        $array = $this->model->getData();
        $this->view->render('index', $array);
    }
}