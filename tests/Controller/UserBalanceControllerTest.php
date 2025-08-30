<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\User;
use App\Entity\Transaction;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class UserBalanceControllerTest extends WebTestCase
{
    private EntityManagerInterface $em;
    private KernelBrowser $client;

    protected function setUp(): void
    {
        // $kernel = self::bootKernel();
        // $this->em = $kernel->getContainer()->get('doctrine')->getManager();
        $client = static::createClient(); // boot le kernel pour toi
        $this->em = $client->getContainer()->get('doctrine')->getManager();
        $this->client = $client;
    }

    public function testUserCanSeeOwnBalance(): void
    {
        // Création utilisateur
        $user = new User();
        $user->setEmail('user@test_balance.com')
             ->setRoles(['ROLE_USER'])
             ->setPassword('password')
             ->setApiToken(bin2hex(random_bytes(32)));


        // Transactions
        
        $transaction1 = new Transaction();
        $transaction1->setAmount(100)->setUser($user);
        $user->addTransaction($transaction1);
        $transaction2 = new Transaction();
        $transaction2->setAmount(50)->setUser($user);
        $user->addTransaction($transaction2);

        $this->em->persist($user);
        $this->em->persist($transaction1);
        $this->em->persist($transaction2);
        $this->em->flush();

        $this->client->request('GET', '/api/users/'.$user->getId().'/balance', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $user->getApiToken(),
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertSame($user->getId(), $data['id']);
        $this->assertEquals(150, $data['balance']);
    }

    public function testUserCannotSeeAnotherUserBalance(): void
    {
        $user1 = new User();
        $user1->setEmail('user1@test.com')->setRoles(['ROLE_USER'])->setPassword('password');
        $user2 = new User();
        $user2->setEmail('user2@test.com')->setRoles(['ROLE_USER'])->setPassword('password')->setApiToken(bin2hex(random_bytes(32)));

        $this->em->persist($user1);
        $this->em->persist($user2);
        $this->em->flush();

        $this->client->request('GET', '/api/users/'.$user1->getId().'/balance', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $user2->getApiToken(),
        ]);
        $this->assertResponseStatusCodeSame(403);
    }

    public function testAdminCanSeeAnyUserBalance(): void
    {
        $admin = new User();
        $admin->setEmail('admin@test.com')->setRoles(['ROLE_ADMIN'])->setPassword('password')->setApiToken(bin2hex(random_bytes(32)));
        $user = new User();
        $user->setEmail('user@test.com')->setRoles(['ROLE_USER'])->setPassword('password');

        $transaction = new Transaction();
        $transaction->setAmount(200)->setUser($user);
        $user->addTransaction($transaction);

        $this->em->persist($admin);
        $this->em->persist($user);
        $this->em->persist($transaction);
        $this->em->flush();

        $this->client->request('GET', '/api/users/'.$user->getId().'/balance', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $admin->getApiToken(),
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals(200, $data['balance']);
    }
}
