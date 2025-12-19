<?php

namespace App\Entity;

use App\Repository\ReclamationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReclamationRepository::class)]
class Reclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reclamations')]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $idUser = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: "Le titre est obligatoire.")]
    #[Assert\Length(
        min: 3,
        minMessage: "Le titre doit contenir au moins {{ limit }} caractères."
    )]
    private ?string $titre = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La description est obligatoire.")]
    #[Assert\Length(
        min: 10,
        minMessage: "La description doit contenir au moins {{ limit }} caractères."
    )]
    private ?string $description = null;

    #[ORM\Column]
    //#[Assert\NotNull(message: "La date de réclamation est obligatoire.")]
    //#[Assert\Type(\DateTime::class)]
    private ?\DateTime $dateReclamation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $reponse = null;

    #[ORM\Column(type: 'string',nullable: true, enumType: PrioriteReclamation::class)]
    private ?PrioriteReclamation $priorite = null;

    #[ORM\Column(type: 'string',enumType: StatutReclamation::class)]
    #[Assert\NotNull(message: "Le statut est obligatoire.", groups: ['admin'])]
    private ?StatutReclamation $statut = null;

    public function __construct()
    {
        $this->dateReclamation = new \DateTime();
        $this->statut = StatutReclamation::EN_ATTENTE;
        $this->reponse = null; // Will be filled by admin when responding
    }

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

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(?string $titre): static
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

    public function getDateReclamation(): ?\DateTime
    {
        return $this->dateReclamation;
    }

    public function setDateReclamation(\DateTime $dateReclamation): static
    {
        $this->dateReclamation = $dateReclamation;

        return $this;
    }

    public function getReponse(): ?string
    {
        return $this->reponse;
    }

    public function setReponse(?string $reponse): static
    {
        $this->reponse = $reponse;

        return $this;
    }

    /**
     * @return PrioriteReclamation[]|null
     */
    public function getPriorite(): ?PrioriteReclamation
    {
        return $this->priorite;
    }

    public function setPriorite(?PrioriteReclamation $priorite): static
    {
        $this->priorite = $priorite;

        return $this;
    }

    public function getStatut(): ?StatutReclamation
    {
        return $this->statut;
    }

    public function setStatut(StatutReclamation $statut): static
    {
        $this->statut = $statut;

        return $this;
    }
}
