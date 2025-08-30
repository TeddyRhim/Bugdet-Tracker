<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserBalanceController extends AbstractController
{
    public function __invoke(User $user): JsonResponse
    {
        $currentUser = $this->getUser();

        if ($currentUser->getId() !== $user->getId() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Vous ne pouvez voir le solde que pour votre propre compte.');
        }
        return $this->json([
            'id' => $user->getId(),
            'balance' => $user->getBalance(),
        ]);
    }
}
