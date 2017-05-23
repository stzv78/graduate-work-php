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
        $array = $this->model->getData('index');
        $this->view->render('admin', $array);
    }

    public function actionLogin()
    {
        $array = $this->model->getData('login');
        $this->view->render('login', $array);
    }

    public function actionEdit()
    {
        $array = $this->model->getData('edit');
        $this->view->render('edit', $array);
    }

    public function actionCategory() {
        $this->model->getData('category');
    }

    public function actionDictionary() {
        $this->model->getData('dictionary');
    }

    public function actionLogout()
    {
        session_destroy();
        redirect('');
    }
}