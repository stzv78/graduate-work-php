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
        if (!isset($_SESSION['adminId'])) {
            redirect('?/admin/login');
            exit;
        }

        return [
            'header' => [
                'title' => 'Панель администратора'
            ],
            'data' => [
                'header' => 'Привет ' . $_SESSION['adminLogin'] . '!'
            ]
        ];
    }

    private function login()
    {
        if (isset($_SESSION['adminId'])) {
            redirect('?/admin');
            exit;
        }

        $errors = [];
        $data = $_POST;

        if (isset($data['goLogin'])) {

            if ($data['loginLog'] === '') {
                $errors[] = 'Введите логин';
            }
            if ($data['passwordLog'] === '') {
                $errors[] = 'Введите пароль';
            }

            $admin = $dictionary = R::findOne('admin', 'login = ?', [$data['loginLog']]);

            if ($admin === null) {
                $errors[] = 'Неверный логин или пароль';
            }

            if (!password_verify($data['passwordLog'], $admin['password'])) {
                $errors[] = 'Неверный логин или пароль';
            }

            if (empty($errors)) {
                $_SESSION['adminLogin'] = $admin['login'];
                $_SESSION['adminId'] = $admin['id'];
                redirect('?/admin');
                exit;
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