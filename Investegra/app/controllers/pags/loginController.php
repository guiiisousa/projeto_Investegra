<?php

namespace App\Controllers\Pags;

use App\Service\View;

class Login
{
    // Exibe a página de login
    public static function getLogin()
    {
        return View::render('login', [
            'name' => 'Guilherme',
            'email' => 'gui@gmail.com'
        ]);
    }
}
?>