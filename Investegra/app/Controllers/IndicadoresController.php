<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Ativo;
use App\Models\Carteira;

class IndicadoresController extends Controller
{
    public function index()
    {
        $this->requireLogin();
        $this->redirect("analises.php");
    }
}
