<?php

require_once "conexao.php";


function registrarHistoricoPatrimonio(
    $conexao,
    $id_usuario
) {

    /*
     * Calcular patrimônio atual
     * de todas as carteiras do usuário.
     */

    $sql = "
        SELECT
            COALESCE(
                SUM(
                    a.quantidade * a.preco_medio
                ),
                0
            ) AS patrimonio
        FROM ativos a
        INNER JOIN carteiras c
            ON a.carteira_id = c.id
        WHERE c.usuario_id = ?
    ";

    $stmt = $conexao->prepare($sql);

    $stmt->bind_param(
        "i",
        $id_usuario
    );

    $stmt->execute();

    $resultado = $stmt->get_result();

    $dados = $resultado->fetch_assoc();

    $patrimonio = $dados["patrimonio"];

    $stmt->close();


    /*
     * Registrar no histórico.
     */

    $sql = "
        INSERT INTO historico_patrimonio
        (
            usuario_id,
            valor_total
        )
        VALUES (?, ?)
    ";

    $stmt = $conexao->prepare($sql);

    $stmt->bind_param(
        "id",
        $id_usuario,
        $patrimonio
    );

    $stmt->execute();

    $stmt->close();

}

?>