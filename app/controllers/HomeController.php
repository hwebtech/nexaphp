<?php

namespace app\controllers;

use core\Controller;

class HomeController extends Controller
{
    public function index()
    {
        return $this->render('home', ['name' => 'H-Web Framework']);
    }

    public function about()
    {
        return $this->render('home', ['name' => 'About Us']);
    }

    public function contact()
    {
        return $this->render('home', ['name' => 'Contact Us']);
    }
}
