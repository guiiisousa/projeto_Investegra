<?php

namespace App\Services;

/* Executa os scripts Python (/scripts) que consultam o Ollama. */
class AnaliseIAService
{
    /* Análise do perfil do investidor. Retorna "" se não for possível gerar. */
    public static function analisarPerfil(array $dados): string
    {
        $r = self::executar("analisar_perfil.py", $dados);

        if (!$r["iniciou"] || $r["codigo"] !== 0 || empty($r["saida"])) {
            return "";
        }

        $json = json_decode($r["saida"], true);

        if (is_array($json) && !empty($json["sucesso"]) && !empty($json["analise"])) {
            return trim($json["analise"]);
        }

        return "";
    }

    /* Análise da carteira. Sempre devolve um texto para exibir. */
    public static function analisarCarteira(array $dados): string
    {
        $r = self::executar("analisar_carteira.py", $dados);

        if (!$r["iniciou"]) {
            return "Não foi possível iniciar a análise da carteira.";
        }

        $json = json_decode($r["saida"], true);

        if (is_array($json) && isset($json["sucesso"]) && $json["sucesso"] === true) {
            return $json["analise"];
        }

        return "Não foi possível gerar a análise da carteira.";
    }

    /* Envia $dados (JSON) para o stdin do script e lê stdout/stderr. */
    private static function executar(string $script, array $dados): array
    {
        $comando = "python3 " . escapeshellarg(BASE_PATH . "/scripts/" . $script);

        $processo = proc_open(
            $comando,
            [
                0 => ["pipe", "r"],
                1 => ["pipe", "w"],
                2 => ["pipe", "w"],
            ],
            $pipes
        );

        if (!is_resource($processo)) {
            return ["iniciou" => false, "codigo" => null, "saida" => "", "erro" => ""];
        }

        fwrite($pipes[0], json_encode($dados, JSON_UNESCAPED_UNICODE));
        fclose($pipes[0]);

        $saida = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $erro = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        $codigo = proc_close($processo);

        return ["iniciou" => true, "codigo" => $codigo, "saida" => $saida, "erro" => $erro];
    }
}
