<?php

namespace Engine\Models\Admin;

use Engine\Core\ParentModel\Model;

/**
 * ======================================================
 * Class Model
 *  Контроллер Engine\Controllers\Admin\Controller
 *
 *  Методы protected вызываются контроллером
 *  Методы private вызываются моделью
 *
 * ======================================================
 */
class AdminModel extends Model
{
    /**
     * Трейт с методами проверок на ошибки
     */
    use Errors;

    /**
     * Трейт с методами запросов к БД
     */
    use Query;

    /**
     * Трейт с методами для обработки данных
     */
    use DataProcessing;

    /**
     * Трейт с методами для работы с ботом Телеграм
     */
    use Telegram;


    /**
     * Запускает метод по запросу контроллера
     * @param $method
     * @return mixed
     */
    public function getData($method, $data = '')
    {
        $array = self::$method();
        return $array;
    }


    /**
     * ======================================================
     * Index-ный метод
     *
     *  Запускает телеграм-бота
     *
     *  Собирает данные для вывода:
     *
     *  - Вопросы с трёх таблиц
     *    - unanswered без ответа*
     *    - blocked заблокированные по ключевым словам*
     *    - answer вопросы с ответом*
     *  - Список категорий
     *  - Список администраторов
     *  - Список ключевых слов
     *
     *  Возвращает массив с данными
     * ======================================================
     */
    protected function index()
    {
        self::sessionCheck();

        self::telegramRun(); // Telegram

        $questions = self::getQuestions();                      // Query
        $categories = self::categoriesProcessing($questions);   // DataProcessing
        $admins = self::getAdmins();                            // Query
        $dictionary = self::dictionaryProcessing();             // DataProcessing
        $words = self::getDictionary();                         // Query

        return [
            'header' => [
                'title' => 'Панель администратора'
            ],
            'data' => [
                'header' => 'Привет ' . $_SESSION['adminLogin'] . '!',
                'questions' => $questions,
                'categories' => $categories,
                'admins' => $admins,
                'dictionary' => $dictionary,
                'words' => $words
            ]
        ];
    }

    /**
     * ======================================================
     * Метод edit
     *
     *  - Работает с вопросом. Данные получает через $_POST.
     *  - Проверяет данные для работы с вопрос и делает запрос в базу иначе переадресовывает.
     *  - Если были обновлены данные администратором то проверяет корекнтость данных и записывает в БД.
     *  - Логирует действия администратора.
     *
     *  - Возвращает массив с данными
     * ======================================================
     */
    protected function edit()
    {
        self::sessionCheck();

        $errors = [];
        $data = $_POST;

        if (isset($data['type'])) {
            $question = self::getQuestion($data['type'], $data['id']); // Query
        } else {
            redirect('?/admin');
        }

        if (isset($data['updateQuestion'])) {
            $errors = self::checkDataQuestion($data); // Errors
            if (empty($errors)) {
                self::updateQuestion($data); // DataProcessing
            }
        }

        $question = self::questionProcessing($question, $data); // DataProcessing

        if (isset($data['action'])) {
            self::actionQuestion($question, $data); // DataProcessing
        }

        $categories = self::getCategories(); // Query

        return [
            'header' => [
                'title' => 'Панель администратора'
            ],
            'data' => [
                'header' => 'Ответить на вопрос',
                'question' => $question,
                'categories' => $categories,
                'error' => @array_shift($errors)
            ]
        ];
    }

    /**
     * ======================================================
     * Метод login
     *
     *  - Проверяет валидность данных для авторизации
     *  - Записывает в сессию логин и id администратора
     *
     *  - Возвращает массив с данными
     * ======================================================
     */
    protected function login()
    {
        if (isset($_SESSION['adminId'])) {
            redirect('?/admin');
        }

        $errors = [];
        $data = $_POST;

        if (isset($data['goLogin'])) {
            $admin = self::getAdmin($data['loginLog']);    // Query
            $errors = self::checkDataLogin($data, $admin); // Errors

            if (empty($errors)) {
                $_SESSION['adminLogin'] = $admin['login'];
                $_SESSION['adminId'] = $admin['id'];
                redirect('?/admin');
            }
        }
        return [
            'header' => [
                'title' => 'Панель администратора'
            ],
            'data' => [
                'header' => 'Авторизация',
                'error' => @array_shift($errors),
                'data' => $data
            ]
        ];
    }

    /**
     * ======================================================
     * Метод category
     *
     *  - Проверяет данные
     *  - Перезаписывает категорию
     *  - Удаляет категорию и все вопросы этой категории
     *  - Добавляет новую категорию
     *
     * ======================================================
     */
    protected function category()
    {
        $data = $_POST;

        if ($data['title'] === '') {
            redirect('?/admin');
        }
        self::actionCategory($data);  // DataProcessing
    }

    /**
     * ======================================================
     * Метод dictionary
     *
     *  - Работает со словарем ключевых слов
     *
     * ======================================================
     */
    protected function dictionary()
    {
        $data = $_POST;

        if (!isset($data['dictionary'])) {
            redirect('?/admin');
        }

        self::actionDictionary($data); // DataProcessing
    }

    /**
     * ======================================================
     * Метод admin
     *
     *  - Работает с администраторами
     *
     *    - Обновляет данные
     *    - Удаляет
     *    - Создаёт
     *
     *  - Логирует
     *
     * ======================================================
     */
    protected function admin()
    {
        $data = $_POST;

        self::actionAdmins($data); // DataProcessing
    }
}
