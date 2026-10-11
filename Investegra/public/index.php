<?php

/*
 * Front controller: toda requisição que não é um arquivo real passa por aqui
 * (veja public/.htaccess).
 */

require __DIR__ . '/../app/bootstrap.php';

$router = require BASE_PATH . '/app/routes.php';

$router->dispatch();
