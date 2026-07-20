<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class UserInput
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 180)]
    public ?string $email = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 6)]
    public ?string $password = null;

    public ?array $roles = null;
}