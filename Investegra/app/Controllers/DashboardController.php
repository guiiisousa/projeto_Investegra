<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Usuario;
use App\Services\DashboardService;

class DashboardController extends Controller
{
    public function index()
    {
        $usuarioId = $this->requireLogin();

        $usuario = Usuario::perfil($usuarioId);

        if (!$usuario) {
            Session::logout();
            $this->redirect("login.php");
        }

        $this->view("dashboard/index", DashboardService::dados($usuarioId, $usuario));
    }
}
