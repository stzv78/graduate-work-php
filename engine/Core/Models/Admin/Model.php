<?php

namespace Engine\Core\Models\Admin;


use Engine\Core\Models\Models;
use RedBeanPHP\R;

class Model extends Models
{
    public function getData()
    {
        #$user = R::load( 'user', 1 );
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