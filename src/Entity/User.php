<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\CurrentUserController;
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
            normalizationContext: ['groups' => ['read:item:user']],
        ),
        new Post(denormalizationContext: ['groups' => ['write:item:user']]),
        new Patch(),
        new Delete(security: 'is_granted("ROLE_ADMIN")'),
        new GetCollection(
            normalizationContext: ['groups' => ['read:collection:user']],
            security: 'is_granted("ROLE_ADMIN")',
        ),
        new Get(
            normalizationContext: ['groups' => ['read:item:user']],
            paginationEnabled: false,
            name: 'currentuser',
            uriTemplate: '/currentuser',
            controller: CurrentUserController::class,
            read: false,
        ),
    ]
)]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\EntityListeners(['App\EntityListener\userListener'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['write:item:user', 'read:collection:user', 'read:item:user'])]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['write:item:user', 'read:collection:user', 'read:item:user'])]
    private ?string $email = null;

    #[ORM\Column]
    #[Groups(['write:item:user', 'read:collection:user', 'read:item:user'])]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Groups(['write:item:user'])]
    private ?string $password = null;

    #[ORM\Column(length: 20)]
    #[Groups(['write:item:user', 'read:collection:user', 'read:item:user'])]
    private ?string $nom = null;

    #[ORM\Column(length: 100)]
    #[Groups(['write:item:user', 'read:collection:user', 'read:item:user'])]
    private ?string $prenom = null;

    #[ORM\Column(length: 15)]
    #[Groups(['write:item:user', 'read:collection:user', 'read:item:user'])]
    private ?string $contact = null;

    #[ORM\Column(length: 15, nullable: true)]
    #[Groups(['write:item:user', 'read:collection:user'])]
    private ?string $codeParrain = null;

    #[ORM\Column(length: 15)]
    #[Groups(['write:item:user', 'read:collection:user', 'read:item:user'])]
    private ?string $codeParrainage = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['write:item:user', 'read:collection:user', 'read:item:user'])]
    private ?int $solde = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'creditedId', targetEntity: Transaction::class, orphanRemoval: true)]
    #[Groups(['read:item:user', 'read:collection:user', 'read:item:user'])]
    private Collection $creditedId;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

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

    public function getSolde(): ?int
    {
        return $this->solde;
    }

    public function setSolde(?int $solde): static
    {
        $this->solde = $solde;

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

    /**
     * @return Collection<int, Transaction>
     */
    public function getCreditedId(): Collection
    {
        return $this->creditedId;
    }

    public function addCreditedId(Transaction $creditedId): static
    {
        if (!$this->creditedId->contains($creditedId)) {
            $this->creditedId->add($creditedId);
            $creditedId->setCreditedId($this);
        }

        return $this;
    }

    public function removeCreditedId(Transaction $creditedId): static
    {
        if ($this->creditedId->removeElement($creditedId)) {
            // set the owning side to null (unless already changed)
            if ($creditedId->getCreditedId() === $this) {
                $creditedId->setCreditedId(null);
            }
        }

        return $this;
    }
}
