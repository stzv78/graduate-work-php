<?php

namespace Engine;

/**
 * Class Session
 * @package Engine
 */
class Session
{
    /**
     * @var \Engine\DI\DI;
     */
    private $di;
    /**
     * @var \Engine\Core\View\View
     */
    private $view;

    /**
     * @var \Engine\Core\Router\Router
     */
    private $router;

    /**
     * Session constructor.
     * @param $di
     */
    public function __construct($di)
    {
        $this->di = $di;
        $this->view = $this->di->get('view');
        $this->router = $this->di->get('router');
    }

    /**
     * Run
     */
    public function run() {
        $this->router->start();
    }
}