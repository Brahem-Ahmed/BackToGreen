<?php

namespace App\Entity;

use App\Repository\ParticipationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ParticipationRepository::class)]
class Participation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id', nullable: false)]
    #[Assert\NotNull(message: 'L\'utilisateur est obligatoire')]
    private ?User $idUser = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\NotNull(message: 'La date d\'inscription est obligatoire')]
    #[Assert\Type(\DateTime::class, message: 'La date d\'inscription doit être une date valide')]
    private ?\DateTime $dateInscription = null;

    #[ORM\Column(enumType: StatutParticipation::class)]
    #[Assert\NotNull(message: 'Le statut est obligatoire')]
    private ?StatutParticipation $statut = null;

    #[ORM\ManyToOne(inversedBy: 'participations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'L\'événement est obligatoire')]
    private ?EvenementEcologique $idEvenement = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\Length(
        min: 3,
        max: 1000,
        minMessage: 'Le commentaire doit contenir au moins {{ limit }} caractères',
        maxMessage: 'Le commentaire ne peut pas dépasser {{ limit }} caractères'
    )]
    private ?string $commentaire = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDateInscription(): ?\DateTime
    {
        return $this->dateInscription;
    }

    public function setDateInscription(?\DateTime $dateInscription): static
    {
        $this->dateInscription = $dateInscription;

        return $this;
    }

    public function getStatut(): ?StatutParticipation
    {
        return $this->statut;
    }

    public function setStatut(StatutParticipation $statut): static
    {
        $this->statut = $statut;

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

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): static
    {
        $this->commentaire = $commentaire;

        return $this;
    }
}
