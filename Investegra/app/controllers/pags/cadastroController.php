<?php

namespace App\Controllers\Pags;

use App\Service\View;

class CadastroController
{
    // Exibe a página de cadastro
    public function getCadastro()
    {
        return View::render('cadastro');
    }
}
?>