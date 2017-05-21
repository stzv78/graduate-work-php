<?php

namespace Engine\Core\Models\Admin;


use Engine\Core\Models\Models;
use RedBeanPHP\R;

class Model extends Models
{
    public function getData($method)
    {
        $array = self::$method();
        return $array;

    }

    private function admin()
    {
        if (isset($_SESSION['adminLog'])) {
            header('Location:/?/admin/login');
        }

        return [
            'header' => [
                'title' => 'Панель администратора'
            ],
            'data' => [
                'header' => 'Привет админ!'
            ]
        ];
    }

    private function login()
    {
        if (!isset($_SESSION['adminLog'])) {
            header('Location:/?/admin');
        }

        $errors = [];
        $data = $_POST;

        if ($data['loginLog'] === '') {
            $errors[] = 'Введите логин';
        }
        if ($data['passwordLog'] === '') {
            $errors[] = 'Введите пароль';
        }

        if (empty($errors) && isset($data['goLogin'])) {

            $admin = $dictionary = R::findOne('admin', 'login = ?', [$data['loginLog']]);

            if ($admin === null) {
                $errors[] = 'Неверный логин или пароль';
            }

            if (!password_verify($data['passwordLog'], $admin['password'])) {
                $errors[] = 'Неверный логин или пароль';
            }

            if (empty($errors)) {
                session_start();
                $_SESSION['adminLog'] = true;
                $_SESSION['login'] = $admin['login'];
                $_SESSION['id'] = $admin['id'];
                header('Locatin:Location:/?/admin');
            }
        }

        return [
            'header' => [
                'title' => 'Панель администратора'
            ],
            'data' => [
                'header' => 'Авторизация',
                'error' => array_shift($errors),
                'data' => $data
            ]
        ];
    }
}