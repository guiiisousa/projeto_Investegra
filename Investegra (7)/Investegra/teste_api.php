<?php

$ticker = "PETR4";

$url = "https://brapi.dev/api/v2/stocks/quote?symbols=" . urlencode($ticker);

$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_SSL_VERIFYPEER => true
]);

$resposta = curl_exec($curl);

if ($resposta === false) {
    die("Erro ao consultar a API: " . curl_error($curl));
}

$codigo_http = curl_getinfo($curl, CURLINFO_HTTP_CODE);

curl_close($curl);

$dados = json_decode($resposta, true);

if ($dados === null) {
    die("A API retornou uma resposta inválida.");
}

echo "<h1>Teste da API brapi</h1>";

echo "<p><strong>Código HTTP:</strong> " . $codigo_http . "</p>";

echo "<h2>Resposta da API</h2>";

echo "<pre>";
print_r($dados);
echo "</pre>";