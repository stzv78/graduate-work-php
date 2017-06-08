<?php

namespace Engine\Controllers\Admin;

use Engine\Core\Response\Response;

trait Errors
{
    /**
     * Проверяет данные вопроса и возвращает массив с ошибками
     * @param $data
     * @return array
     */
    private function checkDataQuestion($data)
    {
        $errors = [];

        if (trim($data['name']) === '') {

            $errors[] = 'Заполните имя';
        }

        if (trim($data['email']) === '') {

            $errors[] = 'Заполните E-mail';
        }

        if (trim($data['question']) === '') {

            $errors[] = 'Напишите вопрос';
        }

        if (mb_strlen(trim($data['question'])) >= 250) {

            $errors[] = 'В тексте вопроса должно быть меньше 250 сиволов';
        }

        if (trim($data['answers']) === '') {

            $errors[] = 'Напишите ответ';
        }

        if ($data['category'] === '0') {

            $errors[] = 'Выбирите категорию';
        }

        return $errors;
    }

    /**
     * Проверяет данные для авторизации и возвращает массив с ошибками
     *
     * @param $data
     * @param $admin
     * @return array
     */
    private function checkDataLogin($data, $admin)
    {
        $errors = [];

        if (trim($data['loginLog']) === '') {

            $errors[] = 'Введите логин';
        }

        if (trim($data['passwordLog']) === '') {

            $errors[] = 'Введите пароль';
        }

        if ($admin === null) {

            $errors[] = 'Неверный логин или пароль';
        }

        if (!password_verify(trim($data['passwordLog']), $admin['password'])) {

            $errors[] = 'Неверный логин или пароль';
        }

        return $errors;
    }

    /**
     * Проверяет данные для регистрации нового администратора
     * @param $data
     */
    private function checkAdminData($data)
    {
        if (trim($data['login']) === '') {

            Response::redirect('?/admin');
        }

        if (trim($data['password']) === '') {

            Response::redirect('?/admin');
        }

        if (!(preg_match('/^[a-zA-Z0-9]+$/', trim($data['login'])))) {

            Response::redirect('?/admin');
        }

        if (!(preg_match('/^[a-zA-Z0-9]+$/', trim($data['password'])))) {

            Response::redirect('?/admin');
        }

        if (!empty($this->model['admin']->getAdmin($data['login']))) {

            Response::redirect('?/admin');
        }
    }
}