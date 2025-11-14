<?php

namespace App\Entity;

use App\Repository\ReclamationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReclamationRepository::class)]
class Reclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reclamations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $idUser = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $titre = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTime $dateReclamation = null;

    #[ORM\Column(length: 255)]
    private ?string $reponse = null;

    #[ORM\Column(nullable: true, enumType: PrioriteReclamation::class)]
    private ?PrioriteReclamation $priorite = null;

    #[ORM\Column(enumType: StatutReclamation::class)]
    private ?StatutReclamation $statut = null;

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

    public function setReponse(string $reponse): static
    {
        $this->reponse = $reponse;

        return $this;
    }

    /**
     * @return PrioriteReclamation[]|null
     */
    public function getPriorite(): ?array
    {
        return $this->priorite;
    }

    public function setPriorite(?array $priorite): static
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
