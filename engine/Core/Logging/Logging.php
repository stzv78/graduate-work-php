<?php

namespace Engine\Core\Logging;


class Logging
{
    public static function logAdmin($action)
    {
        $file = ROOT_DIR . 'log/log.txt';
        $time = date("Y-m-d H:i:s");
        $admin = $_SESSION['adminLogin'];
        $id = $_SESSION['adminId'];

        $log = "[$time] $admin(id:$id) $action \n";

        file_put_contents($file, $log, FILE_APPEND | LOCK_EX);
    }
}