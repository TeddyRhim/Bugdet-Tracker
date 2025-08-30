<?php

namespace App\Tests\Entity;

use PHPUnit\Framework\TestCase;
use App\Entity\Category;
use App\Entity\Transaction;

class CategoryTest extends TestCase
{
    public function testCategorySettersAndGetters(): void
    {
        $category = new Category();
        $category->setName('Alimentation');

        $this->assertEquals('Alimentation', $category->getName());
    }

    public function testTransactionsRelation(): void
    {
        $category = new Category();
        $transaction1 = new Transaction();
        $transaction2 = new Transaction();

        // Ajouter des transactions
        $category->addTransaction($transaction1);
        $category->addTransaction($transaction2);

        // Vérifier que les transactions ont bien été ajoutées
        $this->assertCount(2, $category->getTransactions());
        $this->assertSame($category, $transaction1->getCategory());
        $this->assertSame($category, $transaction2->getCategory());

        // Supprimer une transaction
        $category->removeTransaction($transaction1);
        $this->assertCount(1, $category->getTransactions());
        $this->assertNull($transaction1->getCategory());
    }
}
