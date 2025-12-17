<?php

namespace App\Entity;

use App\Repository\MembreGroupeRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MembreGroupeRepository::class)]
#[ORM\UniqueConstraint(name: 'unique_user_groupe', columns: ['id_user', 'id_groupe'])]
class MembreGroupe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'membreGroupes')]
    #[ORM\JoinColumn(name: 'id_groupe', referencedColumnName: 'id', nullable: false)]
    #[Assert\NotNull(message: "Please select a group.")]
    private ?Groupe $idGroupe = null;

    #[ORM\ManyToOne(inversedBy: 'membreGroupes')]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id', nullable: false)]
    #[Assert\NotNull(message: "Please select a user.")]
    private ?User $idUser = null;

    #[ORM\ManyToOne(targetEntity: EvenementEcologique::class)]
    #[ORM\JoinColumn(name: 'id_evenement', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?EvenementEcologique $idEvenement = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    
    private ?\DateTimeInterface $dateAdhesion = null;

    #[ORM\Column(type: 'string', enumType: StatutMembre::class)]
    #[Assert\NotNull(message: "The status is required.")]
    private ?StatutMembre $statut = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdGroupe(): ?Groupe
    {
        return $this->idGroupe;
    }

    public function setIdGroupe(?Groupe $idGroupe): static
    {
        $this->idGroupe = $idGroupe;
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

    
    public function getIdEvenement(): ?EvenementEcologique
    {
        return $this->idEvenement;
    }

    public function setIdEvenement(?EvenementEcologique $idEvenement): static
    {
        $this->idEvenement = $idEvenement;
        return $this;
    }

    public function getDateAdhesion(): ?\DateTimeInterface
    {
        return $this->dateAdhesion;
    }

    public function setDateAdhesion(\DateTimeInterface $dateAdhesion): static
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
