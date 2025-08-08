<?php

namespace App\Enums;

enum ProductSort: string
{
    case PRICE_ASC = 'price_asc';    // Сначала дешевле
    case ALPHABET = 'alphabet';      // По алфавиту
    case NEWEST = 'newest';          // Сначала новые

    public function label(): string
    {
        return match($this) {
                self::PRICE_ASC => 'Сначала дешевле',
                self::ALPHABET => 'По алфавиту',
                self::NEWEST => 'Сначала новые',
            };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        $result = [];
        foreach (self::cases() as $case) {
            $result[$case->value] = $case->label();
        }
        return $result;
    }
}