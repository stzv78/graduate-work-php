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

        if (empty($errors) && isset($data['goQuestion'])) {
            self::questionRecord($data);
            $data['success'] = 'Вопрос отправлен!';
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

    static private function questionRecord($data) {
        $dictionary = R::getAll('SELECT * FROM dictionary');

        $words = [];
        foreach ($dictionary as $id => $word) {
            if (strpos($data['questionUser'], $word['word']) !== false) {
                $words[] = $word['id'];
            }
        }

        $words = implode(':',$words);

        if (empty($words)) {
            $question = R::dispense('unanswered');
        } else {
            $question = R::dispense('blocked');
            $question->words = $words;
        }

        $question->name = $data['nameUser'];
        $question->email = $data['emailUser'];
        $question->question = $data['questionUser'];
        $question->category = $data['categoryUser'];
        R::store($question);

    }
}