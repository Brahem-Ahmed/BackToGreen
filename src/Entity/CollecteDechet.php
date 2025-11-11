<?php

namespace App\Entity;

use App\Repository\CollecteDechetRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CollecteDechetRepository::class)]
class CollecteDechet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTime $dateCollecte = null;

    #[ORM\Column]
    private ?float $quantite = null;

    #[ORM\Column(enumType: TypeDechet::class)]
    private ?TypeDechet $typeDechet = null;

    #[ORM\Column(enumType: StatutCollecte::class)]
    private ?StatutCollecte $statut = null;

    #[ORM\ManyToOne(inversedBy: 'collecteDechets')]
    private ?ZoneCollecte $idZone = null;

    #[ORM\ManyToOne(inversedBy: 'collecteDechets')]
    private ?User $idUser = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    

    public function getDateCollecte(): ?\DateTime
    {
        return $this->dateCollecte;
    }

    public function setDateCollecte(\DateTime $dateCollecte): static
    {
        $this->dateCollecte = $dateCollecte;

        return $this;
    }

    public function getQuantite(): ?float
    {
        return $this->quantite;
    }

    public function setQuantite(float $quantite): static
    {
        $this->quantite = $quantite;

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

    public function getStatut(): ?StatutCollecte
    {
        return $this->statut;
    }

    public function setStatut(StatutCollecte $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getIdZone(): ?ZoneCollecte
    {
        return $this->idZone;
    }

    public function setIdZone(?ZoneCollecte $idZone): static
    {
        $this->idZone = $idZone;

        return $this;
    }

    public function getIdUser(): ?User
    {
        return $this->idUser;
    }

    public function setIdUser(?User $idUser): static
    {
        $this->idUser = $idUser;

        return $this;
    }
}
