<?php

require_once "brapi.php";

$tickers = [
    "PETR4",
    "VALE3"
];

$cotacoes = obterCotacoes($tickers);

header("Content-Type: application/json; charset=UTF-8");

echo json_encode(
    $cotacoes,
    JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
);