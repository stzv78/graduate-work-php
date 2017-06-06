<?php

namespace Engine\Controllers\Admin;

use Engine\Core\ParentController\Controller;
use Engine\Models\Admin\AdminModel as Admin;

/**
 * ======================================================
 * Class Controller
 *  Модель Admin
 * ======================================================
 */
class AdminController extends Controller
{
    use Errors;

    /**
     *  Подключает модель
     */
    protected function setModel()
    {
        $this->model = new Admin();
    }

    /**
     *  Работа методов:
     *
     *  - Вызывют запрошенные методы модели
     *  - Отвправляют данные для рендеринга страниц*(не все методы отправляют данные)
     *
     */
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

    public function actionAdmin() {
        $this->model->getData('admin');
    }

    public function actionLogout()
    {
        session_destroy();
        redirect('');
    }
}