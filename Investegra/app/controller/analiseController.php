<?php

namespace App\Controllers;

use App\Models\Usuario;
use App\Models\Analise;

class AnaliseController
{
    private $usuarioModel;
    private $analiseModel;

    public function __construct($usuarioModel, $analiseModel)
    {
        $this->usuarioModel = $usuarioModel;
        $this->analiseModel = $analiseModel;
    }

    public function getAnalise($usuarioId)
    {
        return $this->analiseModel->getAnalise($usuarioId);
    }

    public function getPatrimonioTotal($usuarioId)
    {
        return $this->analiseModel->getPatrimonioTotal($usuarioId);
    }
}