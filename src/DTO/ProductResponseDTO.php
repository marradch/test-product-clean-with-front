<?php

namespace App\DTO;

class ProductResponseDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public float $price,
        public ?string $status,
        public int $category_id,
        public ?string $category_name,
        public array $attributes,
        public string $created_at
    ) {}
}