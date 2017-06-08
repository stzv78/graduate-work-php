<?php

namespace Engine\Core\Database;

use RedBeanPHP\R;

/**
 * ======================================================
 * Class Connection
 *  Запускает ORM RedBean
 *  устанавливет настройки для запуск
 * ======================================================
 */
class Connection
{
    /**
     * Connection constructor.
     */
    public function __construct()
    {
        $this->connect();
    }

    /**
     * @return $this
     */
    private function connect()
    {
        $config = require_once DATABASE_CONFIG;

        $dsn = 'mysql:host=' . $config['host'] . ';dbname=' . $config['dbName'] . ';charset=' . $config['charset'];
        R::setup($dsn, $config['userName'], $config['userPassword']);

        #R::fancyDebug(TRUE);
        R::freeze(true);

        return $this;
    }
}