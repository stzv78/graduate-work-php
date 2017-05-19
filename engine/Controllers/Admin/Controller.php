<?php

namespace Engine\Controllers\Admin;

use Engine\Controllers\Controllers;
use Engine\Core\Models\Admin\Model;

class Controller extends Controllers
{

    protected function setModel()
    {
        $this->model = new Model();
    }

    public function actionIndex()
    {
        $array = $this->model->getData();
        $this->view->render('admin', $array);
    }
}