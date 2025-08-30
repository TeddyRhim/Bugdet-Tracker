<?php

namespace App\EventSubscriber;

use App\Entity\Transaction;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Events;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class TransactionHighSubscriber implements EventSubscriber
{
    public function __construct(private LoggerInterface $logger, private MailerInterface $mailer) {}

    public function getSubscribedEvents(): array
    {
        return [Events::postPersist];
    }

    public function postPersist(PostPersistEventArgs $args): void
    {
        $entity = $args->getObject();
        if (!$entity instanceof Transaction) {
            return;
        }

        if ($entity->getAmount() > 1000) {
            $this->logger->info("Transaction élevée détectée : ID {$entity->getId()} montant {$entity->getAmount()}");
            
            $email = (new Email())
                ->from('noreply@example.com')
                ->to('admin@example.com')
                ->subject('Transaction élevée')
                ->text("Transaction ID {$entity->getId()} d'un montant de {$entity->getAmount()} € détectée.");

            $this->mailer->send($email);
        }
    }
}
