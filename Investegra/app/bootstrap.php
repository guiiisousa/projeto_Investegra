<?php

define('BASE_PATH', dirname(__DIR__));

/* Autoload simples: App\Core\Router => app/Core/Router.php */
spl_autoload_register(function ($classe) {
    $prefixo = 'App\\';

    if (strncmp($classe, $prefixo, strlen($prefixo)) !== 0) {
        return;
    }

    $arquivo = BASE_PATH . '/app/'
        . str_replace('\\', '/', substr($classe, strlen($prefixo)))
        . '.php';

    if (is_file($arquivo)) {
        require $arquivo;
    }
});
