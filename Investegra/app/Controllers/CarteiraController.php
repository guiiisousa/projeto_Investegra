<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Carteira;
use App\Models\Historico;

class CarteiraController extends Controller
{
    public function index()
    {
        $usuarioId = $this->requireLogin();

        $carteiras = Carteira::resumoPorUsuario($usuarioId);

        foreach ($carteiras as $id => $carteira) {
            $carteiras[$id]["historico"] = Historico::atividadesPorCarteira(
                $usuarioId,
                (int) $id
            );
        }

        $this->view("carteiras/index", compact("carteiras"));
    }

    public function create()
    {
        $this->requireLogin();

        $tokens = $_SESSION["carteira_create_tokens"] ?? [];
        $agora = time();
        $tokens = array_filter(
            $tokens,
            static fn ($criadoEm) => $agora - $criadoEm < 1800
        );

        $token = bin2hex(random_bytes(32));
        $tokens[$token] = $agora;
        $_SESSION["carteira_create_tokens"] = $tokens;

        $erro = ($_GET["erro"] ?? "") === "envio"
            ? "Este formulário já foi enviado. Confira suas carteiras antes de tentar novamente."
            : "";

        $this->view("carteiras/create", compact("token", "erro"));
    }

    public function store()
    {
        $usuarioId = $this->requireLogin();

        if (!$this->isPost()) {
            $this->redirect("carteiras.php");
        }

        $token = $_POST["token"] ?? "";
        $tokens = $_SESSION["carteira_create_tokens"] ?? [];

        if (
            !\is_string($token)
            || !isset($tokens[$token])
        ) {
            $this->redirect("nova_carteira.php?erro=envio");
        }

        unset($tokens[$token]);
        $_SESSION["carteira_create_tokens"] = $tokens;

        $nome = trim($_POST["nome"] ?? "");
        $descricao = trim($_POST["descricao"] ?? "");

        if ($nome === "" || strlen($nome) > 100) {
            $this->redirect("nova_carteira.php?erro=nome");
        }

        $carteiraId = Carteira::create($nome, $descricao, $usuarioId);

        if ($carteiraId !== null) {
            Historico::registrarAtividade(
                $usuarioId,
                "criou",
                "Carteira",
                "Carteira criada: " . $nome,
                $carteiraId,
                ["nome" => $nome, "descricao" => $descricao]
            );
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

        $carteira = Carteira::find($id, $usuarioId);

        if (!$carteira) {
            $this->redirect("carteiras.php?erro=1");
        }

        if (strlen($nome) > 100) {
            $this->redirect("editar_carteira.php?id=" . $id . "&erro=1");
        }

        if (Carteira::update($id, $usuarioId, $nome, $descricao)) {
            $anterior = $carteira ?? [];
            Historico::registrarAtividade(
                $usuarioId,
                "editou",
                "Carteira",
                "Carteira atualizada: " . $nome,
                $id,
                [
                    "antes" => [
                        "nome" => $anterior["nome"] ?? null,
                        "descricao" => $anterior["descricao"] ?? null,
                    ],
                    "depois" => ["nome" => $nome, "descricao" => $descricao],
                ]
            );
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

        $carteira = Carteira::find($id, $usuarioId);

        if (Carteira::delete($id, $usuarioId)) {
            Historico::registrarAtividade(
                $usuarioId,
                "excluiu",
                "Carteira",
                "Carteira excluída: " . ($carteira["nome"] ?? "Sem nome"),
                $id,
                ["nome" => $carteira["nome"] ?? null, "descricao" => $carteira["descricao"] ?? null]
            );
            $this->redirect("carteiras.php?sucesso=1");
        }

        $this->redirect("carteiras.php?erro=1");
    }
}
