<?php

namespace Engine\Models\Main;

use Engine\Core\ParentModel\Model;
use RedBeanPHP\R;

class MainModel extends Model
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

    public function getQuestion()
    {
        $questions = R::getAll('SELECT id,name,email,question,answers,category,time,hidden FROM answer');

        return $questions;
    }

    public function getCategories()
    {
        $categories = R::getAll('SELECT id,title FROM categories');

        return $categories;
    }

    /**
     * Записывает вопрос в БД
     * @param $data
     */
    protected function questionRecord($data)
    {
        $dictionary = R::getAll('SELECT id,word FROM dictionary');

        $words = [];

        if (!empty($dictionary)) {

            foreach ($dictionary as $id => $word) {

                if (strpos($data['questionUser'], $word['word']) !== false) {

                    $words[] = $word['id'];
                }
            }
        }

        $words = implode(':', $words);

        if (empty($words)) {

            $question = R::dispense('unanswered');

        } else {

            $question = R::dispense('blocked');
            $question->words = $words;
        }

        $question->name     = trim($data['nameUser']);
        $question->email    = trim($data['emailUser']);
        $question->question = trim($data['questionUser']);
        $question->category = $data['categoryUser'];
        $question->time     = R::isoDateTime();

        R::store($question);
    }
}