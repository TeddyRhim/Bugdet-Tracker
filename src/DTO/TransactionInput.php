<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class TransactionInput
{
    #[Assert\NotNull]
    #[Assert\Positive]
    public ?float $amount = null;

    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public ?string $description = null;

    #[Assert\NotNull]
    #[Assert\Positive]
    public ?int $categoryId = null;
}