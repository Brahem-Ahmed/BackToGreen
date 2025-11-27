<?php

namespace App\Entity;

use App\Repository\ZoneCollecteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ZoneCollecteRepository::class)]
class ZoneCollecte
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $adresse = null;

    #[ORM\Column]
    private ?float $latitude = null;

    #[ORM\Column]
    private ?float $longitude = null;

    #[ORM\Column]
    private ?int $capacite = null;

    #[ORM\Column(length: 255)]
    private ?string $horaires = null;

    #[ORM\Column(enumType: TypeDechet::class)]
    private ?TypeDechet $typeDechet = null;

    /**
     * @var Collection<int, CollecteDechet>
     */
    #[ORM\OneToMany(targetEntity: CollecteDechet::class, mappedBy: 'idZone')]
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

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getCapacite(): ?int
    {
        return $this->capacite;
    }

    public function setCapacite(int $capacite): static
    {
        $this->capacite = $capacite;

        return $this;
    }

    public function getHoraires(): ?string
    {
        return $this->horaires;
    }

    public function setHoraires(string $horaires): static
    {
        $this->horaires = $horaires;

        return $this;
    }

    public function getTypeDechet(): ?TypeDechet
    {
        return $this->typeDechet;
    }

    public function setTypeDechet(TypeDechet $typeDechet): static
    {
        $this->typeDechet = $typeDechet;

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
            $collecteDechet->setIdZone($this);
        }

        return $this;
    }

    public function removeCollecteDechet(CollecteDechet $collecteDechet): static
    {
        if ($this->collecteDechets->removeElement($collecteDechet)) {
            // set the owning side to null (unless already changed)
            if ($collecteDechet->getIdZone() === $this) {
                $collecteDechet->setIdZone(null);
            }
        }

        return $this;
    }
}
