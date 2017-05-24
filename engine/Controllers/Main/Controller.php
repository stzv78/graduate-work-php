<?php

namespace Engine\Controllers\Main;

use Engine\Controllers\Controllers;
use Engine\Core\Models\Main\Model as Main;

/**
 * ======================================================
 * Class Controller
 *  Модель Main
 * ======================================================
 */
class Controller extends Controllers
{
    /**
     *  Подключает модель
     */
    protected function setModel()
    {
        $this->model = new Main();
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
        $this->view->render('index', $array);
    }

    public function actionQuestion()
    {
        $array = $this->model->getData('question');
        $this->view->render('question', $array);
    }

    public function action404()
    {
        $this->view->render('404');
    }
}