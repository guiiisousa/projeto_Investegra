<?php

namespace App\Services;

/* Assistente de IA (Ollama): respostas rápidas e prompts por contexto. */
class AssistenteService
{
    /* Devolve [status HTTP, corpo da resposta]. */
    public static function responder(string $pergunta, string $contexto, $dadosIA, $historico): array
    {
        $rapida = self::respostaRapida($pergunta);

        if ($rapida !== null) {
            return [200, [
                "sucesso" => true,
                "tipo" => "rapida",
                "resposta" => $rapida,
            ]];
        }

$historicoTexto = "";


if (is_array($historico)) {

    $historicoRecente =
        array_slice(
            $historico,
            -6
        );


    foreach (
        $historicoRecente
        as $mensagem
    ) {

        if (
            !isset(
                $mensagem["role"],
                $mensagem["content"]
            )
        ) {

            continue;

        }


        $role =
            $mensagem["role"];

        $content =
            $mensagem["content"];


        $nome =
            $role === "user"
                ? "Usuário"
                : "Assistente";


        $historicoTexto .=
            $nome .
            ": " .
            $content .
            "\n";

    }

}

$dadosTexto =
    json_encode(
        $dadosIA,
        JSON_PRETTY_PRINT |
        JSON_UNESCAPED_UNICODE
    );


if ($dadosTexto === false) {

    $dadosTexto =
        "Nenhum dado disponível.";

}

$descricaoContexto = "";


switch ($contexto) {

    case "simulacao":

        $descricaoContexto = "

Você está auxiliando o usuário na página de SIMULAÇÕES do INVESTEGRA.

Os dados abaixo pertencem à simulação que está sendo exibida atualmente.

Use esses valores para responder perguntas específicas sobre a simulação.

Não invente valores.

Se o usuário perguntar sobre o resultado, utilize os números fornecidos.

Os resultados são estimativas matemáticas e não representam garantia de rentabilidade futura.

Dados da simulação:

" . $dadosTexto;

        break;


    case "carteira":

        $descricaoContexto = "

Você está auxiliando o usuário na página de CARTEIRAS do INVESTEGRA.

Utilize os dados fornecidos para explicar patrimônio, ativos, resultados e composição da carteira.

Não invente dados.

Dados disponíveis:

" . $dadosTexto;

        break;


    case "ativos":

        $descricaoContexto = "

Você está auxiliando o usuário na página de ATIVOS do INVESTEGRA.

Explique os dados dos ativos apresentados pelo sistema de forma educacional.

Não invente cotações ou informações que não estejam nos dados fornecidos.

Dados disponíveis:

" . $dadosTexto;

        break;


    case "indicadores":

        $descricaoContexto = "

Você está auxiliando o usuário na página de INDICADORES do INVESTEGRA.

Explique os indicadores financeiros apresentados pelo sistema de maneira educacional.

Não invente valores.

Dados disponíveis:

" . $dadosTexto;

        break;


    case "perfil":

        $descricaoContexto = "

Você está auxiliando o usuário na página de PERFIL DE INVESTIDOR do INVESTEGRA.

Explique o significado do perfil e dos fatores utilizados na análise.

Não altere o perfil calculado pelo sistema.

Dados disponíveis:

" . $dadosTexto;

        break;


    default:

        $descricaoContexto = "

Você está auxiliando o usuário no sistema INVESTEGRA.

Responda de forma educacional sobre investimentos e sobre as funcionalidades do sistema.

Dados disponíveis:

" . $dadosTexto;

        break;
}

$prompt = "

Você é o Assistente INVESTEGRA.

O INVESTEGRA é um sistema educacional de gerenciamento,
análise e simulação de investimentos.

Responda sempre em português do Brasil.

Seja claro, objetivo e didático.

Explique conceitos quando necessário.

Não invente informações.

Quando existirem dados específicos da página,
utilize esses dados em vez de fazer suposições.

Não invente cotações atuais.

Não prometa rentabilidade.

Não garanta resultados financeiros.

Não dê ordens diretas de compra ou venda.

Não substitua uma análise profissional de investimentos.

As simulações do INVESTEGRA são estimativas matemáticas.

Não utilize HTML na resposta.

Evite excesso de formatação.

$descricaoContexto


HISTÓRICO RECENTE DA CONVERSA:

$historicoTexto


PERGUNTA ATUAL DO USUÁRIO:

$pergunta


Responda à pergunta do usuário.
";

        $url = getenv("OLLAMA_URL") ?: "http://localhost:11434/api/generate";

        $dadosOllama = [
            "model" => "llama3.2",
            "prompt" => $prompt,
            "stream" => false,
            "options" => [
                "temperature" => 0.3,
                "num_predict" => 350,
            ],
        ];

        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
            CURLOPT_POSTFIELDS => json_encode($dadosOllama, JSON_UNESCAPED_UNICODE),
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 120,
        ]);

        $resposta = curl_exec($ch);
        $erroCurl = curl_error($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($resposta === false) {
            return [500, [
                "sucesso" => false,
                "erro" => "Não foi possível conectar ao Ollama.",
                "detalhes" => $erroCurl,
            ]];
        }

        if ($status < 200 || $status >= 300) {
            return [500, [
                "sucesso" => false,
                "erro" => "O Ollama retornou um erro.",
                "status" => $status,
                "resposta_ollama" => $resposta,
            ]];
        }

        $respostaJSON = json_decode($resposta, true);

        if (!is_array($respostaJSON)) {
            return [500, [
                "sucesso" => false,
                "erro" => "Resposta inválida recebida do Ollama.",
                "resposta_ollama" => $resposta,
            ]];
        }

        $respostaIA = trim($respostaJSON["response"] ?? "");

        if ($respostaIA === "") {
            return [500, [
                "sucesso" => false,
                "erro" => "O Ollama não retornou uma resposta válida.",
            ]];
        }

        return [200, [
            "sucesso" => true,
            "tipo" => "ia",
            "modelo" => $respostaJSON["model"] ?? "llama3.2",
            "resposta" => $respostaIA,
        ]];
    }

    private static function respostaRapida($pergunta)
    {

        $texto =
            mb_strtolower(
                $pergunta,
                "UTF-8"
            );


        if (
            str_contains(
                $texto,
                "juros compostos"
            )
        ) {

            return "Nos juros compostos, os rendimentos acumulados passam a fazer parte do valor sobre o qual os próximos rendimentos são calculados. Por isso, ao longo do tempo, o crescimento tende a ser maior do que no regime de juros simples.";


        }


        if (
            str_contains(
                $texto,
                "juros simples"
            )
        ) {

            return "Nos juros simples, os juros são calculados com base no capital utilizado no cálculo, sem incorporar os juros anteriores à base de cálculo. Por isso, o crescimento matemático é linear em relação ao tempo.";


        }


        if (
            str_contains(
                $texto,
                "rentabilidade"
            )
        ) {

            return "Rentabilidade representa o ganho ou perda de um investimento em relação ao valor investido. Na simulação do INVESTEGRA, a taxa anual informada é utilizada como uma hipótese matemática para estimar o resultado futuro.";


        }


        if (
            str_contains(
                $texto,
                "aporte"
            )
        ) {

            return "Aporte é o valor colocado no investimento. Na sua simulação, existe um aporte inicial e também pode existir um aporte mensal. Aumentar os aportes tende a aumentar o capital acumulado, mas o resultado também depende da taxa utilizada e do prazo.";


        }


        if (
            str_contains(
                $texto,
                "diversificação"
            )
        ) {

            return "Diversificação consiste em distribuir os recursos entre diferentes investimentos ou categorias, reduzindo a dependência do resultado de um único ativo ou segmento. Ela não elimina os riscos dos investimentos.";


        }


        return null;
    }
}
