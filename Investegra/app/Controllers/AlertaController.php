<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Alerta;
use App\Services\AlertaService;

class AlertaController extends Controller
{
    public function index()
    {
        $usuarioId = $this->requireLogin();

        AlertaService::gerar($usuarioId);

        $alertas = Alerta::all($usuarioId);

        $this->view("alertas/index", compact("alertas"));
    }
}
