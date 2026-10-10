<?php

header('Content-Type: application/json; charset=utf-8');

$termo = trim($_GET['termo'] ?? '');

if ($termo === '' || strlen($termo) < 2) {
    echo json_encode([
        'sucesso' => true,
        'resultados' => []
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$termo = strtoupper($termo);

/*
 * Por enquanto vamos consultar a lista de ativos
 * disponibilizada pela brapi.
 */

$url = "https://brapi.dev/api/quote/list";

$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_SSL_VERIFYPEER => true
]);

$resposta = curl_exec($curl);

if ($resposta === false) {
    http_response_code(500);

    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Não foi possível consultar a API.'
    ], JSON_UNESCAPED_UNICODE);

    curl_close($curl);
    exit;
}

$codigo_http = curl_getinfo($curl, CURLINFO_HTTP_CODE);

curl_close($curl);

if ($codigo_http !== 200) {
    http_response_code(502);

    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'A API retornou um erro.'
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$dados = json_decode($resposta, true);

if (!isset($dados['stocks']) || !is_array($dados['stocks'])) {
    echo json_encode([
        'sucesso' => true,
        'resultados' => []
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$resultados = [];

foreach ($dados['stocks'] as $ativo) {

    $ticker = strtoupper($ativo['stock'] ?? '');
    $nome = $ativo['name'] ?? '';

    if ($ticker === '') {
        continue;
    }

    /*
     * Procura tanto pelo ticker quanto pelo nome.
     */

    if (
        stripos($ticker, $termo) !== false ||
        stripos($nome, $termo) !== false
    ) {

        $resultados[] = [
            'ticker' => $ticker,
            'nome' => $nome
        ];
    }

    /*
     * Não precisamos retornar centenas
     * de resultados.
     */

    if (count($resultados) >= 8) {
        break;
    }
}

echo json_encode([
    'sucesso' => true,
    'resultados' => $resultados
], JSON_UNESCAPED_UNICODE);