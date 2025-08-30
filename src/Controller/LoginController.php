<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class LoginController
{
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(): JsonResponse
    {
        // Symfony Security gère déjà l'authentification via json_login
        throw new \Exception('Ce code ne devrait jamais s’exécuter, c’est géré par le firewall.');
    }
}