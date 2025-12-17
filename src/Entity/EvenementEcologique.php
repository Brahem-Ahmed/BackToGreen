<?php

namespace App\Entity;

use App\Repository\EvenementEcologiqueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use DateTime;
use DateTimeInterface;

#[ORM\Entity(repositoryClass: EvenementEcologiqueRepository::class)]
class EvenementEcologique
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $dateFin = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lieu = null;

    #[ORM\Column(nullable: true)]
    private ?int $capaciteMax = null;

    #[ORM\Column(type: 'string', enumType: CategorieEvenement::class, nullable: true)]
    private ?CategorieEvenement $categorie = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $idOrganisateur = null;

    #[ORM\ManyToOne(targetEntity: Groupe::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Groupe $groupe = null;

    /** @var Collection<int, Participation> */
    #[ORM\OneToMany(mappedBy: 'idEvenement', targetEntity: Participation::class)]
    private Collection $participations;

    /** @var Collection<int, Avis> */
    #[ORM\OneToMany(mappedBy: 'idEvenement', targetEntity: Avis::class)]
    private Collection $avis;

    public function __construct()
    {
        $this->participations = new ArrayCollection();
        $this->avis = new ArrayCollection();
    }

    // ───── GETTERS & SETTERS ─────

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getDateDebut(): ?DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface|string|null $dateDebut): self
    {
        if (is_string($dateDebut)) {
            $this->dateDebut = new \DateTime($dateDebut);
        } else {
            $this->dateDebut = $dateDebut;
        }
        return $this;
    }

    public function getDateFin(): ?DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeInterface|string|null $dateFin): self
    {
        if (is_string($dateFin)) {
            $this->dateFin = new \DateTime($dateFin);
        } else {
            $this->dateFin = $dateFin;
        }
        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(?string $lieu): self
    {
        $this->lieu = $lieu;
        return $this;
    }

    public function getCapaciteMax(): ?int
    {
        return $this->capaciteMax;
    }

    public function setCapaciteMax(?int $capaciteMax): self
    {
        $this->capaciteMax = $capaciteMax;
        return $this;
    }

    public function getCategorie(): ?CategorieEvenement
    {
        return $this->categorie;
    }

    public function setCategorie(?CategorieEvenement $categorie): self
    {
        $this->categorie = $categorie;
        return $this;
    }

    public function getIdOrganisateur(): ?User
    {
        return $this->idOrganisateur;
    }

    public function setIdOrganisateur(?User $idOrganisateur): self
    {
        $this->idOrganisateur = $idOrganisateur;
        return $this;
    }

    public function getGroupe(): ?Groupe
    {
        return $this->groupe;
    }

    public function setGroupe(?Groupe $groupe): self
    {
        $this->groupe = $groupe;
        return $this;
    }

    /** @return Collection<int, Participation> */
    public function getParticipations(): Collection
    {
        return $this->participations;
    }

    public function addParticipation(Participation $participation): self
    {
        if (!$this->participations->contains($participation)) {
            $this->participations->add($participation);
            $participation->setIdEvenement($this);
        }
        return $this;
    }

    public function removeParticipation(Participation $participation): self
    {
        if ($this->participations->removeElement($participation)) {
            if ($participation->getIdEvenement() === $this) {
                $participation->setIdEvenement(null);
            }
        }
        return $this;
    }

    /** @return Collection<int, Avis> */
    public function getAvis(): Collection
    {
        return $this->avis;
    }

    public function addAvi(Avis $avi): self
    {
        if (!$this->avis->contains($avi)) {
            $this->avis->add($avi);
            $avi->setIdEvenement($this);
        }
        return $this;
    }

    public function removeAvi(Avis $avi): self
    {
        if ($this->avis->removeElement($avi)) {
            if ($avi->getIdEvenement() === $this) {
                $avi->setIdEvenement(null);
            }
        }
        return $this;
    }
}
