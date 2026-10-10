<?php

require_once "conexao.php";

function gerarRecomendacoes($conexao, $id_usuario)
{

    /*
    |--------------------------------------------------------------------------
    | Buscar perfil do usuário
    |--------------------------------------------------------------------------
    */

    $sql = "
        SELECT
            perfil_risco,
            experiencia,
            horizonte_investimento,
            objetivo
        FROM usuarios
        WHERE id = ?
    ";

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();

    $resultado = $stmt->get_result();
    $usuario = $resultado->fetch_assoc();

    $stmt->close();


    if (!$usuario) {
        return;
    }


    $perfil = $usuario["perfil_risco"];


    /*
    |--------------------------------------------------------------------------
    | Limpar recomendações anteriores
    |--------------------------------------------------------------------------
    */

    $sql = "
        DELETE FROM recomendacoes
        WHERE usuario_id = ?
    ";

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $stmt->close();


    /*
    |--------------------------------------------------------------------------
    | Verificar patrimônio e quantidade de ativos
    |--------------------------------------------------------------------------
    */

    $sql = "
        SELECT
            COUNT(a.id) AS quantidade_ativos,
            COALESCE(
                SUM(a.quantidade * a.preco_medio),
                0
            ) AS patrimonio
        FROM ativos a
        INNER JOIN carteiras c
            ON a.carteira_id = c.id
        WHERE c.usuario_id = ?
    ";

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();

    $resultado = $stmt->get_result();
    $dados = $resultado->fetch_assoc();

    $quantidade_ativos = (int) $dados["quantidade_ativos"];
    $patrimonio = (float) $dados["patrimonio"];

    $stmt->close();


    /*
    |--------------------------------------------------------------------------
    | Se não possui ativos
    |--------------------------------------------------------------------------
    */

    if ($quantidade_ativos == 0) {

        inserirRecomendacao(
            $conexao,
            $id_usuario,
            "Cadastre seus ativos",
            "Sua carteira ainda não possui ativos cadastrados. Cadastre os investimentos que fazem parte da sua carteira para que o sistema possa apresentar análises de composição e diversificação.",
            "Organização",
            null
        );

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | Recomendações baseadas no perfil
    |--------------------------------------------------------------------------
    */

    if ($perfil == "Conservador") {

        inserirRecomendacao(
            $conexao,
            $id_usuario,
            "Perfil conservador",
            "Seu perfil de risco é classificado como conservador. Ao analisar novos investimentos, considere principalmente características relacionadas à preservação do patrimônio e ao controle da exposição ao risco.",
            "Perfil de risco",
            null
        );

    } elseif ($perfil == "Moderado") {

        inserirRecomendacao(
            $conexao,
            $id_usuario,
            "Perfil moderado",
            "Seu perfil de risco é classificado como moderado. Uma carteira diversificada entre diferentes categorias pode ajudar a equilibrar risco e potencial de retorno.",
            "Perfil de risco",
            null
        );

    } elseif ($perfil == "Arrojado") {

        inserirRecomendacao(
            $conexao,
            $id_usuario,
            "Perfil arrojado",
            "Seu perfil de risco é classificado como arrojado. Mesmo aceitando maior exposição ao risco, é importante acompanhar a composição da carteira e manter uma estratégia de diversificação.",
            "Perfil de risco",
            null
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Verificar diversificação
    |--------------------------------------------------------------------------
    */

    $sql = "
        SELECT
            COUNT(DISTINCT a.categoria_id) AS categorias
        FROM ativos a
        INNER JOIN carteiras c
            ON a.carteira_id = c.id
        WHERE c.usuario_id = ?
        AND a.categoria_id IS NOT NULL
    ";

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();

    $resultado = $stmt->get_result();
    $dados = $resultado->fetch_assoc();

    $categorias = (int) $dados["categorias"];

    $stmt->close();


    if ($categorias == 1) {

        inserirRecomendacao(
            $conexao,
            $id_usuario,
            "Avalie a diversificação",
            "Sua carteira possui ativos registrados em apenas uma categoria. Avalie, de acordo com seus objetivos e tolerância ao risco, a possibilidade de distribuir seus investimentos entre diferentes categorias.",
            "Diversificação",
            null
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Analisar concentração
    |--------------------------------------------------------------------------
    */

    $sql = "
        SELECT
            cat.nome AS categoria,
            SUM(a.quantidade * a.preco_medio) AS valor
        FROM ativos a
        INNER JOIN carteiras c
            ON a.carteira_id = c.id
        LEFT JOIN categorias cat
            ON a.categoria_id = cat.id
        WHERE c.usuario_id = ?
        GROUP BY cat.id, cat.nome
        ORDER BY valor DESC
        LIMIT 1
    ";

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();

    $resultado = $stmt->get_result();
    $maior_categoria = $resultado->fetch_assoc();

    $stmt->close();


    if ($patrimonio > 0 && $maior_categoria) {

        $percentual =
            ($maior_categoria["valor"] / $patrimonio) * 100;


        /*
        |--------------------------------------------------------------------------
        | Concentração acima de 70%
        |--------------------------------------------------------------------------
        */

        if ($percentual >= 70) {

            inserirRecomendacao(
                $conexao,
                $id_usuario,
                "Reduza a concentração",
                "A categoria " .
                $maior_categoria["categoria"] .
                " representa aproximadamente " .
                number_format($percentual, 1, ",", ".") .
                "% do patrimônio registrado. Avalie se essa concentração está de acordo com seu perfil de risco e objetivos.",
                "Concentração",
                null
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Perfil conservador + concentração
    |--------------------------------------------------------------------------
    */

    if (
        $perfil == "Conservador" &&
        $patrimonio > 0 &&
        $maior_categoria
    ) {

        $percentual =
            ($maior_categoria["valor"] / $patrimonio) * 100;


        if ($percentual >= 70) {

            inserirRecomendacao(
                $conexao,
                $id_usuario,
                "Revise a composição da carteira",
                "A concentração identificada merece atenção considerando seu perfil de risco conservador. Avalie a composição da carteira e sua compatibilidade com seus objetivos.",
                "Perfil de risco",
                null
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Poucos ativos
    |--------------------------------------------------------------------------
    */

    if ($quantidade_ativos < 3) {

        inserirRecomendacao(
            $conexao,
            $id_usuario,
            "Acompanhe a diversificação",
            "Sua carteira possui poucos ativos cadastrados. Avalie se a quantidade e a distribuição atual são adequadas aos seus objetivos.",
            "Diversificação",
            null
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Recomendação relacionada ao horizonte
    |--------------------------------------------------------------------------
    */

    if ($usuario["horizonte_investimento"] == "Curto") {

        inserirRecomendacao(
            $conexao,
            $id_usuario,
            "Horizonte de curto prazo",
            "Como seu horizonte de investimento é de curto prazo, considere avaliar cuidadosamente a liquidez e a volatilidade dos investimentos antes de tomar decisões.",
            "Horizonte",
            null
        );

    } elseif ($usuario["horizonte_investimento"] == "Longo") {

        inserirRecomendacao(
            $conexao,
            $id_usuario,
            "Horizonte de longo prazo",
            "Seu horizonte de investimento é de longo prazo. O acompanhamento periódico da carteira e da diversificação pode contribuir para manter a estratégia alinhada aos seus objetivos.",
            "Horizonte",
            null
        );
    }
}


/*
|--------------------------------------------------------------------------
| Função para inserir recomendação
|--------------------------------------------------------------------------
*/

function inserirRecomendacao(
    $conexao,
    $id_usuario,
    $titulo,
    $descricao,
    $categoria,
    $percentual
) {

    $sql = "
        INSERT INTO recomendacoes
        (
            usuario_id,
            titulo,
            descricao,
            categoria,
            percentual_sugerido
        )
        VALUES (?, ?, ?, ?, ?)
    ";

    $stmt = $conexao->prepare($sql);

    $stmt->bind_param(
        "isssd",
        $id_usuario,
        $titulo,
        $descricao,
        $categoria,
        $percentual
    );

    $stmt->execute();

    $stmt->close();
}

?>