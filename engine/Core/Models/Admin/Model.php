<?php

namespace Engine\Core\Models\Admin;

use Engine\Core\Models\Models;
use RedBeanPHP\R;

/**
 * ======================================================
 * Class Model
 *  Контроллер Engine\Controllers\Admin\Controller
 *
 *  Методы protected вызываются контроллером
 *  Методы private вызываются моделью
 *
 * ======================================================
 */
class Model extends Models
{
    /**
     * Запускает метод по запросу контроллера
     * @param $method
     * @return mixed
     */
    public function getData($method)
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
     *  - Вопросы с трёх таблиц
     *    - unanswered без ответа*
     *    - blocked заблокированные по ключевым словам*
     *    - answer вопросы с ответом*
     *  - Список категорий
     *  - Список администраторов
     *  - Список ключевых слов
     *
     *  Возвращает массив с данными
     * ======================================================
     */
    protected function index()
    {
        self::sessionCheck();

        $questions = self::getQuestions();
        $categories = self::getCategories();
        $admins = self::getAdmins();
        $dictionary = self::getDictionary();

        return [
            'header' => [
                'title' => 'Панель администратора'
            ],
            'data' => [
                'header' => 'Привет ' . $_SESSION['adminLogin'] . '!',
                'questions' => $questions,
                'categories' => $categories,
                'admins' => $admins,
                'dictionary' => $dictionary
            ]
        ];
    }

    /**
     * Возращает массив с вопросами
     * @return mixed
     */
    private function getQuestions()
    {
        $questions['unanswered'] = R::getAll('SELECT * FROM unanswered');
        $questions['blocked'] = R::getAll('SELECT * FROM blocked');
        $questions['answer'] = R::getAll('SELECT * FROM answer');
        return $questions;
    }

    /**
     * Возращает массив с категориями
     * @return array
     */
    private function getCategories()
    {
        $categories = R::getAll('SELECT * FROM categories');
        return $categories;
    }

    /**
     * Возращает строку с категорией запрошенную по id
     * @return array
     */
    private function getCategory($id)
    {
        $category = R::getAll('SELECT title FROM categories WHERE id LIKE :id', [
            'id' => $id
        ]);
        return $category[0]['title'];
    }

    /**
     * Возращает массив с администраторами
     * @return array
     */
    private function getAdmins()
    {
        //return array с админами
    }

    /**
     * Возращает массив с ключевыми словами
     * @return array
     */
    private function getDictionary()
    {
        $dictionary = R::getAll('SELECT * FROM dictionary');
        $words = [];
        foreach ($dictionary as $key => $word) {
            $words[] = $word['word'];
        }
        $words = implode(", ", $words);
        return $words;
    }

    /**
     * ======================================================
     * Метод edit
     *
     *  - Работает с вопросом. Данные получает через $_POST.
     *  - Проверяет данные для работы с вопрос и делает запрос в базу иначе переадресовывает.
     *  - Если были обновлены данные администратором то проверяет корекнтость данных и записывает в БД.
     *  - Логирует действия администратора.
     *
     *  - Возвращает массив с данными
     * ======================================================
     */
    protected function edit()
    {
        self::sessionCheck();

        $errors = [];
        $data = $_POST;

        if (isset($data['type'])) {

            $queistion = R::getAll('SELECT * FROM ' . $data['type'] . ' WHERE id LIKE :id', [
                'id' => $data['id']
            ]);

        } else {
            redirect('?/admin');
        }

        if (isset($data['updateQuestion'])) {
            $errors = self::updateQuestion($data);
        }

        foreach ($queistion as $key => $query) {
            if (isset($data['updateQuestion'])) {
                $queistion = $data;
            } else {
                $queistion = $query;
                $queistion['type'] = $data['type'];
            }
            continue;
        }

        if (isset($data['action'])) {

            if ($data['action'] === 'delete') {
                $category = self::getCategory($queistion['category']);
                logAdmin('удалил вопрос: ' . $data['type'] . ' из категории: ' . $category);
                self::trashQuestion($data['type'], $data['id']);
                redirect('?/admin');
            }

            if ($data['action'] === 'open') {
                $queistion['hidden'] = 0;
                $action = 'открыл ';
            } else {
                $queistion['hidden'] = 1;
                $action = 'скрыл ';
            }
            $category = self::getCategory($queistion['category']);
            logAdmin($action . 'вопрос (id:' . $data['id'] . ') из категории: ' . $category);

            self::createAnswer($queistion);
        }

        $categories = self::getCategories();

        return [
            'header' => [
                'title' => 'Панель администратора'
            ],
            'data' => [
                'header' => 'Ответить на вопрос',
                'question' => $queistion,
                'categories' => $categories,
                'error' => @array_shift($errors)
            ]
        ];
    }

