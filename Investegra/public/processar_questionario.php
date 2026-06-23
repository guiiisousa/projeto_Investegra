<?php

session_start();

include("conexao.php");

$id_usuario =
$_SESSION["id"];

/* ==========================
   DADOS
========================== */

$idade =
(int) $_POST["idade"];

$experiencia =
$_POST["experiencia"];

$renda =
(float) $_POST["renda_mensal"];

$horizonte =
$_POST["horizonte"];

$tolerancia =
$_POST["tolerancia"];

$queda =
(int) $_POST["queda"];

$objetivo =
trim($_POST["objetivo"]);

$descricao =
trim($_POST["descricao_pessoal"]);

/* ==========================
   IA PYTHON
========================== */

$comando =

"py analisar_perfil.py " .

escapeshellarg($descricao);

$resultadoIA =
trim(
    shell_exec($comando . " 2>&1")
);

if(!$resultadoIA){

    $perfilIA = "Moderado";

    $analiseIA =
    "Não foi possível executar a análise IA.";

}else{

    $partes =
        explode(
            "|",
            $resultadoIA,
            2
        );

        if(count($partes) < 2){

            $perfilIA = "Moderado";

            $analiseIA =
            "A IA retornou um formato inválido.";

        }else{

            $perfilIA =
            trim($partes[0]);

            $analiseIA =
            trim($partes[1]);
        }
}

/* ==========================
   SCORE
========================== */

$score = 0;

/* ==========================
   IDADE
========================== */

if($idade < 30){

    $score += 2;

}else{

    $score += 1;
}

/* ==========================
   EXPERIÊNCIA
========================== */

switch($experiencia){

    case "Nenhuma":
        $score += 1;
    break;

    case "Básica":
        $score += 2;
    break;

    case "Intermediária":
        $score += 3;
    break;

    case "Avançada":
        $score += 4;
    break;
}

/* ==========================
   HORIZONTE
========================== */

if($horizonte == "Longo Prazo"){

    $score += 3;

}elseif($horizonte == "Médio Prazo"){

    $score += 2;

}else{

    $score += 1;
}

/* ==========================
   TOLERÂNCIA
========================== */

if($tolerancia == "Alta"){

    $score += 3;

}elseif($tolerancia == "Média"){

    $score += 2;

}else{

    $score += 1;
}

/* ==========================
   QUEDA
========================== */

$score += $queda;

/* ==========================
   PERFIL
========================== */

/* ==========================
   SCORE BASE
========================== */

if($score <= 7){

    $perfil = "Conservador";

}elseif($score <= 12){

    $perfil = "Moderado";

}else{

    $perfil = "Arrojado";
}

/* ==========================
   AJUSTE IA
========================== */

if($perfilIA == "Conservador"){

    $score -= 1;

}elseif($perfilIA == "Arrojado"){

    $score += 1;
}

/* ==========================
   PERFIL FINAL
========================== */

if($score <= 7){

    $perfilFinal = "Conservador";

}elseif($score <= 12){

    $perfilFinal = "Moderado";

}else{

    $perfilFinal = "Arrojado";
}

/* ==========================
   SALVAR
========================== */

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
perfil_risco = ?, 
analise_ia = ?

WHERE id = ?

";

$stmt =
$conn->prepare($sql);

$stmt->bind_param(

    "isdssssissi",

    $idade,
    $experiencia,
    $renda,
    $horizonte,
    $tolerancia,
    $objetivo,
    $descricao,
    $score,
    $perfilFinal,
    $analiseIA,
    $id_usuario

);

$stmt->execute();

/* ==========================
   REDIRECIONAR
========================== */

header(
"Location: resultado_quiz.php"
);

exit;