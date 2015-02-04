<?php

namespace App\Controllers;

class ErrorController extends ControllerBase
{

    public function initialize()
    {
        $this->view->setVar('title', $this->title);
    }

    public function indexAction()
    {

    }

    public function notFoundAction()
    {

    }


}

