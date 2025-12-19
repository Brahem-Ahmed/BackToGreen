<?php

namespace App\Entity;

use App\Repository\GroupeRequestRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroupeRequestRepository::class)]
class GroupeRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?EvenementEcologique $evenement = null;

    #[ORM\Column(length: 50)]
    private ?string $statut = 'PENDING'; // PENDING, APPROVED, REJECTED

    #[ORM\ManyToOne]
    private ?Groupe $assignedGroupe = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $processedAt = null;

    #[ORM\ManyToOne]
    private ?User $processedBy = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getEvenement(): ?EvenementEcologique
    {
        return $this->evenement;
    }

    public function setEvenement(?EvenementEcologique $evenement): static
    {
        $this->evenement = $evenement;
        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;
        return $this;
    }

    public function getAssignedGroupe(): ?Groupe
    {
        return $this->assignedGroupe;
    }

    public function setAssignedGroupe(?Groupe $assignedGroupe): static
    {
        $this->assignedGroupe = $assignedGroupe;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getProcessedAt(): ?\DateTimeImmutable
    {
        return $this->processedAt;
    }

    public function setProcessedAt(?\DateTimeImmutable $processedAt): static
    {
        $this->processedAt = $processedAt;
        return $this;
    }

    public function getProcessedBy(): ?User
    {
        return $this->processedBy;
    }

    public function setProcessedBy(?User $processedBy): static
    {
        $this->processedBy = $processedBy;
        return $this;
    }
}
