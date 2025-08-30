<?php

namespace App\Controller;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class CategoryTotalController extends AbstractController
{
    public function __invoke(Category $category): JsonResponse
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Admin only.');
        }
        $total = 0;
        foreach ($category->getTransactions() as $tx) {
            $total += $tx->getAmount();
        }

        return $this->json([
            'category_id' => $category->getId(),
            'total_amount' => $total
        ]);
    }
}
