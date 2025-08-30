<?php

namespace App\Controller;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class CategoryBalanceController extends AbstractController
{
    public function __invoke(Category $category): JsonResponse
    {
        $transactions = [];
        foreach ($category->getTransactions() as $tx) {
            $transactions[] = [
                'id' => $tx->getId(),
                'amount' => $tx->getAmount(),
                'description' => $tx->getDescription(),
                'user_id' => $tx->getUser()->getId(),
                'created_at' => $tx->getCreatedAt()->format('Y-m-d H:i:s'),
            ];
        }

        return $this->json($transactions);
    }
}
