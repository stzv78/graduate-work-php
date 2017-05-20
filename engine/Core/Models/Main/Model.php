<?php

namespace Engine\Core\Models\Main;

use Engine\Core\Models\Models;
use RedBeanPHP\R;

class Model extends Models
{
    public function getData($method)
    {
        $array = self::$method();
        return $array;
    }

    private function index()
    {
        return [
            'header' => [
                'title' => 'F.A.Q.'
            ],
            'data' => [
                'header' => 'F.A.Q.'
            ]
        ];
    }

    private function question()
    {
        $errors = [];
        $data = $_POST;
        if ($data['nameUser'] === '') {
            $errors[] = 'Введите имя*';
        }
        if ($data['emailUser'] === '') {
            $errors[] = 'Введите E-mail*';
        }
        if ($data['categoryUser'] === '0') {
            $errors[] = 'Выберите категорию*';
        }
        if ($data['questionUser'] === '') {
            $errors[] = 'Введите вопрос*';
        }
        if (mb_strlen($data['questionUser']) >= 1000) {
            $errors[] = 'В тексте должно быть меньше 1000 сиволов*';
        }

        if (empty($errors) && isset($_POST['goQuestion'])) {
            $question = R::dispense('unanswered');
            $question->name = $_POST['nameUser'];
            $question->email = $_POST['emailUser'];
            $question->question = $_POST['questionUser'];
            R::store($question);
            $data['success'] = 'Вопрос отправлен';
        }

        $categories = R::getAll('SELECT * FROM categories');

        return [
            'header' => [
                'title' => 'F.A.Q.'
            ],
            'data' => [
                'header' => 'Задать вопрос',
                'error' => array_shift($errors),
                'data' => $data,
                'categories' => $categories
            ]
        ];
    }
}