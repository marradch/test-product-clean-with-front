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

class UpdateProductDTO
{
    #[Assert\Length(max: 255)]
    public ?string $name = null;

    #[Assert\Type('numeric')]
    #[Assert\PositiveOrZero]
    #[Assert\Range(max: 99999999.99)]
    public ?float $price = null;

    #[Assert\Type('integer')]
    #[Assert\Positive]
    public ?int $category_id = null;

    #[Assert\Choice(callback: [ProductStatus::class, 'values'])]
    public ?string $status = null;

    #[Assert\Type('array')]
    public ?array $attributes = null;
}