<?php

namespace Engine\Core\Models\Admin;


use Engine\Core\Models\Models;

class Model extends Models
{
    public function getData()
    {
        return [
            'header' => [
                'title' => 'Панель администратора'
            ],
            'data' => []
        ];
    }
}