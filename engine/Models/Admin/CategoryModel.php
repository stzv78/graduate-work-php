<?php

namespace Engine\Models\Admin;

use Engine\Core\ParentModel\Model;
use RedBeanPHP\R;

class CategoryModel extends Model
{
    /**
     * Трейт с методами запросов к БД
     */
    use Query;

    public function methodCall($method, $data = [])
    {
        if (empty($data)) {
            $array = self::$method();
        } else {
            $array = self::$method($data);
        }

        return $array;
    }

    /**
     * ======================================================
     * Метод category
     *
     *  - Перезаписывает категорию
     *  - Удаляет категорию и все вопросы этой категории
     *  - Добавляет новую категорию
     *
     * @param $data
     * ======================================================
     */
    protected function category($data)
    {
        if ($data['action'] === 'delete') {
            self::actionDeleteCategory($data);
        }

        if ($data['action'] === 'save') {
            self::actionSaveCategory($data);
        }

        if ($data['action'] === 'add') {
            self::actionAddCategory($data);
        }
    }

    private function actionDeleteCategory($data)
    {
        logAdmin('удалил категорию: ' . trim($data['title']));
        self::trashCategory($data);
    }

    private function actionSaveCategory($data)
    {
        logAdmin('обновил (id:' . $data['id'] . ') категорию: ' . trim($data['title']));
        self::entryCategory($data);
    }

    private function actionAddCategory($data)
    {
        logAdmin('добавил категорию: ' . trim($data['title']));
        self::entryCategory($data);
    }

    /**
     * Возращает массив с ключевыми словами
     * @return array
     */
    private function entryCategory($data)
    {
        if ($data['action'] === 'save') {
            $categories = R::findOne('categories', 'id = ?', [$data['id']]);
        } else {
            $categories = R::dispense('categories');
        }

        $categories->title = trim($data['title']);
        R::store($categories);
        redirect('?/admin');
    }

    /**
     * Удаляет категорию и все вопросы в ней
     */
    private function trashCategory($data)
    {
        foreach (self::getQuestions() as $table => $questions) {
            foreach ($questions as $key => $question) {
                if ($question['category'] === $data['id']) {
                    self::trashQuestion($table, $question['id']);
                }
            }
        }
        $category = R::load('categories', $data['id']);
        R::trash($category);
        redirect('?/admin');
    }
}