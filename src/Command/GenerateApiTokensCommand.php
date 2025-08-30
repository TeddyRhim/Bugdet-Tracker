<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:generate-api-tokens',
    description: 'Generates token for users',
)]
class GenerateApiTokensCommand extends Command
{
    protected static $defaultName = 'app:generate-api-tokens';
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function configure()
    {
        $this->setDescription('Génère un apiToken pour tous les utilisateurs sans token');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $users = $this->em->getRepository(User::class)->findAll();

        foreach ($users as $user) {
            if (!$user->getApiToken()) {
                $token = bin2hex(random_bytes(32));
                $user->setApiToken($token);
                $output->writeln("Token généré pour {$user->getEmail()}: $token");
            }
        }

        $this->em->flush();
        $output->writeln('Tous les tokens ont été générés.');
        return Command::SUCCESS;
    }
}
