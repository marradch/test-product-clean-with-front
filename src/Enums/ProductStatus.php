<?php

namespace App\Enums;

enum ProductStatus: string
{
    case Available = 'available';
    case OutOfStock = 'out_of_stock';
    case Archived = 'archived';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}