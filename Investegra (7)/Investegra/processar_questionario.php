<?php

session_start();

if (!isset($_SESSION["id"])) {
    header("Location: login.php");
    exit;
}

require_once "conexao.php";

$id_usuario = $_SESSION["id"];


/*
|--------------------------------------------------------------------------
| Receber dados do questionário
|--------------------------------------------------------------------------
*/

$idade = filter_input(
    INPUT_POST,
    "idade",
    FILTER_VALIDATE_INT
);

$experiencia = $_POST["experiencia"] ?? "";
$renda_mensal = $_POST["renda_mensal"] ?? "";
$horizonte = $_POST["horizonte_investimento"] ?? "";
$tolerancia = $_POST["tolerancia_risco"] ?? "";
$queda = $_POST["queda"] ?? "";
$objetivo = $_POST["objetivo"] ?? "";
$descricao = trim(
    $_POST["descricao_pessoal"] ?? ""
);


/*
|--------------------------------------------------------------------------
| Validação
|--------------------------------------------------------------------------
*/

if (
    $idade === false ||
    $idade < 1 ||
    $idade > 120
) {
    die("Idade inválida.");
}


$experiencias_validas = [
    "Nenhuma",
    "Baixa",
    "Media",
    "Alta"
];

$horizontes_validos = [
    "Curto",
    "Medio",
    "Longo"
];

$tolerancias_validas = [
    "Baixa",
    "Media",
    "Alta"
];

$quedas_validas = [
    "Vender",
    "Esperar",
    "Aumentar"
];

$objetivos_validos = [
    "Preservacao",
    "Reserva",
    "Crescimento",
    "Longo_Prazo"
];

$rendas_validas = [
    "Ate_2000",
    "2000_5000",
    "5000_10000",
    "Acima_10000"
];


if (!in_array($experiencia, $experiencias_validas, true)) {
    die("Experiência inválida.");
}

if (!in_array($horizonte, $horizontes_validos, true)) {
    die("Horizonte de investimento inválido.");
}

if (!in_array($tolerancia, $tolerancias_validas, true)) {
    die("Tolerância ao risco inválida.");
}

if (!in_array($queda, $quedas_validas, true)) {
    die("Resposta sobre queda inválida.");
}

if (!in_array($objetivo, $objetivos_validos, true)) {
    die("Objetivo inválido.");
}

if (!in_array($renda_mensal, $rendas_validas, true)) {
    die("Faixa de renda inválida.");
}


/*
|--------------------------------------------------------------------------
| Pontuação tradicional
|--------------------------------------------------------------------------
*/

$score_experiencia = [
    "Nenhuma" => 0,
    "Baixa" => 1,
    "Media" => 2,
    "Alta" => 3
];

$score_horizonte = [
    "Curto" => 0,
    "Medio" => 1,
    "Longo" => 3
];

$score_tolerancia = [
    "Baixa" => 0,
    "Media" => 2,
    "Alta" => 4
];

$score_queda = [
    "Vender" => 0,
    "Esperar" => 2,
    "Aumentar" => 4
];


$score =
    $score_experiencia[$experiencia] +
    $score_horizonte[$horizonte] +
    $score_tolerancia[$tolerancia] +
    $score_queda[$queda];


/*
|--------------------------------------------------------------------------
| Definir perfil
|--------------------------------------------------------------------------
*/

if ($score <= 4) {

    $perfil = "Conservador";

} elseif ($score <= 9) {

    $perfil = "Moderado";

} else {

    $perfil = "Arrojado";
}


/*
|--------------------------------------------------------------------------
| Converter faixa de renda para valor aproximado
|--------------------------------------------------------------------------
*/

$renda_valores = [

    "Ate_2000" => 2000,

    "2000_5000" => 3500,

    "5000_10000" => 7500,

    "Acima_10000" => 10000

];

$renda_valor = $renda_valores[$renda_mensal];


/*
|--------------------------------------------------------------------------
| Salvar informações tradicionais no banco
|--------------------------------------------------------------------------
*/

$sql = "
    UPDATE usuarios
    SET
        idade = ?,
        experiencia = ?,
        renda_mensal = ?,
        horizonte_investimento = ?,
        tolerancia_risco = ?,
        objetivo = ?,
        descricao_pessoal = ?,
        score_risco = ?,
        perfil_risco = ?
    WHERE id = ?
";


$stmt = $conexao->prepare($sql);

$stmt->bind_param(
    "isdssssisi",
    $idade,
    $experiencia,
    $renda_valor,
    $horizonte,
    $tolerancia,
    $objetivo,
    $descricao,
    $score,
    $perfil,
    $id_usuario
);


if (!$stmt->execute()) {

    die("Erro ao salvar o perfil.");

}

$stmt->close();


/*
|--------------------------------------------------------------------------
| Preparar dados para o Python
|--------------------------------------------------------------------------
*/

$dados_python = [

    "idade" => $idade,

    "experiencia" => $experiencia,

    "renda_mensal" => $renda_valor,

    "horizonte_investimento" => $horizonte,

    "tolerancia_risco" => $tolerancia,

    "objetivo" => $objetivo,

    "descricao_pessoal" => $descricao,

    "perfil_risco" => $perfil

];


$json = json_encode(
    $dados_python,
    JSON_UNESCAPED_UNICODE
);


/*
|--------------------------------------------------------------------------
| Executar Python
|--------------------------------------------------------------------------
*/

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


/*
|--------------------------------------------------------------------------
| Se o Python puder ser executado
|--------------------------------------------------------------------------
*/

$analise_ia = "";


if (is_resource($processo)) {

    /*
    | Enviar JSON para o Python
    */

    fwrite(
        $pipes[0],
        $json
    );

    fclose($pipes[0]);


    /*
    | Ler resposta
    */

    $saida = stream_get_contents(
        $pipes[1]
    );

    fclose($pipes[1]);


    /*
    | Ler possíveis erros
    */

    $erro_python = stream_get_contents(
        $pipes[2]
    );

    fclose($pipes[2]);


    $codigo = proc_close(
        $processo
    );


    /*
    |--------------------------------------------------------------------------
    | Interpretar resposta do Python
    |--------------------------------------------------------------------------
    */

    if ($codigo === 0 && !empty($saida)) {

        $resultado_python = json_decode(
            $saida,
            true
        );


        if (
            is_array($resultado_python) &&
            !empty($resultado_python["sucesso"]) &&
            !empty($resultado_python["analise"])
        ) {

            $analise_ia =
                trim(
                    $resultado_python["analise"]
                );
        }
    }
}


/*
|--------------------------------------------------------------------------
| Salvar análise da IA
|--------------------------------------------------------------------------
*/

if (!empty($analise_ia)) {

    $sql = "
        UPDATE usuarios
        SET analise_ia = ?
        WHERE id = ?
    ";

    $stmt = $conexao->prepare($sql);

    $stmt->bind_param(
        "si",
        $analise_ia,
        $id_usuario
    );

    $stmt->execute();

    $stmt->close();
}


/*
|--------------------------------------------------------------------------
| Ir para resultado
|--------------------------------------------------------------------------
*/

header(
    "Location: resultado_quiz.php"
);

exit;

?>