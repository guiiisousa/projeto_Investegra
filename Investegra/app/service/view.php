<?php

namespace App\Service;

class View
{
    // Obtém o caminho do arquivo de visualização
    private static function getContentView($view)
    {
        $file = __DIR__ . '/../../views/' . $view . '.html';
        return file_exists($file) ? $file : null;
    }


    // Exibe a página com os dados fornecidos
    public static function render($view, $data = [])
    { 
        $contentView = self::getContentView($view);
        return $contentView ? file_get_contents($contentView) : null;
    }
}
?>