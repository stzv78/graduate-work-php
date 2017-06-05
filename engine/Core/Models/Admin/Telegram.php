<?php

namespace Engine\Core\Models\Admin;

use TelegramBot\Api\BotApi;

trait Telegram
{
    /**
     * Токен телеграм-бота
     */
    private $token = '385602286:AAHi-3lKvIkXy0xEENmC1AElR-73we46kKE';

    /**
     * ======================================================
     * Метод telegramRun
     *
     *  Получает данные по api
     *  Проверяет данные
     *  Записывает данные если их нет в БД
     *
     * ======================================================
     */
    private function telegramRun()
    {
        $bot = new BotApi($this->token);

        $updates = $bot->getUpdates();

        $telegramList = self::getTelegramList(); // Query

        foreach ($updates as $key => $data) {
            $errors = [];
            $chatId = $updates[$key]->getMessage()->getChat()->getId();
            $messageId = $updates[$key]->getMessage()->getMessageId();
            $name = $updates[$key]->getMessage()->getChat()->getFirstName();
            $text = $updates[$key]->getMessage()->getText();

            foreach ($telegramList as $id => $array) {
                if ($array['chat'] == $chatId && $array['message'] == $messageId) {
                    $errors[] = 'error';
                }
            }

            if (empty($errors)) {
                self::entryTelegramMessages($chatId, $messageId, $text, $name); // Telegram
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
        $dictionary = self::getDictionary(); // Query

        self::entryTelegram($chatId, $messageId); // Query

        $words = [];
        if (!empty($dictionary)) {
            foreach ($dictionary as $id => $word) {
                if (strpos($text, $word['word']) !== false) {
                    $words[] = $word['id'];
                }
            }
            $words = implode(':', $words);
        }

        self::entryQuestion($chatId, $messageId, $name, $text, $words); // Query
    }

    /**
     * Проверяет отвечен вопрос в чат или нет
     *
     * Если нет, то отвечает и помечает как отвеченные
     * @param $chatId
     * @param $messageId
     * @param $text
     */
    private function messageTelegram($chatId, $messageId, $text)
    {
        $answer = self::getAnswer($messageId); // Query

        if ($answer == 0) {
            self::sendMessageTelegram($chatId, $text); // Telegram
            self::markMessageTelegram($messageId); // Query
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

}