<?php

namespace Engine\Core\Response;

class Response
{
    public static function redirect($uri = '')
    {
        header('Location:' . HOST . $uri);

        exit;
    }
}