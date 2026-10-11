<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;

class HomeController extends Controller
{
    public function index()
    {
        $this->redirect(Session::check() ? "dashboard.php" : "login.php");
    }
}
