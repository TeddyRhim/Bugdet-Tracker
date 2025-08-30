<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TestController extends AbstractController
{
    #[Route('/api/test', name: 'api_test', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function test(): JsonResponse
    {
        $user = $this->getUser();

        $email = null;
        if ($user instanceof \App\Entity\User) {
            $email = $user->getEmail();
        }

        return $this->json([
            'ok' => true,
            'user' => $email,
        ]);
    }
}