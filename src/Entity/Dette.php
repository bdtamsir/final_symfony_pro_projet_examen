<?php

namespace App\Entity;

use App\Repository\DetteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DetteRepository::class)]
class Dette
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Le Montant est obligatoire', groups: ['controller'])]
    private ?float $montant = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Entrer un premier montant')]
    private ?float $montantVerser = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateAt = null;

    #[ORM\ManyToOne(inversedBy: 'dettes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $archived = false;

    /**
     * @var Collection<int, Paiement>
     */
    #[ORM\OneToMany(targetEntity: Paiement::class, mappedBy: 'dette', cascade: ['persist', 'remove'])]
    private Collection $paiements;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'La Quantité est obligatoire')]
    private ?float $Qte = null;

    /**
     * @var Collection<int, Articles>
     */
    #[ORM\ManyToMany(targetEntity: Articles::class, inversedBy: 'dette', cascade: ['persist', 'remove'])]
    private Collection $articles;



    public function __construct()
    {
        $this->paiements = new ArrayCollection();
        $this->articles = new ArrayCollection();
    }

    public function getMontantRestant(): float
    {
        return max(0, $this->montant - $this->montantVerser);
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): static
    {
        $this->montant = $montant;

        return $this;
    }

    public function getMontantVerser(): ?float
    {
        return $this->montantVerser;
    }

    public function setMontantVerser(float $montantVerser): static
    {
        $this->montantVerser = $montantVerser;

        return $this;
    }

    public function getDateAt(): ?\DateTimeImmutable
    {
        return $this->dateAt;
    }

    public function setDateAt(\DateTimeImmutable $dateAt): static
    {
        $this->dateAt = $dateAt;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function isArchived(): bool
    {
        return $this->archived;
    }

    public function setArchived(bool $archived): static
    {
        $this->archived = $archived;

        return $this;
    }

    /**
     * @return Collection<int, Paiement>
     */
    public function getPaiements(): Collection
    {
        return $this->paiements;
    }

    public function addPaiement(Paiement $paiement): static
    {
        if (!$this->paiements->contains($paiement)) {
            $this->paiements->add($paiement);
            $paiement->setDette($this);
        }

        return $this;
    }

    public function removePaiement(Paiement $paiement): static
    {
        if ($this->paiements->removeElement($paiement)) {
            // set the owning side to null (unless already changed)
            if ($paiement->getDette() === $this) {
                $paiement->setDette(null);
            }
        }

        return $this;
    }

    public function getQte(): ?float
    {
        return $this->Qte;
    }

    public function setQte(float $Qte): static
    {
        $this->Qte = $Qte;

        return $this;
    }

    /**
     * @return Collection<int, Articles>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Articles $articles): self
    {
        if (!$this->articles->contains($articles)) {
            $this->articles->add($articles);
            $articles->addDette($this);
        }
        return $this;
    }

    public function removeArticle(Articles $articles): self
    {
        if ($this->articles->removeElement($articles)) {
            $articles->removeDette($this);
        }
        return $this;
    }
}
