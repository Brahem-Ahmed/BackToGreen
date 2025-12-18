<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'This email address is already in use.')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Last name is required.')]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: 'Last name must be at least {{ limit }} characters long.',
        maxMessage: 'Last name cannot be longer than {{ limit }} characters.'
    )]
    #[Assert\Regex(
        pattern: '/^[a-zA-ZÀ-ÿ\s\'-]+$/u',
        message: 'Last name can only contain letters, spaces, hyphens and apostrophes.'
    )]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'First name is required.')]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: 'First name must be at least {{ limit }} characters long.',
        maxMessage: 'First name cannot be longer than {{ limit }} characters.'
    )]
    #[Assert\Regex(
        pattern: '/^[a-zA-ZÀ-ÿ\s\'-]+$/u',
        message: 'First name can only contain letters, spaces, hyphens and apostrophes.'
    )]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Email address is required.')]
    #[Assert\Email(message: 'Please enter a valid email address.')]
    #[Assert\Length(max: 255, maxMessage: 'Email address cannot be longer than {{ limit }} characters.')]
    private ?string $email = null;

    #[ORM\Column(name: 'mot_de_passe', length: 255)]
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Password is required.')]
    #[Assert\Length(
        min: 8,
        max: 255,
        minMessage: 'Password must be at least {{ limit }} characters long.',
        maxMessage: 'Password cannot be longer than {{ limit }} characters.'
    )]
    #[Assert\Regex(
        pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/',
        message: 'Password must contain at least one lowercase letter, one uppercase letter, and one number.'
    )]
    private ?string $motDePasse = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Phone number is required.')]
    #[Assert\Regex(
        pattern: '/^[\+]?[0-9\s\-\(\)\.]{8,20}$/',
        message: 'Please enter a valid phone number (8-20 digits).'
    )]
    private ?string $telephone = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Address is required.')]
    #[Assert\Length(
        min: 5,
        max: 500,
        minMessage: 'Address must be at least {{ limit }} characters long.',
        maxMessage: 'Address cannot be longer than {{ limit }} characters.'
    )]
    private ?string $addresse = null;

    #[ORM\Column(type: 'string', enumType: RoleUser::class)]
    #[Assert\NotNull(message: 'Please select a user role.')]
    private ?RoleUser $role = null;

    /**
     * @var Collection<int, CollecteDechet>
     */
    #[ORM\OneToMany(targetEntity: CollecteDechet::class, mappedBy: 'idUser')]
    private Collection $collecteDechets;

    /**
     * @var Collection<int, Reclamation>
     */
    #[ORM\OneToMany(targetEntity: Reclamation::class, mappedBy: 'idUser')]
    private Collection $reclamations;

    /**
     * @var Collection<int, Avis>
     */
    #[ORM\OneToMany(targetEntity: Avis::class, mappedBy: 'idUser')]
    private Collection $avis;

    public function __construct()
    {
        $this->collecteDechets = new ArrayCollection();
        $this->reclamations = new ArrayCollection();
        $this->avis = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getMotDePasse(): ?string
    {
        return $this->motDePasse;
    }

    public function setMotDePasse(string $motDePasse): static
    {
        $this->motDePasse = $motDePasse;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getAddresse(): ?string
    {
        return $this->addresse;
    }

    public function setAddresse(string $addresse): static
    {
        $this->addresse = $addresse;

        return $this;
    }

    public function getRole(): ?RoleUser
    {
        return $this->role;
    }

    public function setRole(RoleUser $role): static
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return Collection<int, CollecteDechet>
     */
    public function getCollecteDechets(): Collection
    {
        return $this->collecteDechets;
    }

    public function addCollecteDechet(CollecteDechet $collecteDechet): static
    {
        if (!$this->collecteDechets->contains($collecteDechet)) {
            $this->collecteDechets->add($collecteDechet);
            $collecteDechet->setIdUser($this);
        }

        return $this;
    }

    public function removeCollecteDechet(CollecteDechet $collecteDechet): static
    {
        if ($this->collecteDechets->removeElement($collecteDechet)) {
            // set the owning side to null (unless already changed)
            if ($collecteDechet->getIdUser() === $this) {
                $collecteDechet->setIdUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Reclamation>
     */
    public function getReclamations(): Collection
    {
        return $this->reclamations;
    }

    public function addReclamation(Reclamation $reclamation): static
    {
        if (!$this->reclamations->contains($reclamation)) {
            $this->reclamations->add($reclamation);
            $reclamation->setIdUser($this);
        }

        return $this;
    }

    public function removeReclamation(Reclamation $reclamation): static
    {
        if ($this->reclamations->removeElement($reclamation)) {
            // set the owning side to null (unless already changed)
            if ($reclamation->getIdUser() === $this) {
                $reclamation->setIdUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Avis>
     */
    public function getAvis(): Collection
    {
        return $this->avis;
    }

    public function addAvi(Avis $avi): static
    {
        if (!$this->avis->contains($avi)) {
            $this->avis->add($avi);
            $avi->setIdUser($this);
        }

        return $this;
    }

    public function removeAvi(Avis $avi): static
    {
        if ($this->avis->removeElement($avi)) {
            // set the owning side to null (unless already changed)
            if ($avi->getIdUser() === $this) {
                $avi->setIdUser(null);
            }
        }

        return $this;
    }

    // UserInterface methods
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        // Map RoleUser enum to Symfony roles
        $roles = ['ROLE_USER'];
        
        if ($this->role === RoleUser::ADMIN) {
            $roles[] = 'ROLE_ADMIN';
        }
        
        return array_unique($roles);
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
    }

    // PasswordAuthenticatedUserInterface method
    public function getPassword(): string
    {
        return $this->motDePasse;
    }
}
