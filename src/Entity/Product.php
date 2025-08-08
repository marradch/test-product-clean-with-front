<?php

namespace App\Entity;

class Product
{
    public function __construct(
        public readonly ?int $id,
        public ?string $name,
        public ?float $price,
        public ?string $status,
        public ?int $categoryId,
        public ?array $attributes = [],
    ) {}
}