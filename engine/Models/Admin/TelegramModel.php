<?php

namespace Engine\Models\Admin;

use Engine\Core\ParentModel\Model;
use TelegramBot\Api\BotApi;
use RedBeanPHP\R;

Class TelegramModel extends Model
{
    /**
     * Трейт с запросами
     */
    use Query;
    /**
     * Токен телеграм-бота
     */
    private $token = '385602286:AAHi-3lKvIkXy0xEENmC1AElR-73we46kKE';

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
     * Метод telegramRun
     *
     *  Получает данные по api
     *  Проверяет данные
     *  Записывает данные если их нет в БД
     */
    public function telegramRun()
    {
        $bot = new BotApi($this->token);

        $updates = $bot->getUpdates();

        $telegramList = self::getTelegramList();

        foreach ($updates as $key => $data) {

            $errors    = [];
            $chatId    = $updates[$key]->getMessage()->getChat()->getId();
            $messageId = $updates[$key]->getMessage()->getMessageId();
            $name      = $updates[$key]->getMessage()->getChat()->getFirstName();
            $text      = $updates[$key]->getMessage()->getText();

            foreach ($telegramList as $id => $array) {

                if ($array['chat'] == $chatId && $array['message'] == $messageId) {

                    $errors[] = 'error';
                }
            }

            if (empty($errors)) {

                self::entryTelegramMessages($chatId, $messageId, $text, $name);
            }
        }
    }

    /**
     * Записывает новые вопросы из телеграма в БД
     * @param $chatId
     * @param $messageId
     * @param $text
     * @param $name
     */
    private function entryTelegramMessages($chatId, $messageId, $text, $name)
    {
        $dictionary = self::getDictionary();

        self::entryTelegram($chatId, $messageId);

        $words = [];

        if (!empty($dictionary)) {

            foreach ($dictionary as $id => $word) {

                if (strpos($text, $word['word']) !== false) {

                    $words[] = $word['id'];
                }
            }

            $words = implode(':', $words);
        }

        self::entryQuestion($chatId, $messageId, $name, $text, $words);
    }

    /**
     * Проверяет отвечен вопрос в чат или нет
     *
     * Если нет, то отвечает и помечает как отвеченные
     * @param $chatId
     * @param $messageId
     * @param $text
     */
    public function messageTelegram($chatId, $messageId, $text)
    {
        $answer = self::getAnswer($messageId);

        if ($answer == 0) {

            self::sendMessageTelegram($chatId, $text);
            self::markMessageTelegram($messageId);
        }
    }


    /**
     * Отправляет сообщение
     * @param $chatId
     * @param $text
     */
    private function sendMessageTelegram($chatId, $text)
    {
        $bot = new BotApi($this->token);
        $bot->sendMessage($chatId, $text);
    }

    /**
     * Возращает массив с записанными вопросами из телеграма
     * @return array
     */
    private function getTelegramList()
    {
        $telegramList = R::getAll('SELECT id,chat,message,answer FROM telegram');

        return $telegramList;
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

    /**
     * Записывает вопрос из телегрмма
     * @param $chatId
     * @param $messageId
     * @param $text
     * @param $name
     * @param $words
     */
    private function entryQuestion($chatId, $messageId, $name, $text, $words)
    {
        if (empty($words)) {
            $question = R::dispense('unanswered');
        } else {
            $question = R::dispense('blocked');
            $question->words = $words;
        }

        $question->name     = $name;
        $question->email    = 'telegram:' . $chatId . ':' . $messageId;
        $question->question = $text;
        $question->category = 0;
        $question->time     = R::isoDateTime();

        R::store($question);
    }

    /**
     * Записывает данные о вопросе из телеграма
     * @param $chatId
     * @param $messageId
     */
    private function entryTelegram($chatId, $messageId)
    {
        $telegram = R::dispense('telegram');

        $telegram->chat    = $chatId;
        $telegram->message = $messageId;
        $telegram->answer  = 0;

        R::store($telegram);
    }
}