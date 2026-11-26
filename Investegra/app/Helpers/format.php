<?php

function formatarMoeda($valor)
{
    return "R$ " . number_format($valor, 2, ",", ".");
}

function formatarPercentual($valor)
{
    return number_format($valor, 2, ",", ".") . "%";
}

function formatarQuantidade($valor)
{
    $formatado = number_format((float) $valor, 8, ",", ".");

    return rtrim(rtrim($formatado, "0"), ",");
}

function classeResultado($valor)
{
    if ($valor === null) {
        return "";
    }

    return $valor >= 0 ? "resultado-positivo" : "resultado-negativo";
}

function sinalResultado($valor)
{
    if ($valor === null) {
        return "";
    }

    return $valor >= 0 ? "+" : "";
}
