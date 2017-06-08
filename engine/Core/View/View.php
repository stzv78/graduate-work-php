<?php

namespace Engine\Core\View;

/**
 * ======================================================
 * Class View
 *  Подключает TWIG, устанавливает настройки
 *  Рендерит страници и выводит по запросу и данным
 *
 * ======================================================
 */
class View
{
    /**
     * Рендер страниц
     * @param $name
     * @param array $array
     */
    public function render($name, $array = [])
    {
        $array['data']['thisHost'] = HOST;
        $array['header']['thisHost'] = HOST;

        $name = ucfirst(CONTROLLER) . DS . $name;

        $twig = $this->twigInit();

        $this->display($twig, 'Main' . DS .'header', $array['header']);
        $this->display($twig, $name, $array['data']);
        $this->display($twig, 'Main' . DS .'footer');
    }

    /**
     * Рендер и вывод страници
     * @param $twig
     * @param $name
     * @param array $array
     */
    private function display($twig, $name, $array = [])
    {
        $template = $twig->load($name . '.twig');
        $template->display($array);
    }

    /**
     * Подключает TWIG
     * Устанавливает настройки
     * @return \Twig_Environment
     */
    private function twigInit()
    {
        $loader = new \Twig_Loader_Filesystem(TWIG_TEMPLATES);

        $twig = new \Twig_Environment($loader, [
            'cache' => TWIG_CACHE,
            'auto_reload' => true
        ]);

        return $twig;
    }
}