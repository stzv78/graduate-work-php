<?php

namespace Engine\Controllers\User;

use Engine\Controllers\Controllers;
use Engine\Core\Models\User\User;

/**
 * Class Controller
 * @package Engine\Controllers\User
 */
class Controller extends Controllers
{
    /**
     * Подключение Engine\Core\Models\User\User
     */
    protected function setModel()
    {
        $model = new User();
        $this->model = $model;
    }

    /**
     * @param $db
     * @return mixed
     */
    public function signup($db)
    {
        if (isset($_POST["go_regist"])) {
            return $this->model->signup($db);
        }
    }

    /**
     * @param $db
     * @return mixed
     */
    public function login($db)
    {
        if (isset($_POST["go_login"])) {
            return $this->model->login($db);
        }
    }

    /**
     * Engine\Core\Models\User\User::logout()
     */
    public function logout()
    {
        if (isset($_POST['logout'])) {
            $this->model->logout();
        }
    }
}