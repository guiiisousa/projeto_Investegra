<?php
namespace App\Models;

class Analise
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getAnalise($usuarioId)
    {
        $sql = "
            SELECT
                a.categoria,
                SUM(a.quantidade * a.preco_medio) AS total
            FROM ativos a
            INNER JOIN carteiras c
                ON a.carteira_id = c.id
            WHERE c.usuario_id = ?
            GROUP BY a.categoria
            ORDER BY total DESC
        ";

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param("i", $usuarioId);

        $stmt->execute();

        return $stmt
            ->get_result()
            ->fetch_all(MYSQLI_ASSOC);
    }

    public function getPatrimonioTotal($usuarioId)
    {
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

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param("i", $usuarioId);

        $stmt->execute();

        return $stmt
            ->get_result()
            ->fetch_assoc()['patrimonio'];
    }
}
?>
