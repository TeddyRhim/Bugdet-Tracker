<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Entity\Transaction;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testEmailGetterSetter(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $this->assertSame('test@example.com', $user->getEmail());
    }

    public function testRoles(): void
    {
        $user = new User();
        $user->setRoles(['ROLE_ADMIN']);
        $roles = $user->getRoles();

        $this->assertContains('ROLE_ADMIN', $roles);
        $this->assertContains('ROLE_USER', $roles); // ROLE_USER ajouté automatiquement
    }

    public function testPassword(): void
    {
        $user = new User();
        $user->setPassword('hashed_password');
        $this->assertSame('hashed_password', $user->getPassword());
    }

    public function testApiToken(): void
    {
        $user = new User();
        $user->setApiToken('123abc');
        $this->assertSame('123abc', $user->getApiToken());
    }

    public function testTransactions(): void
    {
        $user = new User();
        $transaction = new Transaction();
        $transaction->setAmount(50);

        $user->addTransaction($transaction);
        $this->assertCount(1, $user->getTransactions());

        $user->removeTransaction($transaction);
        $this->assertCount(0, $user->getTransactions());
    }

    public function testBalance(): void
    {
        $user = new User();

        $t1 = new Transaction();
        $t1->setAmount(100);

        $t2 = new Transaction();
        $t2->setAmount(50);

        $user->addTransaction($t1);
        $user->addTransaction($t2);

        $this->assertEquals(150, $user->getBalance());
    }
}
