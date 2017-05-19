<?php

namespace Engine\Controllers\Main;

use Engine\Controllers\Controllers;

/**
 * Class Controllers
 * @package Engine\Controllers
 */
class Controller extends Controllers
{
    public function actionIndex()
    {
        $array = [
            'header' => [
                'title' => 'F.A.Q.'
            ],
            'data' => []
        ];
        $this->view->render('index', $array);
    }
}