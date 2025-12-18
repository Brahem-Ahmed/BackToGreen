<?php

namespace App\Entity;

use App\Repository\EvenementEcologiqueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EvenementEcologiqueRepository::class)]
class EvenementEcologique
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le titre est obligatoire")]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: "Le titre doit contenir au moins {{ limit }} caractères",
        maxMessage: "Le titre ne peut pas dépasser {{ limit }} caractères"
    )]
    #[Assert\Regex(
        pattern: "/^[a-zA-Z0-9\s\-_,.!;()]+$/",
        message: "Le titre contient des caractères non autorisés"
    )]
    private ?string $titre = null;

    #[ORM\Column(length: 1000)]
    #[Assert\NotBlank(message: "La description est obligatoire")]
    #[Assert\Length(
        min: 10,
        max: 1000,
        minMessage: "La description doit contenir au moins {{ limit }} caractères",
        maxMessage: "La description ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\NotNull(message: "La date de début est obligatoire")]
    #[Assert\Type("\\DateTimeInterface", message: "La date de début doit être une date valide")]
    #[Assert\GreaterThan(
        "today",
        message: "La date de début doit être dans le futur"
    )]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\NotNull(message: "La date de fin est obligatoire")]
    #[Assert\Type("\\DateTimeInterface", message: "La date de fin doit être une date valide")]
    #[Assert\Expression(
        "this.getDateDebut() < this.getDateFin()",
        message: "La date de fin doit être postérieure à la date de début"
    )]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le lieu est obligatoire")]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: "Le lieu doit contenir au moins {{ limit }} caractères",
        maxMessage: "Le lieu ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $lieu = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "La capacité maximale est obligatoire")]
    #[Assert\Type("integer", message: "La capacité doit être un nombre entier")]
    #[Assert\Positive(message: "La capacité maximale doit être un nombre positif")]
    #[Assert\LessThanOrEqual(
        value: 10000,
        message: "La capacité maximale ne peut pas dépasser {{ value }} participants"
    )]
    #[Assert\GreaterThan(
        value: 0,
        message: "La capacité maximale doit être au moins de 1 participant"
    )]
    private ?int $capaciteMax = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    #[Assert\NotBlank(message: "Veuillez sélectionner au moins une catégorie")]
    #[Assert\Count(
        min: 1,
        minMessage: "Veuillez sélectionner au moins une catégorie",
        max: 5,
        maxMessage: "Vous ne pouvez pas sélectionner plus de {{ limit }} catégories"
    )]
    #[Assert\All([
        new Assert\NotBlank(message: "Une catégorie ne peut pas être vide"),
        new Assert\Choice([
            'choices' => ['recycling', 'cleaning', 'tree_planting', 'education', 'conservation', 'other'],
            'message' => 'La catégorie "{{ value }}" n\'est pas valide. Choisissez parmi: recycling, cleaning, tree_planting, education, conservation, other'
        ])
    ])]
    private ?array $categorie = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "L'organisateur est obligatoire")]
    private ?User $idOrganisateur = null;

    /**
     * @var Collection<int, Participation>
     */
    #[ORM\OneToMany(targetEntity: Participation::class, mappedBy: 'idEvenement', cascade: ['remove'])]
    private Collection $participations;

    /**
     * @var Collection<int, Avis>
     */
    #[ORM\OneToMany(targetEntity: Avis::class, mappedBy: 'idEvenement', cascade: ['remove'])]
    private Collection $avis;

    public function __construct()
    {
        $this->participations = new ArrayCollection();
        $this->avis = new ArrayCollection();
        $this->categorie = [];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
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

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(?\DateTimeInterface $dateDebut): static
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(?\DateTimeInterface $dateFin): static
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): static
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getCapaciteMax(): ?int
    {
        return $this->capaciteMax;
    }

    public function setCapaciteMax(int $capaciteMax): static
    {
        $this->capaciteMax = $capaciteMax;

        return $this;
    }

    /**
     * Return categories as an array of values (strings).
     * This method is resilient to legacy serialized values stored in the DB.
     *
     * @return string[]
     */
    public function getCategorie(): array
    {
        if (is_array($this->categorie)) {
            return $this->categorie;
        }

       
        if (is_string($this->categorie)) {
            $un = @unserialize($this->categorie);
            if ($un !== false || $this->categorie === 'b:0;') {
                return is_array($un) ? $un : [];
            }

            $json = json_decode($this->categorie, true);
            if (is_array($json)) {
                return $json;
            }
        }

        return [];
    }

    public function setCategorie(array $categorie): static
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function addCategorie(string $categorie): static
    {
        if (!in_array($categorie, $this->getCategorie(), true)) {
            $this->categorie[] = $categorie;
        }

        return $this;
    }

    public function removeCategorie(string $categorie): static
    {
        $key = array_search($categorie, $this->getCategorie(), true);
        if ($key !== false) {
            unset($this->categorie[$key]);
            // Reindex array
            $this->categorie = array_values($this->categorie);
        }

        return $this;
    }

    public function getIdOrganisateur(): ?User
    {
        return $this->idOrganisateur;
    }

    public function setIdOrganisateur(?User $idOrganisateur): static
    {
        $this->idOrganisateur = $idOrganisateur;

        return $this;
    }

    /**
     * @return Collection<int, Participation>
     */
    public function getParticipations(): Collection
    {
        return $this->participations;
    }

    public function addParticipation(Participation $participation): static
    {
        if (!$this->participations->contains($participation)) {
            $this->participations->add($participation);
            $participation->setIdEvenement($this);
        }

        return $this;
    }

    public function removeParticipation(Participation $participation): static
    {
        if ($this->participations->removeElement($participation)) {
            // set the owning side to null (unless already changed)
            if ($participation->getIdEvenement() === $this) {
                $participation->setIdEvenement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Avis>
     */
    public function getAvis(): Collection
    {
        return $this->avis;
    }

    public function addAvi(Avis $avi): static
    {
        if (!$this->avis->contains($avi)) {
            $this->avis->add($avi);
            $avi->setIdEvenement($this);
        }

        return $this;
    }

    public function removeAvi(Avis $avi): static
    {
        if ($this->avis->removeElement($avi)) {
            // set the owning side to null (unless already changed)
            if ($avi->getIdEvenement() === $this) {
                $avi->setIdEvenement(null);
            }
        }

        return $this;
    }

    /**
     * Méthodes utilitaires supplémentaires
     */

    public function getNombreParticipants(): int
    {
        return $this->participations->count();
    }

    public function getPlacesRestantes(): int
    {
        return $this->capaciteMax - $this->getNombreParticipants();
    }

    public function isComplet(): bool
    {
        return $this->getNombreParticipants() >= $this->capaciteMax;
    }

    public function isEnCours(): bool
    {
        $now = new \DateTime();
        return $this->dateDebut <= $now && $this->dateFin >= $now;
    }

    public function isTermine(): bool
    {
        $now = new \DateTime();
        return $this->dateFin < $now;
    }

    public function isAVenir(): bool
    {
        $now = new \DateTime();
        return $this->dateDebut > $now;
    }

    public function getDuree(): \DateInterval
    {
        return $this->dateDebut->diff($this->dateFin);
    }

    public function getCategorieLabels(): array
    {
        $labels = [
            'recycling' => 'Recyclage',
            'cleaning' => 'Nettoyage',
            'tree_planting' => 'Plantation d\'arbres',
            'education' => 'Éducation',
            'conservation' => 'Conservation',
            'other' => 'Autre'
        ];

        $result = [];
        foreach ($this->getCategorie() as $categorie) {
            if (isset($labels[$categorie])) {
                $result[] = $labels[$categorie];
            }
        }

        return $result;
    }

    public function __toString(): string
    {
        return $this->titre ?? 'Nouvel événement';
    }
}