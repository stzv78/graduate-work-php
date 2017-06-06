<?php

namespace Engine\Controllers\Main;

use Engine\Core\ParentController\Controller;
use Engine\Models\Main\MainModel as Main;

/**
 * ======================================================
 * Class AdminController
 *
 *  Работа методов:
 *
 *  - Обрабатывают и собирают данные,
 *  - Отвправляют данные для рендеринга страниц*(не все методы отправляют данные)
 *  - Запускают рендер страницы
 * ======================================================
 */
class MainController extends Controller
{
    /**
     * Трейт с ошибками
     */
    use Errors;

    /**
     *  Подключает модель
     */
    protected function setModel()
    {
        $this->model['main'] = new Main();
    }

    public function actionIndex()
    {
        $questions = $this->model['main']->getQuestion();
        $categories = $this->model['main']->getCategories();

        $array = [
            'header' => [
                'title' => 'F.A.Q.',
                'theme' => 'index'
            ],
            'data' => [
                'header' => 'F.A.Q.',
                'categories' => $categories,
                'questions' => $questions
            ]
        ];

        $this->view->render('index', $array);
    }

    public function actionQuestion()
    {
        $errors = [];
        $data = $_POST;

        if (isset($data['goQuestion'])) {

            $errors = self::checkDataQuestion($data);

            if (empty($errors)) {
                $this->model['main']->methodCall('questionRecord', $data);
                $data['success'] = 'Вопрос отправлен!';
            }
        }

        $categories = $this->model['main']->getCategories();

        $array = [
            'header' => [
                'title' => 'F.A.Q.'
            ],
            'data' => [
                'header' => 'Задать вопрос',
                'error' => @array_shift($errors),
                'data' => $data,
                'categories' => $categories
            ]
        ];

        $this->view->render('question', $array);
    }

    public function action404()
    {
        $this->view->render('404');
    }
}