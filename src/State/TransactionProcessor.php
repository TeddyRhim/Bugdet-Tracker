<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Transaction;
use App\Repository\UserRepository;
use App\Repository\CategoryRepository;
use App\DTO\TransactionInput;
use Symfony\Bundle\SecurityBundle\Security;

class TransactionProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserRepository $userRepository,
        private CategoryRepository $categoryRepository,
        private Security $security
    ) {}

    public function process(
        mixed $data,
        Operation $operation,
        array $uriVariables = [],
        array $context = []
    ): mixed
    {
        if (!$data instanceof TransactionInput) {
            throw new \InvalidArgumentException('Invalid input');
        }
        $user = $user = $this->security->getUser();
        if (!$user) {
            throw new \RuntimeException('User not authenticated');
        }

        $category = $this->categoryRepository->find($data->categoryId);
        if (!$category) {
            throw new \RuntimeException('Category not found');
        }

        $transaction = new Transaction();
        $transaction->setAmount($data->amount);
        $transaction->setDescription($data->description);
        $transaction->setUser($user);
        $transaction->setCategory($category);
        $transaction->setCreatedAt(new \DateTimeImmutable());
        $this->em->persist($transaction);
        $this->em->flush();
        return $transaction;
    }
}