<?php

namespace Engine\Models\Admin;


trait DataProcessing
{
    /**
     *  Проверка авторизации
     */
    private function sessionCheck()
    {
        if (!isset($_SESSION['adminId'])) {
            redirect('?/admin/login');
        }
    }

    /**
     *  Обработка словаря для вывода в форму
     */
    private function dictionaryProcessing()
    {
        $dictionary = self::getDictionary(); // Query
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
        $categories = self::getCategories(); // Query

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
     * Отправляет записывает вопрос в БД и логирует
     * Логирует дествие
     * @param $data
     */
    private function updateQuestion($data)
    {
        if (empty($errors)) {
            if ($data['type'] !== 'answer') {
                $category = self::getCategory($data['category']); // Query
                logAdmin('ответил на вопрос: ' . $data['type'] . ' из категории: ' . $category);
            } else {
                $category = self::getCategory($data['category']); // Query
                logAdmin('обновил вопрос: (id:' . $data['id'] . ') из категории: ' . $category);
            }
            self::createAnswer($data); // Query
        }
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

    /**
     * Выполняет действия с вопросом по запросу
     * Логирует дествие
     * @param $data
     * @param $question
     */
    private function actionQuestion($question, $data)
    {
        if ($data['action'] === 'delete') {
            self::actionDeleteQuestion($question, $data); // DataProcessing
        }

        self::actionOpenQuestion($question, $data);       // DataProcessing

        self::createAnswer($question);                    // Query
    }

    private function actionDeleteQuestion($question, $data)
    {
        if ($question['category'] != 0) {
            $category = self::getCategory($question['category']); // Query
        } else {
            $category = 'Сообщения из телеграма';
        }
        logAdmin('удалил вопрос: ' . $data['type'] . ' из категории: ' . $category);
        self::trashQuestion($data['type'], $data['id']); // Query
        redirect('?/admin');
    }

    private function actionOpenQuestion($question, $data)
    {
        if ($data['action'] === 'open') {
            $question['hidden'] = 0;
            $action = 'открыл ';
        } else {
            $question['hidden'] = 1;
            $action = 'скрыл ';
        }
        $category = self::getCategory($question['category']); // Query
        logAdmin($action . 'вопрос (id:' . $data['id'] . ') из категории: ' . $category);
    }

    /**
     * Выполняет действия с категорией по запросу
     * Логирует дествие
     * @param $data
     */
    private function actionCategory($data)
    {
        if ($data['action'] === 'delete') {
            self::actionDeleteCategory($data); // DataProcessing
        }

        if ($data['action'] === 'save') {
            self::actionSaveCategory($data);   // DataProcessing
        }

        if ($data['action'] === 'add') {
            self::actionAddCategory($data);    // DataProcessing
        }
    }

    private function actionDeleteCategory($data)
    {
        logAdmin('удалил категорию: ' . trim($data['title']));
        self::trashCategory($data); // Query
    }

    private function actionSaveCategory($data)
    {
        logAdmin('обновил (id:' . $data['id'] . ') категорию: ' . trim($data['title']));
        self::entryCategory($data); // Query
    }

    private function actionAddCategory($data)
    {
        logAdmin('добавил категорию: ' . trim($data['title']));
        self::entryCategory($data); // Query
    }

    /**
     * Выполняет действия со словарём по запросу
     * @param $data
     */
    private function actionDictionary($data)
    {
        logAdmin('обновил словарь');
        self::refreshDictionary($data); // Query
    }

    /**
     * Выполняет действия с администратором по запросу
     * Логирует дествие
     * @param $data
     */
    private function actionAdmins($data)
    {
        if ($data['action'] === 'delete') {
            self::trashAdmin($data);        // Query
        }

        if ($data['action'] === 'save') {
            self::actionSaveAdmin($data);   // Query
        }

        if ($data['action'] === 'add') {
            self::actionAddAdmin($data);    // Query
        }
    }

    private function actionSaveAdmin($data)
    {
        logAdmin('обновил администратора (id:' . $data['id'] . ')');
        self::refreshAdmin($data); // Query
    }

    private function actionAddAdmin($data)
    {
        self::checkAdminData($data); // Errors

        logAdmin('добавил администратора: ' . $data['login']);
        self::entryAdmin($data);     // Query
    }
}