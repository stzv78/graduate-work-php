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
     * Запускает метод по запросу контроллера
     * @param $method
     * @param $data
     * @return mixed
     */
    abstract public function methodCall($method, $data = []);

}