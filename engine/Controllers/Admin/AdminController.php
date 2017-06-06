<?php

namespace Engine\Controllers\Admin;

use Engine\Core\ParentController\Controller;
use Engine\Models\Admin\AdminModel    as Admin;
use Engine\Models\Admin\QuestionModel as Question;
use Engine\Models\Admin\CategoryModel as Category;
use Engine\Models\Admin\TelegramModel as Telegram;

/**
 * ======================================================
 * Class AdminController
 *
 *  Работа методов:
 *
 *  - Обрабатывают и собирают данные,
 *  - Отвправляют данные для рендеринга страниц*(не все методы отправляют данные)
 *  - Запускают рендер страницы
 * ======================================================
 */
class AdminController extends Controller
{
    /**
     * Трейт с ошибками
     */
    use Errors;
    /**
     * Трейт с методами для обработки данных
     */
    use DataProcessing;

    /**
     *  Подключает модели
     */
    protected function setModel()
    {
        self::sessionCheck();
        $this->model['admin'] = new Admin();
        $this->model['question'] = new Question();
        $this->model['category'] = new Category();
        $this->model['telegram'] = new Telegram();
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
     * ======================================================
     */
    public function actionIndex()
    {
        $this->model['telegram']->telegramRun();

        $questions = $this->model['question']->getQuestions();
        $categories = self::categoriesProcessing($questions);
        $admins = $this->model['admin']->getAdmins();
        $dictionary = self::dictionaryProcessing();
        $words = $this->model['question']->getDictionary();

        $array =  [
            'header' => [
                'title' => 'Панель администратора'
            ],
            'data'   => [
                'header' => 'Привет ' . $_SESSION['adminLogin'] . '!',
                'questions'  => $questions,
                'categories' => $categories,
                'admins'     => $admins,
                'dictionary' => $dictionary,
                'words'      => $words
            ]
        ];

        $this->view->render('admin', $array);
    }

    public function actionLogin()
    {
        if (isset($_SESSION['adminId'])) {
            redirect('?/admin');
        }

        $errors = [];
        $data = $_POST;

        if (isset($data['goLogin'])) {
            $admin = $this->model['admin']->getAdmin($data['loginLog']);
            $errors = self::checkDataLogin($data, $admin);

            if (empty($errors)) {
                $_SESSION['adminLogin'] = $admin['login'];
                $_SESSION['adminId'] = $admin['id'];
                redirect('?/admin');
            }
        }
        $array = [
            'header' => [
                'title' => 'Панель администратора'
            ],
            'data' => [
                'header' => 'Авторизация',
                'error' => array_shift($errors),
                'data' => $data
            ]
        ];

        $this->view->render('login', $array);
    }

    public function actionEdit()
    {
        $data = $_POST;
        $errors = [];

        if (isset($data['type'])) {
            $question = $this->model['question']->getQuestion($data['type'], $data['id']);
        } else {
            redirect('?/admin');
        }

        if (isset($data['updateQuestion'])) {
            $errors = self::checkDataQuestion($data);
            if (empty($errors)) {

                if (strpos($data['email'], 'telegram') !== false) {
                    $telegram = explode(':', $data['email']);
                    $this->model['telegram']->messageTelegram($telegram[1], $telegram[2], trim($data['answers']));
                }

                $this->model['question']->methodCall('updateQuestion', $data);
            }
        }

        $question = self::questionProcessing($question, $data);

        if (isset($data['action'])) {
            $data['objectQuestion'] = $question;
            $this->model['question']->methodCall('actionQuestion', $data);
        }

        $categories = $this->model['category']->getCategories();

        $array = [
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

        $this->view->render('edit', $array);
    }

    public function actionCategory() {
        $data = $_POST;

        if ($data['title'] === '') {
            redirect('?/admin');
        }

        $this->model['category']->methodCall('category', $data);
    }

    public function actionDictionary() {
        $data = $_POST;

        if (!isset($data['dictionary'])) {
            redirect('?/admin');
        }

        $this->model['question']->methodCall('dictionary', $data);
    }

    public function actionAdmin() {
        $data = $_POST;

        if ($data['action'] === 'add') {
            self::checkAdminData($data);
        }

        $this->model['admin']->methodCall('admin', $data);

        redirect('admin');
    }

    public function actionLogout()
    {
        session_destroy();
        redirect('');
    }
}