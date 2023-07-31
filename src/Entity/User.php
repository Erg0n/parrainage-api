<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * L'entité USER qui gère tous les utilisateurs relatif à notre système de parrainage
 */
#[UniqueEntity('email')]
#[ApiResource(
    formats: ['json' => ['application/json']], //Spécifie le format attendu
    security: 'is_granted("ROLE_USER")', // Permet de gérer les roles
    operations: [
        new Get(
            normalizationContext: ['groups' => ['read:item']],
        ),
        new Post(denormalizationContext: ['groups' => ['write:item:user']]),
        new Patch(),
        new Delete(security: 'is_granted("ROLE_ADMIN")'),
        new GetCollection(
            normalizationContext: ['groups' => ['read:collection:user']],
            security: 'is_granted("ROLE_ADMIN")',
            )
    ]
)]
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public function __construct() {
        $this->createdAt = new \DateTimeImmutable();
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:item','read:collection:user'])]
    private ?int $id = null;
    
    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['read:item','read:collection:user','write:item:user'])]
    private ?string $email = null;
    
    #[ORM\Column]
    #[Groups(['read:item','write:item:user','read:collection:user'])]
    private array $roles = [];
    
    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Groups(['write:item:user'])]
    private ?string $password = null;
    
    #[ORM\Column(length: 20)]
    #[Groups(['read:item','read:collection:user','write:item:user','read:collection:trans'])]
    private ?string $nom = null;
    
    #[ORM\Column(length: 100)]
    #[Groups(['read:item','read:collection:user','write:item:user','read:collection:trans'])]
    private ?string $prenom = null;
    
    #[ORM\Column(length: 15)]
    #[Groups(['read:item','read:collection:user','write:item:user'])]
    private ?string $contact = null;
    
    #[ORM\Column(length: 10, nullable: true)]
    #[Groups(['read:item','read:collection:user','write:item:user'])]
    private ?string $codeParrain = null;
    
    #[ORM\Column(length: 10)]
    #[Groups(['read:item','read:collection:user','write:item:user'])]
    private ?string $codeParrainage = null;
    
    #[ORM\Column]
    #[Groups(['read:item','read:collection:user'])]
    private ?\DateTimeImmutable $createdAt = null;
    
    #[ORM\Column(nullable: true)]
    #[Groups(['read:item','read:collection:user','write:item:user'])]
    private ?int $solde = null;
    
    #[ORM\OneToMany(mappedBy: 'crediteur', targetEntity: Transaction::class, orphanRemoval: true)]
    private Collection $crediteur;
    
    #[ORM\OneToMany(mappedBy: 'debiteur', targetEntity: Transaction::class, orphanRemoval: true)]
    private Collection $debiteur;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getContact(): ?string
    {
        return $this->contact;
    }

    public function setContact(string $contact): static
    {
        $this->contact = $contact;

        return $this;
    }

    public function getCodeParrain(): ?string
    {
        return $this->codeParrain;
    }

    public function setCodeParrain(?string $codeParrain): static
    {
        $this->codeParrain = $codeParrain;

        return $this;
    }

    public function getCodeParrainage(): ?string
    {
        return $this->codeParrainage;
    }

    public function setCodeParrainage(string $codeParrainage): static
    {
        $this->codeParrainage = $codeParrainage;

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

    public function getSolde(): ?int
    {
        return $this->solde;
    }

    public function setSolde(?int $solde): static
    {
        $this->solde = $solde;

        return $this;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getCrediteur(): Collection
    {
        return $this->crediteur;
    }

    public function addCrediteur(Transaction $crediteur): static
    {
        if (!$this->crediteur->contains($crediteur)) {
            $this->crediteur->add($crediteur);
            $crediteur->setCrediteur($this);
        }

        return $this;
    }

    public function removeCrediteur(Transaction $crediteur): static
    {
        if ($this->crediteur->removeElement($crediteur)) {
            // set the owning side to null (unless already changed)
            if ($crediteur->getCrediteur() === $this) {
                $crediteur->setCrediteur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getDebiteur(): Collection
    {
        return $this->debiteur;
    }

    public function addDebiteur(Transaction $debiteur): static
    {
        if (!$this->debiteur->contains($debiteur)) {
            $this->debiteur->add($debiteur);
            $debiteur->setDebiteur($this);
        }

        return $this;
    }

    public function removeDebiteur(Transaction $debiteur): static
    {
        if ($this->debiteur->removeElement($debiteur)) {
            // set the owning side to null (unless already changed)
            if ($debiteur->getDebiteur() === $this) {
                $debiteur->setDebiteur(null);
            }
        }

        return $this;
    }

}
