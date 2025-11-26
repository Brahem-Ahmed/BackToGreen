<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $motDePasse = null;

    #[ORM\Column(length: 255)]
    private ?string $telephone = null;

    #[ORM\Column(length: 255)]
    private ?string $addresse = null;

    #[ORM\Column(type: 'string', enumType: RoleUser::class)]
    private ?RoleUser $role = null;

    /**
     * @var Collection<int, CollecteDechet>
     */
    #[ORM\OneToMany(targetEntity: CollecteDechet::class, mappedBy: 'idUser')]
    private Collection $collecteDechets;

    public function __construct()
    {
        $this->collecteDechets = new ArrayCollection();
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
}
