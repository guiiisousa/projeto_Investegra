<?php

/*
 * Configuração padrão. Pode ser sobrescrita por variáveis de ambiente
 * (DB_HOST, DB_USER, DB_PASS, DB_NAME) ou por config/config.local.php,
 * que NÃO deve ser versionado.
 */

$config = [
    'db' => [
        'host' => getenv('DB_HOST') ?: 'localhost',
        'user' => getenv('DB_USER') ?: 'root',
        'pass' => getenv('DB_PASS') !== false ? getenv('DB_PASS') : '',
        'name' => getenv('DB_NAME') ?: 'Investegra',
    ],
];

$local = __DIR__ . '/config.local.php';

if (is_file($local)) {
    $config = array_replace_recursive($config, require $local);
}

return $config;
