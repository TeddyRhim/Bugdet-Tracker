<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserSubscriber implements EventSubscriber
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function getSubscribedEvents(): array
    {
        return [Events::prePersist, Events::preUpdate];
    }

    public function prePersist(PrePersistEventArgs $args): void
    {
        $this->handlePassword($args);
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $this->handlePassword($args);

        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $args->getObjectManager();
        $uow = $em->getUnitOfWork();
        $meta = $em->getClassMetadata(User::class);
        /** @var User $user */
        $user = $args->getObject();
        $uow->recomputeSingleEntityChangeSet($meta, $user);
    }

    private function handlePassword($args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof User) {
            return;
        }

        if ($entity->getPassword() && !str_starts_with($entity->getPassword(), '$2y$')) {
            $hashed = $this->passwordHasher->hashPassword($entity, $entity->getPassword());
            $entity->setPassword($hashed);
        }
    }
}
