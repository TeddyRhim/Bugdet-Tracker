<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\TransactionRepository;



class TransactionRecentController extends AbstractController
{
    public function __invoke(TransactionRepository $repo): JsonResponse
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Admin only.');
        }
        $transactions = $repo->findBy([], ['createdAt' => 'DESC'], 10);

        return $this->json($transactions);
    }
}
