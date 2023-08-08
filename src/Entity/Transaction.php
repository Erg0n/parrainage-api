<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use App\Repository\TransactionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * L'entité TRANSACTION qui gère toutes les transactions entre utilisateur
 */
#[ORM\Entity(repositoryClass: TransactionRepository::class)]
#[
    ApiResource(
        formats: ['json' => ['application/json']], //Spécifie le format attendu
        security: 'is_granted("ROLE_USER")', // Permet de gérer les roles
        operations: [
            new Get(normalizationContext: ['groups' => ['read:item:trans']]),
            new GetCollection(normalizationContext: ['groups' => ['read:collection:trans']]),
        ]
    ),
    ApiFilter(SearchFilter::class, properties: ['crediteur' => 'exact', 'debiteur' => 'exact'])
]
class Transaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:collection:trans', 'read:item:trans', 'read:collection:user', 'read:item:user'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['read:collection:trans', 'read:item:trans', 'read:collection:user', 'read:item:user'])]
    private ?int $montant = null;

    #[ORM\Column(length: 180)]
    #[Groups(['read:collection:trans', 'read:item:trans', 'read:collection:user', 'read:item:user'])]
    private ?string $emailSender = null;

    #[ORM\Column]
    #[Groups(['read:collection:trans', 'read:item:trans', 'read:collection:user', 'read:item:user'])]
    private ?\DateTimeImmutable $creadtedAt = null;

    #[ORM\ManyToOne(inversedBy: 'creditedId')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:collection:trans', 'read:item:trans'])]
    private ?User $creditedId = null;

    public function __construct()
    {
        $this->creadtedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): ?int
    {
        return $this->montant;
    }

    public function setMontant(int $montant): static
    {
        $this->montant = $montant;

        return $this;
    }

    public function getEmailSender(): ?string
    {
        return $this->emailSender;
    }

    public function setEmailSender(string $emailSender): static
    {
        $this->emailSender = $emailSender;

        return $this;
    }

    public function getCreadtedAt(): ?\DateTimeImmutable
    {
        return $this->creadtedAt;
    }

    public function setCreadtedAt(\DateTimeImmutable $creadtedAt): static
    {
        $this->creadtedAt = $creadtedAt;

        return $this;
    }

    public function getCreditedId(): ?User
    {
        return $this->creditedId;
    }

    public function setCreditedId(?User $creditedId): static
    {
        $this->creditedId = $creditedId;

        return $this;
    }
}
