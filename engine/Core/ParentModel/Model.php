<?php

namespace Engine\Core\ParentModel;

/**
 * ======================================================
 * Class Models
 *  Родительский класс моделей
 *
 * ======================================================
 */
abstract class Model
{

    /**
     * Запускает методы по запросу контроллера
     * @param $method
     * @return mixed
     */
    abstract public function getData($method, $data);

    /**
     * Index-ный метод модели для сбора дынных и их возврата для рендеринга
     * @return mixed
     */
    abstract protected function index();
}