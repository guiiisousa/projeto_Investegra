<?php

namespace App\Services;

/* Questionário de perfil de investidor: validação, pontuação e textos. */
class PerfilService
{
    private const EXPERIENCIAS = ["Nenhuma" => 0, "Baixa" => 1, "Media" => 2, "Alta" => 3];
    private const HORIZONTES = ["Curto" => 0, "Medio" => 1, "Longo" => 3];
    private const TOLERANCIAS = ["Baixa" => 0, "Media" => 2, "Alta" => 4];
    private const QUEDAS = ["Vender" => 0, "Esperar" => 2, "Aumentar" => 4];
    private const OBJETIVOS = ["Preservacao", "Reserva", "Crescimento", "Longo_Prazo"];
    private const RENDAS = [
        "Ate_2000" => 2000,
        "2000_5000" => 3500,
        "5000_10000" => 7500,
        "Acima_10000" => 10000,
    ];

    /*
     * Valida as respostas e calcula score e perfil.
     * Devolve [mensagem de erro|null, dados].
     */
    public static function avaliar(array $post): array
    {
        $idade = filter_var($post["idade"] ?? null, FILTER_VALIDATE_INT);
        $experiencia = $post["experiencia"] ?? "";
        $rendaMensal = $post["renda_mensal"] ?? "";
        $horizonte = $post["horizonte_investimento"] ?? "";
        $tolerancia = $post["tolerancia_risco"] ?? "";
        $queda = $post["queda"] ?? "";
        $objetivo = $post["objetivo"] ?? "";
        $descricao = trim($post["descricao_pessoal"] ?? "");

        if ($idade === false || $idade < 1 || $idade > 120) {
            return ["Idade inválida.", []];
        }

        if (!array_key_exists($experiencia, self::EXPERIENCIAS)) {
            return ["Experiência inválida.", []];
        }

        if (!array_key_exists($horizonte, self::HORIZONTES)) {
            return ["Horizonte de investimento inválido.", []];
        }

        if (!array_key_exists($tolerancia, self::TOLERANCIAS)) {
            return ["Tolerância ao risco inválida.", []];
        }

        if (!array_key_exists($queda, self::QUEDAS)) {
            return ["Resposta sobre queda inválida.", []];
        }

        if (!in_array($objetivo, self::OBJETIVOS, true)) {
            return ["Objetivo inválido.", []];
        }

        if (!array_key_exists($rendaMensal, self::RENDAS)) {
            return ["Faixa de renda inválida.", []];
        }

        $score = self::EXPERIENCIAS[$experiencia]
            + self::HORIZONTES[$horizonte]
            + self::TOLERANCIAS[$tolerancia]
            + self::QUEDAS[$queda];

        if ($score <= 4) {
            $perfil = "Conservador";
        } elseif ($score <= 9) {
            $perfil = "Moderado";
        } else {
            $perfil = "Arrojado";
        }

        return [null, [
            "idade" => $idade,
            "experiencia" => $experiencia,
            "renda_mensal" => self::RENDAS[$rendaMensal],
            "horizonte" => $horizonte,
            "tolerancia" => $tolerancia,
            "objetivo" => $objetivo,
            "descricao" => $descricao,
            "score" => $score,
            "perfil" => $perfil,
        ]];
    }

    /* Texto explicativo exibido no resultado do quiz. */
    public static function descricao(string $perfil): string
    {
        if ($perfil === "Conservador") {
            return "O perfil conservador indica uma maior preocupação com a preservação do patrimônio e uma menor tolerância a oscilações. É importante observar liquidez, segurança e os riscos envolvidos em cada investimento.";
        }

        if ($perfil === "Moderado") {
            return "O perfil moderado indica uma disposição intermediária ao risco, buscando equilibrar preservação do patrimônio e possibilidade de crescimento. A diversificação pode ser importante para equilibrar diferentes níveis de risco.";
        }

        if ($perfil === "Arrojado") {
            return "O perfil arrojado indica maior tolerância a oscilações e maior disposição para assumir riscos em busca de crescimento no longo prazo. Ainda assim, é importante considerar diversificação, horizonte de investimento e capacidade de suportar perdas.";
        }

        return "Seu perfil foi calculado pelo questionário com base nas respostas fornecidas.";
    }
}
