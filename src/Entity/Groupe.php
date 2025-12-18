<?php

namespace App\Entity;

use App\Repository\GroupeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: GroupeRepository::class)]
class Groupe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom du groupe est obligatoire.")]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: "Le nom doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères."
    )]
    #[Assert\Regex(
        pattern: "/^[a-zA-Z0-9\s\-_]+$/",
        message: "Le nom ne peut contenir que des lettres, chiffres, espaces, tirets et underscores."
    )]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La description est obligatoire.")]
    #[Assert\Length(
        min: 10,
        max: 255,
        minMessage: "La description doit contenir au moins {{ limit }} caractères.",
        maxMessage: "La description ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTime $dateCreation = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "Le nombre de membres est obligatoire.")]
    #[Assert\Range(
        min: 1,
        max: 6,
        notInRangeMessage: "Le nombre de membres doit être entre {{ min }} et {{ max }}."
    )]
    #[Assert\PositiveOrZero(message: "Le nombre de membres ne peut pas être négatif.")]
    private ?int $nombreMembres = null;
    
    #[ORM\ManyToOne(inversedBy: 'groupes')]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $idCreateur = null;

    #[ORM\ManyToOne(inversedBy: 'groupes')]
    #[ORM\JoinColumn(name: 'id_evenement', referencedColumnName: 'id', nullable: true)]
    private ?EvenementEcologique $evenement = null;

    /**
     * @return Collection<int, MembreGroupe>
     */
    #[ORM\OneToMany(targetEntity: MembreGroupe::class, mappedBy: 'idGroupe')]
    private Collection $membreGroupes;

    public function __construct()
    {
        $this->membreGroupes = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDateCreation(): ?\DateTime
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTime $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getNombreMembres(): ?int
    {
        return $this->nombreMembres;
    }

    public function setNombreMembres(int $nombreMembres): static
    {
        $this->nombreMembres = $nombreMembres;

        return $this;
    }

    public function getIdCreateur(): ?User
    {
        return $this->idCreateur;
    }

    public function setIdCreateur(?User $idCreateur): static
    {
        $this->idCreateur = $idCreateur;

        return $this;
    }

    public function getEvenement(): ?EvenementEcologique
    {
        return $this->evenement;
    }

    public function setEvenement(?EvenementEcologique $evenement): static
    {
        $this->evenement = $evenement;

        return $this;
    }

    /**
     * @return Collection<int, MembreGroupe>
     */
    public function getMembreGroupes(): Collection
    {
        return $this->membreGroupes;
    }
public function getStatus(): string
{
    if ($this->isFull()) {
        return 'Plein';
    }
    return 'Ouvert';
}
    public function addMembreGroupe(MembreGroupe $membreGroupe): static
    {
        if (!$this->membreGroupes->contains($membreGroupe)) {
            $this->membreGroupes->add($membreGroupe);
            $membreGroupe->setIdGroupe($this);
        }

        return $this;
    }

    public function removeMembreGroupe(MembreGroupe $membreGroupe): static
    {
        if ($this->membreGroupes->removeElement($membreGroupe)) {
            // set the owning side to null (unless already changed)
            if ($membreGroupe->getIdGroupe() === $this) {
                $membreGroupe->setIdGroupe(null);
            }
        }

        return $this;
    }

    public function getActiveMembersCount(): int
    {
        return $this->membreGroupes->filter(fn(MembreGroupe $m) => $m->getStatut() === StatutMembre::MEMBRE_ACTIF)->count();
    }

    public function isFull(): bool
    {
        return $this->nombreMembres !== null && $this->getActiveMembersCount() >= $this->nombreMembres;
    }

    public function getAvailableSlots(): ?int
    {
        if ($this->nombreMembres === null) {
            return null;
        }

        return max(0, $this->nombreMembres - $this->getActiveMembersCount());
    }
}
