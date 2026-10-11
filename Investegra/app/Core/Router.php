<?php

namespace App\Core;

class Router
{
    private array $rotas = [];

    /* Registra uma página: "carteiras" => Controller@acao */
    public function add(string $pagina, string $controller, string $acao): void
    {
        $this->rotas[$pagina] = [$controller, $acao];
    }

    public function dispatch(): void
    {
        $caminho = parse_url($_SERVER["REQUEST_URI"] ?? "/", PHP_URL_PATH) ?: "/";

        if (substr($caminho, -1) === "/") {
            $pagina = "index";
        } elseif (preg_match('#(?:^|/)api/([A-Za-z0-9_]+)(?:\.php)?$#i', $caminho, $m)) {
            /* /api/chat e /api/chat.php => "api/chat" */
            $pagina = "api/" . $m[1];
        } else {
            /* "/login" e "/login.php" apontam para a mesma página. */
            $pagina = preg_replace('/\.php$/i', '', basename($caminho));
        }

        if (!isset($this->rotas[$pagina])) {
            http_response_code(404);
            echo "Página não encontrada.";
            return;
        }

        [$classe, $acao] = $this->rotas[$pagina];

        $classe = "App\\Controllers\\" . $classe;

        (new $classe())->$acao();
    }
}
