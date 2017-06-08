<?php

namespace Engine\Controllers\Main;


trait Errors
{
    /**
     * Проверяет данные вопроса
     * @param $data
     * @return array
     */
    private function checkDataQuestion($data)
    {
        $errors = [];

        if ($data['nameUser'] === '') {

            $errors[] = 'Введите имя';
        }

        if ($data['emailUser'] === '') {

            $errors[] = 'Введите E-mail';
        }

        if ($data['categoryUser'] === '0') {

            $errors[] = 'Выберите категорию';
        }

        if ($data['questionUser'] === '') {

            $errors[] = 'Введите вопрос';
        }

        if (mb_strlen($data['questionUser']) >= 250) {

            $errors[] = 'В тексте должно быть меньше 250 сиволов';
        }

        return $errors;
    }
}