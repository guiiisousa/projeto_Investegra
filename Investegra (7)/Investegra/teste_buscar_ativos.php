<?php

$termo = "PETR";

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
    die("Erro: " . curl_error($curl));
}

$codigo = curl_getinfo($curl, CURLINFO_HTTP_CODE);

curl_close($curl);

echo "<h1>Teste da lista de ativos</h1>";
echo "<p>HTTP: $codigo</p>";

echo "<pre>";
print_r(json_decode($resposta, true));
echo "</pre>";