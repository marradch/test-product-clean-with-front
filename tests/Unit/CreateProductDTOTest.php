<?php

use PHPUnit\Framework\TestCase;
use App\DTO\CreateProductDTO;
use Symfony\Component\Validator\Validation;

class CreateProductDTOTest extends TestCase
{
    public function testInvalidNameTriggersValidationError(): void
    {
        $dto = new CreateProductDTO();
        $dto->name = ''; // пусто
        $dto->price = 100;
        $dto->category_id = 1;
        $dto->attributes = [];

        $validator = Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator();
        $errors = $validator->validate($dto);

        $this->assertGreaterThan(0, count($errors));
    }
}