    /**
     * Проверяет данные отправляет запрос и логирует иначе возвращает ошибку
     * @param $data
     * @return array
     */
    private function updateQuestion($data)
    {

        if (trim($data['name']) === '') {
            $errors[] = 'Заполните имя*';
        }
        if (trim($data['email']) === '') {
            $errors[] = 'Заполните E-mail*';
        }
        if (trim($data['question']) === '') {
            $errors[] = 'Напишите вопрос*';
        }
        if (mb_strlen(trim($data['question'])) >= 250) {
            $errors[] = 'В тексте вопроса должно быть меньше 250 сиволов*';
        }
        if (trim($data['answers']) === '') {
            $errors[] = 'Напишите ответ*';
        }
        if ($data['category'] === '0') {
            $errors[] = 'Выбирите категорию*';
        }

        if ($data['type'] !== 'answer') {
            $category = self::getCategory($data['category']);
            logAdmin('ответил на вопрос: ' . $data['type'] . ' из категории: ' . $category);
        } else {
            $category = self::getCategory($data['category']);
            logAdmin('обновил вопрос: (id:' . $data['id'] . ') из категории: ' . $category);
        }

        if (empty($errors)) {
            self::createAnswer($data);
        }

        return $errors;
    }

    /**
     * Удаляет вопрос
     * @param $table
     * @param $id
     */
    private function trashQuestion($table, $id)
    {
        $question = R::load($table, $id);
        R::trash($question);
    }

    /**
     * Удаляет вопрос без ответа и создаёт вопрос с ответом
     * Или обновляет вопрос с ответом
     * @param $data
     */
    private function createAnswer($data)
    {
        if ($data['type'] !== 'answer') {
            self::trashQuestion($data['type'], $data['id']);
            $answer = R::dispense('answer');
            $answer->hidden = 0;
        } else {
            $answer = R::findOne($data['type'], 'id = ?', [$data['id']]);
            $answer->hidden = $data['hidden'];
        }

        $answer->name = trim($data['name']);
        $answer->email = trim($data['email']);
        $answer->question = trim($data['question']);
        $answer->answers = trim($data['answers']);
        $answer->category = $data['category'];
        $answer->time = $data['time'];
        R::store($answer);
        redirect('?/admin');
    }

    /**
     *  Проверка авторизации
     */
    private function sessionCheck()
    {
        if (!isset($_SESSION['adminId'])) {
            redirect('?/admin/login');
        }
    }

    /**
     * ======================================================
     * Метод login
     *
     *  - Проверяет валидность данных для авторизации
     *  - Записывает в сессию логин и id администратора
     *
     *  - Возвращает массив с данными
     * ======================================================
     */
    protected function login()
    {
        if (isset($_SESSION['adminId'])) {
            redirect('?/admin');
        }

        $errors = [];
        $data = $_POST;

        if (isset($data['goLogin'])) {

            if (trim($data['loginLog']) === '') {
                $errors[] = 'Введите логин';
            }
            if (trim($data['passwordLog']) === '') {
                $errors[] = 'Введите пароль';
            }

            $admin = R::findOne('admin', 'login = ?', [trim($data['loginLog'])]);

            if ($admin === null) {
                $errors[] = 'Неверный логин или пароль';
            }

            if (!password_verify(trim($data['passwordLog']), $admin['password'])) {
                $errors[] = 'Неверный логин или пароль';
            }

            if (empty($errors)) {
                $_SESSION['adminLogin'] = $admin['login'];
                $_SESSION['adminId'] = $admin['id'];
                redirect('?/admin');
            }
        }
        return [
            'header' => [
                'title' => 'Панель администратора'
            ],
            'data' => [
                'header' => 'Авторизация',
                'error' => @array_shift($errors),
                'data' => $data
            ]
        ];
    }

    /**
     * ======================================================
     * Метод category
     *
     *  - Проверяет данные
     *  - Перезаписывает категорию
     *  - Удаляет категорию и все вопросы этой категории
     *  - Добавляет новую категорию
     *
     * ======================================================
     */
    protected function category()
    {
        $data = $_POST;
        var_dump($data);

        if ($data['title'] === '') {
            redirect('?/admin');
        }

        if ($data['action'] === 'save') {
            $categories = R::findOne('categories', 'id = ?', [$data['id']]);
            $categories->title = trim($data['title']);
            R::store($categories);
            redirect('?/admin');
        }

        if ($data['action'] === 'delete') {
            foreach (self::getQuestions() as $table => $questions) {
                foreach ($questions as $key => $question) {
                    if ($question['category'] === $data['id']) {
                        self::trashQuestion($table, $question['id']);
                    }
                }
            }
            $category = R::load('categories', $data['id']);
            R::trash($category);
            redirect('?/admin');
        }

        if ($data['action'] === 'add') {
            $categories = R::dispense('categories');
            $categories->title = trim($data['title']);
            R::store($categories);
            logAdmin('добавил категорию: ' . trim($data['title']));
            redirect('?/admin');
        }
    }

    /**
     * ======================================================
     * Метод category
     *
     *  - Проверяет данные
     *  - Перезаписывает категорию
     *  - Удаляет категорию и все вопросы этой категории
     *  - Добавляет новую категорию
     *
     * ======================================================
     */
    protected function
}