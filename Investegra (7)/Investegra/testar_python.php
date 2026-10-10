<?php

header("Content-Type: text/html; charset=UTF-8");

$dados = [

    "idade" => 18,

    "experiencia" => "Media",

    "renda_mensal" => 3500,

    "horizonte_investimento" => "Longo",

    "tolerancia_risco" => "Media",

    "objetivo" => "Crescimento",

    "descricao_pessoal" =>
        "Quero aprender a organizar melhor meus investimentos.",

    "perfil_risco" => "Moderado"
];


$json = json_encode(
    $dados,
    JSON_UNESCAPED_UNICODE
);


$comando = "py analisar_perfil.py";


$processo = proc_open(

    $comando,

    [
        0 => ["pipe", "r"],
        1 => ["pipe", "w"],
        2 => ["pipe", "w"]
    ],

    $pipes
);


if (!is_resource($processo)) {

    die("Não foi possível executar o Python.");

}


fwrite(
    $pipes[0],
    $json
);

fclose($pipes[0]);


$saida = stream_get_contents(
    $pipes[1]
);

fclose($pipes[1]);


$erro = stream_get_contents(
    $pipes[2]
);

fclose($pipes[2]);


$codigo = proc_close($processo);


echo "<h2>DEBUG</h2>";

echo "<h3>Código de saída:</h3>";
echo "<pre>";
var_dump($codigo);
echo "</pre>";


echo "<h3>Saída do Python:</h3>";
echo "<pre>";
echo htmlspecialchars($saida);
echo "</pre>";


echo "<h3>Erro do Python:</h3>";
echo "<pre>";
echo htmlspecialchars($erro);
echo "</pre>";


echo "<h3>JSON enviado:</h3>";
echo "<pre>";
echo htmlspecialchars($json);
echo "</pre>";


echo "<h3>JSON decodificado pelo PHP:</h3>";

$resultado = json_decode(
    $saida,
    true
);

echo "<pre>";
var_dump($resultado);
echo "</pre>";

?>