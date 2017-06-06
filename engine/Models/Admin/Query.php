<?php

namespace Engine\Models\Admin;

use RedBeanPHP\R;

trait Query
{
    /**
     * Возращает массив с вопросами
     * @return mixed
     */
    private function getQuestions()
    {
        $questions['unanswered'] = R::getAll('SELECT * FROM unanswered');
        foreach ($questions['unanswered'] as $data) {
            $data['question'] = htmlspecialchars($data['question'], ENT_QUOTES);
        }
        $questions['blocked'] = R::getAll('SELECT * FROM blocked');
        foreach ($questions['blocked'] as $data) {
            $data['question'] = htmlspecialchars($data['question'], ENT_QUOTES);
        }
        $questions['answer'] = R::getAll('SELECT * FROM answer');
        return $questions;
    }

    /**
     * Возращает массив с категориями
     * @return array
     */
    private function getCategories()
    {
        $categories = R::getAll('SELECT * FROM categories');
        return $categories;
    }

    /**
     * Возращает строку с категорией запрошенную по id
     * @param $id
     * @return array
     */
    private function getCategory($id)
    {
        $category = R::getAll('SELECT title FROM categories WHERE id LIKE :id', [
            'id' => $id
        ]);
        return $category[0]['title'];
    }

    /**
     * Возращает массив с администраторами
     * @return array
     */
    private function getAdmins()
    {
        $admin = R::getAll('SELECT * FROM admin');
        return $admin;
    }

    /**
     * Возращает массив с ключевыми словами
     * @return array
     */
    private function getDictionary()
    {
        $dictionary = R::getAll('SELECT * FROM dictionary');

        return $dictionary;
    }

    /**
     * Ищет вопрос по ID в таблице
     * @param $table
     * @param $id
     * @return array
     */
    private function getQuestion($table, $id)
    {
        $question = R::getAll('SELECT * FROM ' . $table . ' WHERE id LIKE :id', [
            'id' => $id
        ]);

        return $question;
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

    /**
     * Удаляет вопрос без ответа и создаёт вопрос с ответом
     * Или обновляет вопрос с ответом
     * @param $data
     */
    private function createAnswer($data)
    {
        if (strpos($data['email'], 'telegram') !== false) {
            $telegram = explode(':', $data['email']);
            self::messageTelegram($telegram[1], $telegram[2], trim($data['answers'])); // Telegram
        }
        if ($data['type'] !== 'answer') {
            self::trashQuestion($data['type'], $data['id']); // Query
            $answer = R::dispense('answer');
            $answer->hidden = $data['hidden'];
        } else {
            $answer = R::findOne($data['type'], 'id = ?', [$data['id']]);
            $answer->hidden = $data['hidden'];
        }

        $answer->name = trim($data['name']);
        $answer->email = trim($data['email']);
        $answer->question = trim($data['question']);
        $answer->answers = trim($data['answers']);
        $answer->category = $data['category'];
        $answer->time = $data['time'];
        R::store($answer);
        redirect('?/admin');
    }


    /**
     * Возращает администратора
     * @param $login
     * @return \RedBeanPHP\OODBBean
     */
    private function getAdmin($login)
    {
        $admin = R::findOne('admin', 'login = ?', [trim($login)]);

        return $admin;
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
        foreach (self::getQuestions() as $table => $questions) {    // Query
            foreach ($questions as $key => $question) {
                if ($question['category'] === $data['id']) {
                    self::trashQuestion($table, $question['id']);   // Query
                }
            }
        }
        $category = R::load('categories', $data['id']);
        R::trash($category);
        redirect('?/admin');
    }

    /**
     * Обновляет словарь
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

        redirect('?/admin');
    }

    /**
     * Удаляет администратора
     */
    private function trashAdmin($data)
    {
        $admin = R::load('admin', $data['id']);
        logAdmin('удалил администратора: ' . $admin['login']);
        R::trash($admin);
        redirect('?/admin');
    }

    /**
     * Обновляет администратора
     */
    private function refreshAdmin($data)
    {
        $admin = R::findOne('admin', 'id = ?', [$data['id']]);
        if (trim($data['login']) !== '') {
            $admin->login = trim($data['login']);
        }
        if (trim($data['password']) !== '') {
            $admin->password = password_hash(trim($data['password']), PASSWORD_DEFAULT);
        }
        if ($_SESSION['adminId'] === $data['id']) {
            session_destroy();
        }
        R::store($admin);

        redirect('?/admin');
    }

    /**
     * Записывает нового администратора
     */
    private function entryAdmin($data)
    {
        $admin = R::dispense('admin');
        $admin->login = $data['login'];
        $admin->password = password_hash(trim($data['password']), PASSWORD_DEFAULT);
        R::store($admin);
        redirect('?/admin');
    }

    /**
     * Возращает массив с записанными вопросами из телеграма
     * @return array
     */
    private function getTelegramList()
    {
        $telegramList = R::getAll('SELECT * FROM telegram');

        return $telegramList;
    }

    /**
     * Записывает данные о вопросе из телеграма
     */
    private function entryTelegram($chatId, $messageId)
    {
        $telegram = R::dispense('telegram');
        $telegram->chat = $chatId;
        $telegram->message = $messageId;
        $telegram->answer = 0;
        R::store($telegram);
    }

    /**
     * Записывает вопрос из телегрмма
     */
    private function entryQuestion($chatId, $messageId, $name, $text, $words)
    {
        if (empty($words)) {
            $question = R::dispense('unanswered');
        } else {
            $question = R::dispense('blocked');
            $question->words = $words;
        }
        $question->name = $name;
        $question->email = 'telegram:' . $chatId . ':' . $messageId;
        $question->question = $text;
        $question->category = 0;
        $question->time = R::isoDateTime();
        R::store($question);
    }

    /**
     * Возвращает статус вопроса из телеграмма
     * @param $messageId
     * @return mixed
     */
    private function getAnswer($messageId)
    {
        $telegram = R::findOne('telegram', 'message = ?', [$messageId]);
        $answer = $telegram->getProperties()['answer'];

        return $answer;
    }

    /**
     * Помечает вопрос из телеграмма как отвеченный
     * @param $messageId
     */
    private function markMessageTelegram($messageId)
    {
        $telegram = R::findOne('telegram', 'message = ?', [$messageId]);
        $telegram->answer = 1;
        R::store($telegram);
    }
}