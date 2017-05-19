<?php

namespace Engine\Core\Database;

use \PDO;

/**
 * Class Connection
 * @package Engine\Core\Database
 */
class Connection
{
    /**
     * @var
     */
    private $link;


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
        $this->link = new PDO($dsn, $config['userName'], $config['userPassword'], [
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION

        ]);
        return $this;
    }

    /**
     * @param $sql
     * @param $array
     * @return mixed
     */
    public function execute($sql, $array = [])
    {
        $sth = $this->link->prepare($sql);
        return $sth->execute($array);
    }

    /**
     * @param $sql
     * @return array
     */
    public function query($sql, $array = [])
    {
        $sth = $this->link->prepare($sql);
        $sth->execute($array);
        $result = $sth->fetchALL(PDO::FETCH_ASSOC);
        if ($result === false) {
            return [];
        }
        return $result;
    }
}