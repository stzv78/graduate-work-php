<?php

namespace Engine\Core\View;

/**
 * Class Render
 * @package Engine\Core\Render
 */
class View
{
    /**
     * @param $name
     * @param array $array
     */
    public function render($name, $array = [])
    {
        $array['data']['thisHost'] = HOST;
        $array['header']['thisHost'] = HOST;
        $twig = $this->twigInit();
        $this->display($twig, 'header', $array['header']);
        $this->display($twig, $name, $array['data']);
        $this->display($twig, 'footer');
    }

    private function display($twig, $name, $array = [])
    {
        $template = $twig->load($name . '.twig');
        $template->display($array);
    }

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