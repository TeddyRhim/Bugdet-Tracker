<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Category;
use App\Entity\Transaction;
use App\Entity\User;

class CategoryTotalControllerTest extends WebTestCase
{
    public function testAdminCanGetTotal(): void
    {
        $client = static::createClient();
        $em = $client->getContainer()->get('doctrine')->getManager();

        // Crée un utilisateur admin
        $admin = new User();
        $admin->setEmail('admin@example_total.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword('password');
        $admin->setApiToken(bin2hex(random_bytes(32)));
        $em->persist($admin);

        // Crée une catégorie
        $category = new Category();
        $category->setName('Test Cat');
        $em->persist($category);

        // Crée quelques transactions liées à la catégorie
        $tx1 = new Transaction();
        $tx1->setAmount(100)
            ->setCategory($category);

        $category->addTransaction($tx1);

        $tx2 = new Transaction();
        $tx2->setAmount(200)
            ->setCategory($category);

        $category->addTransaction($tx2);

        $em->persist($tx1);
        $em->persist($tx2);
        $em->persist($category);

        $em->flush();

        // Connecte l'admin (ou configure un token API si nécessaire)
        $client->loginUser($admin);

        $client->request('GET', '/api/categories/'.$category->getId().'/total', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $admin->getApiToken(),
        ]);

        $this->assertResponseIsSuccessful();

        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame($category->getId(), $responseData['category_id']);
        $this->assertSame(300, $responseData['total_amount']);
    }

    public function testNonAdminCannotGetTotal(): void
    {
        $client = static::createClient();
        $em = $client->getContainer()->get('doctrine')->getManager();

        $user = new User();
        $user->setEmail('user@example_total.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword('password');
        $user->setApiToken(bin2hex(random_bytes(32)));
        $em->persist($user);

        $category = new Category();
        $category->setName('Test Dog');
        $em->persist($category);

        $em->flush();

        $client->loginUser($user);

        $client->request('GET', '/api/categories/'.$category->getId().'/total', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $user->getApiToken(),
        ]);

        $this->assertResponseStatusCodeSame(403);
    }
}
