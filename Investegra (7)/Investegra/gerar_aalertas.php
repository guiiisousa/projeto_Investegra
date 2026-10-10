<?php

require_once "conexao.php";

function gerarAlertas($conexao, $id_usuario)
{
    // ==========================================
    // 1. Verificar se o usuário possui carteiras
    // ==========================================

    $sql = "
        SELECT COUNT(*) AS total
        FROM carteiras
        WHERE usuario_id = ?
    ";

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();

    $resultado = $stmt->get_result();
    $dados = $resultado->fetch_assoc();

    $total_carteiras = $dados["total"];

    $stmt->close();


    // ==========================================
    // 2. Verificar se possui ativos
    // ==========================================

    $sql = "
        SELECT COUNT(*) AS total
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

    $total_ativos = $dados["total"];

    $stmt->close();


    // ==========================================
    // 3. Limpar alertas antigos não lidos
    // ==========================================

    $sql = "
        DELETE FROM alertas
        WHERE usuario_id = ?
        AND lido = 0
    ";

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $stmt->close();


    // ==========================================
    // 4. Alertar se não houver carteira
    // ==========================================

    if ($total_carteiras == 0) {

        inserirAlerta(
            $conexao,
            $id_usuario,
            "Nenhuma carteira cadastrada",
            "Você ainda não possui uma carteira cadastrada no INVESTEGRA.",
            "Baixo"
        );

        return;
    }


    // ==========================================
    // 5. Alertar se não houver ativos
    // ==========================================

    if ($total_ativos == 0) {

        inserirAlerta(
            $conexao,
            $id_usuario,
            "Carteira sem ativos",
            "Sua carteira ainda não possui ativos cadastrados.",
            "Baixo"
        );

        return;
    }


    // ==========================================
    // 6. Verificar quantidade de categorias
    // ==========================================

    $sql = "
        SELECT COUNT(DISTINCT a.categoria_id) AS total
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

    $total_categorias = $dados["total"];

    $stmt->close();


    // ==========================================
    // 7. Alerta de baixa diversificação
    // ==========================================

    if ($total_categorias == 1) {

        inserirAlerta(
            $conexao,
            $id_usuario,
            "Baixa diversificação",
            "Todos os seus ativos estão concentrados em uma única categoria. Avalie a diversificação da carteira.",
            "Medio"
        );
    }


    // ==========================================
    // 8. Verificar concentração por categoria
    // ==========================================

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


    // ==========================================
    // 9. Calcular patrimônio total
    // ==========================================

    $sql = "
        SELECT
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

    $patrimonio = (float) $dados["patrimonio"];

    $stmt->close();


    // ==========================================
    // 10. Verificar concentração acima de 70%
    // ==========================================

    if ($patrimonio > 0 && $maior_categoria) {

        $percentual =
            ($maior_categoria["valor"] / $patrimonio) * 100;

        if ($percentual >= 70) {

            inserirAlerta(
                $conexao,
                $id_usuario,
                "Alta concentração",
                "A categoria " .
                $maior_categoria["categoria"] .
                " representa aproximadamente " .
                number_format($percentual, 1, ",", ".") .
                "% do patrimônio da carteira.",
                "Alto"
            );
        }
    }
}


/*
|--------------------------------------------------------------------------
| Função para inserir alerta
|--------------------------------------------------------------------------
*/

function inserirAlerta(
    $conexao,
    $id_usuario,
    $titulo,
    $mensagem,
    $nivel
) {

    $sql = "
        INSERT INTO alertas
        (
            usuario_id,
            titulo,
            mensagem,
            nivel
        )
        VALUES (?, ?, ?, ?)
    ";

    $stmt = $conexao->prepare($sql);

    $stmt->bind_param(
        "isss",
        $id_usuario,
        $titulo,
        $mensagem,
        $nivel
    );

    $stmt->execute();

    $stmt->close();
}

?>