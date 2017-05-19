<?php

namespace Engine\Core\Database;

use \PDO;
use RedBeanPHP\R;

/**
 * Class Connection
 * @package Engine\Core\Database
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
        R::setup($dsn, $config['userName'], $config['userPassword'], [
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        #R::freeze(true);
        return $this;
    }
}