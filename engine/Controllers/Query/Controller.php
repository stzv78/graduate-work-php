<?php

namespace Engine\Controllers\Query;

use Engine\Controllers\Controllers;
use Engine\Core\Models\Query\Query;

/**
 * Class Controller
 * @package Engine\Controllers\Query
 */
class Controller extends Controllers
{
    /**
     * Подключение Engine\Core\Models\Query\Query
     */
    protected function setModel()
    {
        $model = new Query();
        $this->model = $model;
    }

    /**
     * @param $db
     * @param $uri
     * @return mixed
     */
    public function getDataTable($db, $uri)
    {
        $param = $this->model->run($db, $uri);
        if ($param['action'] === 'save&edit') {
            $param['action'] = $param['action'] . '/' . $uri['id'];
        }
        return $param;
    }

    /**
     * Engine\Core\Models\Query\Query::create()
     */
    public function create()
    {
        $this->model->create();
    }

    /**
     * Engine\Core\Models\Query\Query::delete()
     */
    public function delete()
    {
        $this->model->delete();
    }

    /**
     * Engine\Core\Models\Query\Query::done()
     */
    public function done()
    {
        $this->model->done();
    }

    /**
     * Engine\Core\Models\Query\Query::delegation()
     */
    public function assigned()
    {
        $this->model->delegation();
    }
}