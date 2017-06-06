<?php

namespace Engine\Models\Admin;

use Engine\Core\ParentModel\Model;
use RedBeanPHP\R;

class AdminModel extends Model
{
    public function methodCall($method, $data = [])
    {
        if (empty($data)) {
            $array = self::$method();
        } else {
            $array = self::$method($data);
        }

        return $array;
    }

    /**
     * ======================================================
     * Метод admin
     *
     *  - Работает с администраторами
     *
     *    - Обновляет данные
     *    - Удаляет
     *    - Создаёт
     *
     *  - Логирует
     *
     * @param $data
     * ======================================================
     */
    protected function admin($data)
    {
        if ($data['action'] === 'delete') {
            self::trashAdmin($data);
        }

        if ($data['action'] === 'save') {
            self::actionSaveAdmin($data);
        }

        if ($data['action'] === 'add') {
            self::actionAddAdmin($data);
        }
    }

    private function actionSaveAdmin($data)
    {
        logAdmin('обновил администратора (id:' . $data['id'] . ')');
        self::refreshAdmin($data);
    }

    private function actionAddAdmin($data)
    {
        logAdmin('добавил администратора: ' . $data['login']);
        self::entryAdmin($data);
    }

    /**
     * Удаляет администратора
     */
    private function trashAdmin($data)
    {
        $admin = R::load('admin', $data['id']);
        logAdmin('удалил администратора: ' . $admin['login']);
        R::trash($admin);
        redirect('?/admin');
    }

    /**
     * Обновляет администратора
     */
    private function refreshAdmin($data)
    {
        $admin = R::findOne('admin', 'id = ?', [$data['id']]);
        if (trim($data['login']) !== '') {
            $admin->login = trim($data['login']);
        }
        if (trim($data['password']) !== '') {
            $admin->password = password_hash(trim($data['password']), PASSWORD_DEFAULT);
        }
        if ($_SESSION['adminId'] === $data['id']) {
            session_destroy();
        }
        R::store($admin);

        redirect('?/admin');
    }

    /**
     * Записывает нового администратора
     */
    private function entryAdmin($data)
    {
        $admin = R::dispense('admin');
        $admin->login = $data['login'];
        $admin->password = password_hash(trim($data['password']), PASSWORD_DEFAULT);
        R::store($admin);
        redirect('?/admin');
    }

    /**
     * Возращает администратора
     * @param $login
     * @return \RedBeanPHP\OODBBean
     */
    public function getAdmin($login)
    {
        $admin = R::findOne('admin', 'login = ?', [trim($login)]);

        return $admin;
    }

    /**
     * Возращает массив с администраторами
     * @return array
     */
    public function getAdmins()
    {
        $admin = R::getAll('SELECT id,login,password FROM admin');
        return $admin;
    }
}
