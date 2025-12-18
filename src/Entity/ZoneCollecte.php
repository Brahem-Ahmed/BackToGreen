<?php

namespace App\Entity;

use App\Repository\ZoneCollecteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ZoneCollecteRepository::class)]
class ZoneCollecte
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Zone name is required.')]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: 'Zone name must be at least {{ limit }} characters long.',
        maxMessage: 'Zone name cannot be longer than {{ limit }} characters.'
    )]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Address is required.')]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: 'Address must be at least {{ limit }} characters long.',
        maxMessage: 'Address cannot be longer than {{ limit }} characters.'
    )]
    private ?string $adresse = null;

    #[ORM\Column]
    #[Assert\NotNull(message: 'Latitude is required.')]
    #[Assert\Type(type: 'numeric', message: 'Latitude must be a number.')]
    #[Assert\Range(min: -90, max: 90, notInRangeMessage: 'Latitude must be between -90 and 90.')]
    private ?float $latitude = null;

    #[ORM\Column]
    #[Assert\NotNull(message: 'Longitude is required.')]
    #[Assert\Type(type: 'numeric', message: 'Longitude must be a number.')]
    #[Assert\Range(min: -180, max: 180, notInRangeMessage: 'Longitude must be between -180 and 180.')]
    private ?float $longitude = null;

    #[ORM\Column]
    #[Assert\NotNull(message: 'Capacity is required.')]
    #[Assert\Type(type: 'numeric', message: 'Capacity must be a number.')]
    #[Assert\Positive(message: 'Capacity must be positive.')]
    private ?int $capacite = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Operating hours are required.')]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: 'Operating hours must be at least {{ limit }} characters long.',
        maxMessage: 'Operating hours cannot be longer than {{ limit }} characters.'
    )]
    private ?string $horaires = null;

    #[ORM\Column(enumType: TypeDechet::class)]
    #[Assert\NotNull(message: 'Waste type is required.')]
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

    public function __toString(): string
    {
        return $this->nom ?? (string) $this->id;
    }
}
