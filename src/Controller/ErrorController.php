<?php

namespace Ainab\Really\Controller;

class ErrorController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function notFound($message = null)
    {
        // Handle 404
        echo $this->twig->render('errors/404.html.twig', ['message' => $message]);
    }
}
