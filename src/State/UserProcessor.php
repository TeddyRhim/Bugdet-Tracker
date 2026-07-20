<?php

namespace App\State;

use App\DTO\UserInput;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use ApiPlatform\State\ProcessorInterface;

class UserProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function process(
        mixed $data,
        \ApiPlatform\Metadata\Operation $operation,
        array $uriVariables = [],
        array $context = []
    ): mixed {

        /** @var UserInput $data */

        $user = new User();

        $user->setEmail($data->email);

        // HASH PASSWORD PROPRE
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $data->password
        );

        $user->setPassword($hashedPassword);

        // default role
        $user->setRoles($data->roles ?? ['ROLE_USER']);

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }
}