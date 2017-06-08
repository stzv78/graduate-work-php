<?php

namespace Engine\Models\Admin;

use RedBeanPHP\R;

trait Query
{
    /**
     * Возращает массив с категориями
     * @return array
     */
    public function getCategories()
    {
        $categories = R::getAll('SELECT id,title FROM categories');

        return $categories;
    }

    /**
     * Возращает строку с категорией запрошенную по id
     * @param $id
     * @return array
     */
    public function getCategory($id)
    {
        $category = R::getAll('SELECT title FROM categories WHERE id LIKE :id', [
            'id' => $id
        ]);

        return $category[0]['title'];
    }

    /**
     * Возращает массив с ключевыми словами
     * @return array
     */
    public function getDictionary()
    {
        $dictionary = R::getAll('SELECT id,word FROM dictionary');

        return $dictionary;
    }

    /**
     * Ищет вопрос по ID в таблице
     * @param $table
     * @param $id
     * @return array
     */
    public function getQuestion($table, $id)
    {
        if ($table === 'answer') {

            $сolumns = 'id,name,email,question,answers,category,time,hidden';

        } elseif ($table === 'blocked') {

            $сolumns = 'id,words,name,email,question,category,time';

        } else {

            $сolumns = 'id,name,email,question,category,time';
        }

        $question = R::getAll('SELECT ' . $сolumns . ' FROM ' . $table . ' WHERE id LIKE :id', [
            'id' => $id
        ]);

        return $question;
    }

    /**
     * Возращает массив с вопросами
     * @return mixed
     */
    public function getQuestions()
    {
        $questions['unanswered'] = R::getAll('SELECT id,name,email,question,category,time FROM unanswered');

        foreach ($questions['unanswered'] as $data) {

            $data['question'] = htmlspecialchars($data['question'], ENT_QUOTES);
        }

        $questions['blocked'] = R::getAll('SELECT id,words,name,email,question,category,time FROM blocked');

        foreach ($questions['blocked'] as $data) {

            $data['question'] = htmlspecialchars($data['question'], ENT_QUOTES);
        }

        $questions['answer'] = R::getAll('SELECT id,name,email,question,answers,category,time,hidden FROM answer');

        return $questions;
    }

    /**
     * Удаляет вопрос
     * @param $table
     * @param $id
     */
    private function trashQuestion($table, $id)
    {
        $question = R::load($table, $id);

        R::trash($question);
    }
}