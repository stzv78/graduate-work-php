<?php

namespace Engine\Service\View;

use Engine\Service\AbstractProvider;
use Engine\Core\View\View;

class Provider extends AbstractProvider
{
    /**
     * @var string
     */
    public $serviceName = 'view';

    /**
     * @return mixed
     */
    public function init()
    {
        $render = new View();
        $this->di->set($this->serviceName, $render);
    }
}