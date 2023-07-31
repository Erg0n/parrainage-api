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
#[ApiResource(
    formats: ['json' => ['application/json']], //Spécifie le format attendu
    security: 'is_granted("ROLE_USER")', // Permet de gérer les roles
    operations: [
        new Get(),
        new Post(),
        new Patch(),
        new Delete(security: 'is_granted("ROLE_ADMIN")'),
        new GetCollection(normalizationContext: ['groups' => ['read:collection:trans']])
    ]
),
ApiFilter(SearchFilter::class, properties: ['crediteur' => 'exact','debiteur' => 'exact'])]
class Transaction
{
    public function __construct() {
        $this->createdAt = new \DateTimeImmutable();
    }
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:collection:trans'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['read:collection:trans'])]
    private ?int $montant = null;

    #[ORM\Column]
    #[Groups(['read:collection:trans'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'crediteur')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:collection:trans'])]
    private ?User $crediteur = null;

    #[ORM\ManyToOne(inversedBy: 'debiteur')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:collection:trans'])]
    private ?User $debiteur = null;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCrediteur(): ?User
    {
        return $this->crediteur;
    }

    public function setCrediteur(?User $crediteur): static
    {
        $this->crediteur = $crediteur;

        return $this;
    }

    public function getDebiteur(): ?User
    {
        return $this->debiteur;
    }

    public function setDebiteur(?User $debiteur): static
    {
        $this->debiteur = $debiteur;

        return $this;
    }
}
