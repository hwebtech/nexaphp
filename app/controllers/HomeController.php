<?php

namespace app\controllers;

use core\Controller;

class HomeController extends Controller
{
    public function index()
    {
        $params = [
            'name' => 'H-Web Framework'
        ];
        return $this->render('home', $params);
    }
}
