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
        return [
            'header' => [
                'title' => 'Панель администратора'
            ],
            'data' => [
                'header' => 'Привет админ!'
            ]
        ];
    }
}