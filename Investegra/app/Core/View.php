<?php

namespace App\Core;

class View
{
    public static function render(string $view, array $dados = []): void
    {
        $arquivo = BASE_PATH . '/app/Views/' . $view . '.php';

        if (!is_file($arquivo)) {
            http_response_code(500);
            die("View não encontrada: " . htmlspecialchars($view));
        }

        /* Helpers só são carregados aqui para não colidir com as páginas legadas. */
        require_once BASE_PATH . '/app/Helpers/format.php';

        extract($dados, EXTR_SKIP);

        require $arquivo;
    }
}
