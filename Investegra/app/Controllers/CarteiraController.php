<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Carteira;

class CarteiraController extends Controller
{
    public function index()
    {
        $usuarioId = $this->requireLogin();

        $carteiras = Carteira::resumoPorUsuario($usuarioId);

        $this->view("carteiras/index", compact("carteiras"));
    }

    public function create()
    {
        $this->requireLogin();

        $this->view("carteiras/create");
    }

    public function store()
    {
        $usuarioId = $this->requireLogin();

        if (!$this->isPost()) {
            $this->redirect("carteiras.php");
        }

        $nome = trim($_POST["nome"] ?? "");
        $descricao = trim($_POST["descricao"] ?? "");

        if ($nome === "" || strlen($nome) > 100) {
            $this->redirect("nova_carteira.php?erro=nome");
        }

        if (Carteira::create($nome, $descricao, $usuarioId)) {
            $this->redirect("carteiras.php?sucesso=1");
        }

        $this->redirect("carteiras.php?erro=1");
    }

    public function edit()
    {
        $usuarioId = $this->requireLogin();

        $id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

        if (!$id) {
            $this->redirect("carteiras.php?erro=1");
        }

        /* O usuario_id também é verificado: ninguém edita carteira alheia. */
        $carteira = Carteira::find($id, $usuarioId);

        if (!$carteira) {
            $this->redirect("carteiras.php?erro=1");
        }

        $this->view("carteiras/edit", compact("carteira"));
    }

    public function update()
    {
        $usuarioId = $this->requireLogin();

        if (!$this->isPost()) {
            $this->redirect("carteiras.php");
        }

        $id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
        $nome = trim($_POST["nome"] ?? "");
        $descricao = trim($_POST["descricao"] ?? "");

        if (!$id || $nome === "") {
            $this->redirect("carteiras.php?erro=1");
        }

        if (strlen($nome) > 100) {
            $this->redirect("editar_carteira.php?id=" . $id . "&erro=1");
        }

        if (Carteira::update($id, $usuarioId, $nome, $descricao)) {
            $this->redirect("carteiras.php?sucesso=1");
        }

        $this->redirect("carteiras.php?erro=1");
    }

    public function destroy()
    {
        $usuarioId = $this->requireLogin();

        $id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

        if (!$id) {
            $this->redirect("carteiras.php?erro=1");
        }

        if (Carteira::delete($id, $usuarioId)) {
            $this->redirect("carteiras.php?sucesso=1");
        }

        $this->redirect("carteiras.php?erro=1");
    }
}
