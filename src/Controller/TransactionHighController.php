<?php

namespace App\Controller;

use App\Entity\Transaction;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;

class TransactionHighController extends AbstractController
{
    public function __invoke(EntityManagerInterface $em): JsonResponse
    {
        $transactions = $em->getRepository(Transaction::class)
            ->createQueryBuilder('t')
            ->where('t.amount > :threshold')
            ->setParameter('threshold', 1000)
            ->orderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->json($transactions, 200, [], ['groups' => ['transaction:read']]);
    }
}
