<?php

namespace Engine\Core\Models;

/**
 * ======================================================
 * Class Models
 *  Родительский класс моделей
 *
 * ======================================================
 */
abstract class Models
{

    /**
     * Запускает методы по запросу контроллера
     * @param $method
     * @return mixed
     */
    abstract public function getData($method);

    /**
     * Index-ный метод модели для сбора дынных и их возврата для рендеринга
     * @return mixed
     */
    abstract protected function index();
}