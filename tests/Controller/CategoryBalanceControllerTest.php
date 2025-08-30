<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Category;
use App\Entity\Transaction;
use App\Entity\User;

class CategoryBalanceControllerTest extends WebTestCase
{
    public function testGetCategoryBalance(): void
    {
        $client = static::createClient();
        $em = $client->getContainer()->get('doctrine')->getManager();

        // Crée un utilisateur
        $user = new User();
        $user->setEmail('user@example.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword('password');
        $user->setApiToken(bin2hex(random_bytes(32)));
        $em->persist($user);

        // Crée une catégorie
        $category = new Category();
        $category->setName('Alimentation');
        $em->persist($category);

        // Crée des transactions liées à la catégorie et à l'utilisateur
        $tx1 = new Transaction();
        $tx1->setAmount(50)
            ->setDescription('Déjeuner')
            ->setUser($user)
            ->setCreatedAt(new \DateTimeImmutable());

        $category->addTransaction($tx1);

        $tx2 = new Transaction();
        $tx2->setAmount(100)
            ->setDescription('Dîner')
            ->setUser($user)
            ->setCreatedAt(new \DateTimeImmutable());

        $category->addTransaction($tx2);

        $em->persist($tx1);
        $em->persist($tx2);
        $em->persist($category);

        $em->flush();

        // Connecte l'utilisateur
        $client->loginUser($user);

        $client->request('GET', '/api/categories/'.$category->getId().'/balance', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $user->getApiToken(),
        ]);

        $this->assertResponseIsSuccessful();

        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertCount(2, $responseData);

        $this->assertEquals($tx1->getAmount(), $responseData[0]['amount']);
        $this->assertEquals($tx2->getAmount(), $responseData[1]['amount']);
        $this->assertSame($user->getId(), $responseData[0]['user_id']);
    }
}
