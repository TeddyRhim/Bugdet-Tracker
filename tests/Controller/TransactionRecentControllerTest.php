<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\User;

class TransactionRecentControllerTest extends WebTestCase
{
    public function testAccessDeniedForNonAdmin(): void
    {
        $client = static::createClient();
        $em = $client->getContainer()->get('doctrine')->getManager();

        // Création d'un utilisateur "simple" pour simuler la connexion
        $user = new User();
        $user->setEmail('user@example_recent.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword('dummy'); // password hash not relevant for test
        $user->setApiToken(bin2hex(random_bytes(32)));
        $em->persist($user);

        $em->flush();


        $client->loginUser($user);

        $client->request('GET', '/api/transactions/recent', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $user->getApiToken(),
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testAccessGrantedForAdmin(): void
    {
        $client = static::createClient();
        $em = $client->getContainer()->get('doctrine')->getManager();

        $admin = new User();
        $admin->setEmail('admin@example_recent.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword('dummy');
        $admin->setApiToken(bin2hex(random_bytes(32)));
        $em->persist($admin);

        $em->flush();


        $client->loginUser($admin);

        $client->request('GET', '/api/transactions/recent', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $admin->getApiToken(),
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $content = $client->getResponse()->getContent();
        $this->assertJson($content);

        $data = json_decode($content, true);
        $this->assertIsArray($data);
        $this->assertLessThanOrEqual(10, count($data)); // Vérifie que max 10 transactions sont retournées
    }
}
