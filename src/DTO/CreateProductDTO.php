<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\PositiveOrZero;
use Symfony\Component\Validator\Constraints\NotBlank;
use App\Enums\ProductStatus;

class CreateProductDTO
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $name;

    #[Assert\NotBlank]
    #[Assert\Type(type: 'numeric')]
    #[Assert\PositiveOrZero]
    #[Assert\Range(
        notInRangeMessage: 'Цена должна быть не более 99999999.99',
        max: 99999999.99
    )]
    public float $price;

    #[Assert\NotBlank]
    #[Assert\Type('integer')]
    #[Assert\Positive]
    public int $category_id;

    #[Assert\Type('array')]
    public array $attributes;

    #[Assert\NotBlank]
    #[Assert\Choice(callback: [ProductStatus::class, 'values'])]
    public string $status;
}