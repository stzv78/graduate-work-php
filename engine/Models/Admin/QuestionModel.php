<?php

namespace Engine\Models\Admin;

use Engine\Core\ParentModel\Model;
use Engine\Core\Response\Response;
use Engine\Core\Logging\Logging;
use RedBeanPHP\R;

class QuestionModel extends Model
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
     * Метод updateQuestion
     * Отправляет записывает вопрос в БД и логирует
     * Логирует дествие
     *
     * @param $data
     */
    protected function updateQuestion($data)
    {
        if (empty($errors)) {

            if ($data['type'] !== 'answer') {

                $category = self::getCategory($data['category']);
                Logging::logAdmin('ответил на вопрос: ' . $data['type'] . ' из категории: ' . $category);

            } else {

                $category = self::getCategory($data['category']);
                Logging::logAdmin('обновил вопрос: (id:' . $data['id'] . ') из категории: ' . $category);
            }

            self::createAnswer($data);
        }
    }

    /**
     * Удаляет вопрос без ответа и создаёт вопрос с ответом
     * Или обновляет вопрос с ответом
     * @param $data
     */
    private function createAnswer($data)
    {
        if ($data['type'] !== 'answer') {

            self::trashQuestion($data['type'], $data['id']); // Query
            $answer = R::dispense('answer');
            $answer->hidden = $data['hidden'];

        } else {

            $answer = R::findOne($data['type'], 'id = ?', [$data['id']]);
            $answer->hidden = $data['hidden'];
        }

        $answer->name     = trim($data['name']);
        $answer->email    = trim($data['email']);
        $answer->question = trim($data['question']);
        $answer->answers  = trim($data['answers']);
        $answer->category = $data['category'];
        $answer->time     = $data['time'];

        R::store($answer);

        Response::redirect('?/admin');
    }

    /**
     * Выполняет действия с вопросом по запросу
     * Логирует дествие
     * @param $data
     */
    protected function actionQuestion($data)
    {
        $question = $data['objectQuestion'];

        if ($data['action'] === 'delete') {

            self::actionDeleteQuestion($question, $data);
        }

        self::actionOpenQuestion($question, $data);

        self::createAnswer($question);
    }

    private function actionDeleteQuestion($question, $data)
    {
        if ($question['category'] != 0) {

            $category = self::getCategory($question['category']);

        } else {

            $category = 'Сообщения из телеграма';
        }

        Logging::logAdmin('удалил вопрос: ' . $data['type'] . ' из категории: ' . $category);

        self::trashQuestion($data['type'], $data['id']); // Query

        Response::redirect('?/admin');
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

        $category = self::getCategory($question['category']);

        Logging::logAdmin($action . 'вопрос (id:' . $data['id'] . ') из категории: ' . $category);
    }

    /**
     * Метод dictionary
     *
     *  - Работает со словарем ключевых слов
     *
     * @param $data
     */
    protected function dictionary($data)
    {
        Logging::logAdmin('обновил словарь');

        self::refreshDictionary($data);
    }

    /**
     * Обновляет словарь
     * @param $data
     */
    private function refreshDictionary($data)
    {
        $data['dictionary'] = trim($data['dictionary']);

        $getDictionary = R::getAll('SELECT * FROM dictionary');;
        foreach ($getDictionary as $key => $word) {

            if (strpos($data['dictionary'], $word['word']) === false) {

                $deleteWord = R::load('dictionary', $word['id']);

                R::trash($deleteWord);
            }
        }

        $words = explode(', ', $data['dictionary']);

        foreach ($words as $key => $word) {

            $getWord = R::getAll('SELECT word FROM dictionary WHERE word = ?', [$word]);

            if (empty($getWord)) {

                $dictionary = R::dispense('dictionary');
                $dictionary->word = $word;
                R::store($dictionary);
            }
        }

        Response::redirect('?/admin');
    }
}