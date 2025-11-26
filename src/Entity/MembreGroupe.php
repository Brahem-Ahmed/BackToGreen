<?php

namespace App\Entity;

use App\Repository\MembreGroupeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MembreGroupeRepository::class)]
class MembreGroupe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'membreGroupes')]
    private ?Groupe $idGroupe = null;

    #[ORM\ManyToOne(inversedBy: 'membreGroupes')]
    private ?User $idUser = null;

    #[ORM\Column]
    private ?\DateTime $dateAdhesion = null;

    #[ORM\Column(enumType: StatutMembre::class)]
    private ?StatutMembre $statut = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdGroupe(): ?groupe
    {
        return $this->idGroupe;
    }

    public function setIdGroupe(?groupe $idGroupe): static
    {
        $this->idGroupe = $idGroupe;

        return $this;
    }

    public function getIdUser(): ?user
    {
        return $this->idUser;
    }

    public function setIdUser(?user $idUser): static
    {
        $this->idUser = $idUser;

        return $this;
    }

    public function getDateAdhesion(): ?\DateTime
    {
        return $this->dateAdhesion;
    }

    public function setDateAdhesion(\DateTime $dateAdhesion): static
    {
        $this->dateAdhesion = $dateAdhesion;

        return $this;
    }

    public function getStatut(): ?StatutMembre
    {
        return $this->statut;
    }

    public function setStatut(StatutMembre $statut): static
    {
        $this->statut = $statut;

        return $this;
    }
}
