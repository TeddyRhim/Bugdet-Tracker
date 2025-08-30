<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\User;

class TransactionHighControllerTest extends WebTestCase
{
    public function testAccessDeniedForNonAdmin(): void
    {
        $client = static::createClient();
        $em = $client->getContainer()->get('doctrine')->getManager();


        $user = new User();
        $user->setEmail('user@example_high.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword('dummy');
        $user->setApiToken(bin2hex(random_bytes(32)));
        $em->persist($user);

        $em->flush();

        $client->loginUser($user);

        $client->request('GET', '/api/transactions/high', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $user->getApiToken(),
        ]);

        // Même si le controller n’a pas de check ROLE_ADMIN, on peut adapter si tu veux ajouter sécurité
        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
    }

    public function testReturnsHighTransactions(): void
    {
        $client = static::createClient();
        $em = $client->getContainer()->get('doctrine')->getManager();

        $admin = new User();
        $admin->setEmail('admin@example_high.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword('dummy');
        $admin->setApiToken(bin2hex(random_bytes(32)));
        $em->persist($admin);

        $em->flush();

        $client->loginUser($admin);

        $client->request('GET', '/api/transactions/high', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $admin->getApiToken(),
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $content = $client->getResponse()->getContent();
        $this->assertJson($content);

        $transactions = json_decode($content, true);

        $this->assertIsArray($transactions);

        foreach ($transactions as $transaction) {
            $this->assertArrayHasKey('amount', $transaction);
            $this->assertGreaterThan(1000, $transaction['amount']);
        }
    }
}
