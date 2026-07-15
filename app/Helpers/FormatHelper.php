<?php

namespace App\Helpers;

/**
 * Class FormatHelper
 * Centraliza funções utilitárias de formatação visual e textual.
 */
class FormatHelper
{
    /**
     * Formata um CNPJ cru no padrão ##.###.###/####-##
     * 
     * @param string $cnpj
     * @return string
     */
    public static function cnpj(string $cnpj): string
    {
        $cnpj = preg_replace('/\D/', '', $cnpj);
        if (strlen($cnpj) === 14) {
            return preg_replace('/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/', '$1.$2.$3/$4-$5', $cnpj);
        }
        return $cnpj;
    }

    /**
     * Retorna a representação textual simples de um valor monetário.
     * 
     * @param float $valor
     * @return string
     */
    public static function valorPorExtenso(float $valor): string
    {
        return number_format($valor, 2, ',', '.') . " Reais";
    }
}
