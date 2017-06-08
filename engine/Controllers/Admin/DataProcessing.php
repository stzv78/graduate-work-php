<?php

namespace Engine\Controllers\Admin;

use Engine\Core\Response\Response;

trait DataProcessing
{
    /**
     *  Проверка авторизации
     */
    private function sessionCheck()
    {
        if (!isset($_SESSION['adminId'])) {

            $action = '';

            if (defined('ACTION')) {

                $action = ACTION;
            }

            if ($action !== 'login') {

                Response::redirect('?/admin/login');
            }
        }
    }

    /**
     *  Обработка словаря для вывода в форму
     */
    private function dictionaryProcessing()
    {
        $dictionary = $this->model['question']->getDictionary();
        $words = [];

        foreach ($dictionary as $key => $word) {

            $words[] = $word['word'];
        }

        $words = implode(", ", $words);

        return $words;
    }

    /**
     * Обрабатывает массив категорий для вывода дынных о количестве вопросов в каждой категории
     * @param $questions
     * @return mixed
     */
    private function categoriesProcessing($questions)
    {
        $categories = $this->model['question']->getCategories();

        foreach ($categories as $key => $category) {

            foreach ($questions as $table => $array) {

                foreach ($array as $id => $question) {

                    $amount = isset($categories[$key][$table]) ? $amount : 0;
                    $categories[$key][$table] = $amount;

                    $boolean = $category['id'] === $question['category'];

                    if ($boolean) {

                        $amount++;
                        $categories[$key][$table] = $amount;
                    }

                    $amHidden = isset($categories[$key]['hidden']) ? $amHidden : 0;
                    $categories[$key]['hidden'] = $amHidden;

                    $booleanHidden = $table == 'answer' && $question['hidden'] == 1 && $boolean;

                    if ($booleanHidden) {

                        $amHidden++;
                        $categories[$key]['hidden'] = $amHidden;
                    }
                }
            }
        }

        return $categories;
    }

    /**
     * Выбирает данные для вывода в форму
     * @param $data
     * @param $question
     * @return mixed
     */
    private function questionProcessing($question, $data)
    {
        foreach ($question as $key => $query) {

            if (isset($data['updateQuestion'])) {

                $question = $data;
            } else {

                $question = $query;
                $question['type'] = $data['type'];
            }
            continue;
        }

        return $question;
    }
}