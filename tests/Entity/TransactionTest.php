<?php

namespace App\Tests\Entity;

use PHPUnit\Framework\TestCase;
use App\Entity\Transaction;
use App\Entity\User;
use App\Entity\Category;

class TransactionTest extends TestCase
{
    public function testTransactionSettersAndGetters(): void
    {
        $transaction = new Transaction();

        // Création des objets liés
        $user = new User();
        $user->setEmail('test@example.com');

        $category = new Category();
        $category->setName('Alimentation');

        // Test des setters
        $transaction->setAmount(100.0);
        $transaction->setDescription('Achat supermarché');
        $transaction->setUser($user);
        $transaction->setCategory($category);

        // Test des getters avec assertSame/assertEquals
        $this->assertSame(100.0, $transaction->getAmount());
        $this->assertSame('Achat supermarché', $transaction->getDescription());
        $this->assertSame($user, $transaction->getUser());
        $this->assertSame($category, $transaction->getCategory());
    }

    public function testPrePersistSetsCreatedAt(): void
    {
        $transaction = new Transaction();
        $transaction->setCreatedAtValue();

        $this->assertInstanceOf(\DateTimeImmutable::class, $transaction->getCreatedAt());
    }

    public function testValidateAmountThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $transaction = new Transaction();
        $transaction->setAmount(-50);
        $transaction->validateAmount();
    }
}
