<?php

namespace App\Enums;

enum DocumentType: string
{
    case CPF = 'CPF';
    case CNPJ = 'CNPJ';

    /**
     * Retorna um array associativo com os valores para uso em formulários.
     *
     * @return array<string, string>
     */
    public static function toArray(): array
    {
        return array_column(self::cases(), 'name', 'value');
    }

    /**
     * Retorna o nome amigável para exibição.
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::CPF => 'CPF (Pessoa Física)',
            self::CNPJ => 'CNPJ (Pessoa Jurídica)',
        };
    }
}
