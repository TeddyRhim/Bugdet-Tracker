<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Controller\TransactionRecentController;
use App\Controller\TransactionHighController;





#[ORM\Entity(repositoryClass: TransactionRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/transactions/recent',
            controller: TransactionRecentController::class,
            name: 'transaction_recent',
            read: false
        ),
        new Get(
            uriTemplate: '/transactions/high',
            controller: TransactionHighController::class,
            name: 'transaction_high',
            read: false
        ),
        new Post(
            security: "is_granted('ROLE_ADMIN')",
            securityMessage: "Seuls les admins peuvent créer une transaction."
        ),
        new Get()
    ],
    normalizationContext: [
        'groups' => ['transaction:read'],
        'enable_max_depth' => true, // pour éviter les erreurs 500 des jointures qui tournent en boucle
    ],
    denormalizationContext: ['groups' => ['transaction:write']]
)]
class Transaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read'])]

    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['user:read', 'user:write'])]

    private ?float $amount = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['user:read', 'user:write'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'transactions')]
    #[Groups(['user:read', 'user:write'])]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'transactions')]
    #[Groups(['user:read', 'user:write'])]
    private ?Category $category = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(?float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }
}
