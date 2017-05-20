<?php

namespace Engine\Core\Models\Main;

use Engine\Core\Models\Models;

class Model extends Models
{
    public function getData()
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
}