<?php

namespace Engine\Models\Main;

use Engine\Core\ParentModel\Model;
use RedBeanPHP\R;

/**
 * ======================================================
 * Class Model
 *  Контроллер Engine\Controllers\Main\Controller
 *
 *  Методы protected вызываются контроллером
 *  Методы private вызываются моделью
 *
 * ======================================================
 */
class MainModel extends Model
{
    public function getData($method, $data = '')
    {
        $array = self::$method();
        return $array;
    }

    /**
     * ======================================================
     * Index-ный метод
     *
     *  Собирает данные для вывода:
     *
     *  - Вопросы таблицы
     *    - answer вопросы с ответом*
     *  - Список категорий
     *
     *  Возвращает массив с данными
     * ======================================================
     */
    protected function index()
    {
        $questions = R::getAll('SELECT id,name,email,question,answers,category,time,hidden FROM answer');
        $categories = R::getAll('SELECT id,title FROM categories');
        return [
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
    }

    /**
     * ======================================================
     * Метод question
     *
     *  Получает данные через $_POST
     *  Обрабатывает данные
     *  Запускает запись вопроса в БД
     *
     *  Возвращает массив с данными
     * ======================================================
     */
    protected function question()
    {
        $errors = [];
        $data = $_POST;

        if (isset($data['goQuestion'])) {

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
            if (mb_strlen($data['questionUser']) >= 250) {
                $errors[] = 'В тексте должно быть меньше 250 сиволов*';
            }

            if (empty($errors)) {
                self::questionRecord($data);
                $data['success'] = 'Вопрос отправлен!';
            }
        }

        $categories = R::getAll('SELECT id,title FROM categories');

        return [
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
    }

    /**
     * Записывает вопрос в БД
     */
    private function questionRecord($data)
    {
        $dictionary = R::getAll('SELECT id,word FROM dictionary');

        $words = [];
        if (!empty($dictionary)) {
            foreach ($dictionary as $id => $word) {
                if (strpos($data['questionUser'], $word['word']) !== false) {
                    $words[] = $word['id'];
                }
            }
        }

        $words = implode(':', $words);

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
        $question->time = R::isoDateTime();
        R::store($question);
    }
}