<?php

namespace App\Entity;

use App\Repository\EvenementEcologiqueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EvenementEcologiqueRepository::class)]
class EvenementEcologique
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column]
    private ?string $dateDebut = null;

    #[ORM\Column]
    private ?string $dateFin = null;

    #[ORM\Column(length: 255)]
    private ?string $lieu = null;

    #[ORM\Column]
    private ?int $capaciteMax = null;

    

    #[ORM\Column(type: Types::SIMPLE_ARRAY, enumType: CategorieEvenement::class)]
    private array $categorie = [];

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $idOrganisateur = null;

    /**
     * @var Collection<int, Participation>
     */
    #[ORM\OneToMany(targetEntity: Participation::class, mappedBy: 'idEvenement')]
    private Collection $participations;

    /**
     * @var Collection<int, Avis>
     */
    #[ORM\OneToMany(targetEntity: Avis::class, mappedBy: 'idEvenement')]
    private Collection $avis;

    public function __construct()
    {
        $this->participations = new ArrayCollection();
        $this->avis = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

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

    public function getDateDebut(): ?\DateTime
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTime $dateDebut): static
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTime
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTime $dateFin): static
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): static
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getCapaciteMax(): ?int
    {
        return $this->capaciteMax;
    }

    public function setCapaciteMax(int $capaciteMax): static
    {
        $this->capaciteMax = $capaciteMax;

        return $this;
    }

    

 
    /**
     * @return CategorieEvenement[]
     */
    public function getCategorie(): array
    {
        return $this->categorie;
    }

    public function setCategorie(array $categorie): static
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getIdOrganisateur(): ?User
    {
        return $this->idOrganisateur;
    }

    public function setIdOrganisateur(?User $idOrganisateur): static
    {
        $this->idOrganisateur = $idOrganisateur;

        return $this;
    }

    /**
     * @return Collection<int, Participation>
     */
    public function getParticipations(): Collection
    {
        return $this->participations;
    }

    public function addParticipation(Participation $participation): static
    {
        if (!$this->participations->contains($participation)) {
            $this->participations->add($participation);
            $participation->setIdEvenement($this);
        }

        return $this;
    }

    public function removeParticipation(Participation $participation): static
    {
        if ($this->participations->removeElement($participation)) {
            // set the owning side to null (unless already changed)
            if ($participation->getIdEvenement() === $this) {
                $participation->setIdEvenement(null);
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
            $avi->setIdEvenement($this);
        }

        return $this;
    }

    public function removeAvi(Avis $avi): static
    {
        if ($this->avis->removeElement($avi)) {
            // set the owning side to null (unless already changed)
            if ($avi->getIdEvenement() === $this) {
                $avi->setIdEvenement(null);
            }
        }

        return $this;
    }

   

   
}
