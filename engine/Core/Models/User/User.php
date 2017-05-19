<?php

namespace Engine\Core\Models\User;

/**
 * Class User
 * @package Engine\Core\Models\User
 */
class User
{
    /**
     * @param $db
     * @return array
     */
    public function signup($db)
    {
        $data = $this->dataFiltration();
        return $this->checkupDataSignup($data, $db);
    }

    /**
     * @param $db
     * @return array
     */
    public function login($db)
    {
        $data = $this->dataFiltration();
        return $this->checkupDataLogin($data, $db);
    }

    /**
     * @return mixed
     */
    private function dataFiltration()
    {
        $data = $_POST;
        $data['name'] = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $data['login'] = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_STRING);
        $data['name'] = trim($data['name']);
        $data['login'] = trim($data['login']);

        $data['loginLog'] = filter_input(INPUT_POST, 'loginLog', FILTER_SANITIZE_STRING);
        $data['passwordLog'] = filter_input(INPUT_POST, 'passwordLog', FILTER_SANITIZE_STRING);
        $data['loginLog'] = trim($data['loginLog']);
        $data['passwordLog'] = trim($data['passwordLog']);

        return $data;
    }

    /**
     * @param $data
     * @param $db
     * @return array
     */
    private function checkupDataLogin($data, $db)
    {
        $objDataBase = $db;
        $errors = [];

        if (!empty($data["loginLog"]) && !empty($data["passwordLog"])) {
            $sqlLoginTest = "SELECT * FROM user WHERE login LIKE :login";
            $sqlLoginTestArr = ["login" => $data["loginLog"]];
            $validationUser = $objDataBase->query($sqlLoginTest, $sqlLoginTestArr);
            @$validationPass = password_verify($data["passwordLog"], $validationUser[0]["password"]);

            if (!empty($validationUser) && $validationPass) {
                $_SESSION['logUser'] = ['id' => (int)$validationUser[0]['id'], 'name' => $validationUser[0]['name']];
                redirect('?/todo');
            } else {
                $errors [] = 'Пользователя с таким логин не существует или неправельно введён пароль!';
            }
        } elseif ($data["loginLog"] === '') {
            $errors [] = 'Введите логин!';
        } elseif ($data["passwordLog"] === '') {
            $errors [] = 'Введите пароль!';
        }

        return [
            'error' => array_shift($errors),
            'login' => $data["login"]
        ];
    }

    /**
     * @param $data
     * @param $db
     * @return array
     */
    private function checkupDataSignup($data, $db)
    {
        $objDataBase = $db;
        $errors = [];

        if ($data["name"] == '') {
            $errors [] = 'Введите имя!';
        }

        if ($data["login"] == '') {
            $errors [] = 'Введите логин!';
        }

        if (isset($data['login'])) {
            if (!(preg_match('/^[a-zA-Z0-9]+$/', trim($data['login'])))) {
                $errors [] = 'Логин должен состоять только из цифр и букв латинского алфавита!';
            }
        }

        if (strlen($data['login']) < 5) {
            $errors [] = 'Логин должен состоять не менеe чем из 5 символов!';
        }

        if (isset($data["login"])) {
            $sqlLoginTest = "SELECT login FROM user WHERE login LIKE :login";
            $sqlLoginTestArr = ["login" => $data["login"] . "%"];
            if (!empty($objDataBase->query($sqlLoginTest, $sqlLoginTestArr))) {
                $errors [] = 'Пользователь с таким именем уже существует!';
            }
        }

        if ($data["password"] == '') {
            $errors [] = 'Введите пароль!';
        }

        if (trim($data["password_2"]) != trim($data["password"])) {
            $errors [] = 'Повторный пароль не совпадает с первым!';
        }

        if (empty($errors)) {
            $data['password'] = password_hash(trim($data['password']), PASSWORD_DEFAULT);
            $sqlSignUp = "INSERT INTO user (login, password, name) VALUES ( :login, :password, :name)";
            $sqlSignUpArr = [
                "login" => $data["login"],
                "password" => $data["password"],
                "name" => $data["name"]
            ];
            $objDataBase->execute($sqlSignUp, $sqlSignUpArr);
            redirect('?/login/login');
        }
        return [
            'error' => array_shift($errors),
            "login" => $data["login"],
            "name" => $data["name"]
        ];
    }

    /**
     * Уничтожает сессию
     */
    public function logout()
    {
        session_destroy();
        redirect('?/login/login');
    }
}