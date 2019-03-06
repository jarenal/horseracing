<?php

namespace Jarenal\App\Controller;


use Jarenal\Core\Controller;

class Home extends Controller
{
    public function index()
    {
        return $this->view->render("home/index.tpl", ["title" => "Horses race"]);
    }
}
