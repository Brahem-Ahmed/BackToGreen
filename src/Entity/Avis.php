<?php

namespace App\Entity;

use App\Repository\AvisRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AvisRepository::class)]
class Avis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'avis')]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $idUser = null;

    #[ORM\ManyToOne(inversedBy: 'avis')]
    #[ORM\JoinColumn(nullable: true)]
    private ?EvenementEcologique $idEvenement = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le commentaire est obligatoire.")]
#[Assert\Length(
    min: 5,
    minMessage: "Le commentaire doit contenir au moins {{ limit }} caractères."
)]
    private ?string $commentaire = null;

    #[ORM\Column]
     #[Assert\NotBlank(message: "La note est obligatoire.")]
    #[Assert\Range(
        min: 1,
        max: 5,
        notInRangeMessage: "La note doit être comprise entre {{ min }} et {{ max }}."
    )]
    private ?int $note = null;

    #[ORM\Column]
    //#[Assert\NotNull(message: "La date d'avis est obligatoire.")]
    //#[Assert\Type(\DateTime::class)]
    private ?\DateTime $dateAvis = null;


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

    public function setCommentaire(string $commentaire): static
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(int $note): static
    {
        $this->note = $note;

        return $this;
    }

    public function getDateAvis(): ?\DateTime
    {
        return $this->dateAvis;
    }

    public function setDateAvis(\DateTime $dateAvis): static
    {
        $this->dateAvis = $dateAvis;

        return $this;
    }

    
}
