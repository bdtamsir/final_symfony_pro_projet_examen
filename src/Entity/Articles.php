<?php

namespace App\Entity;

use App\Repository\ArticlesRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ArticlesRepository::class)]
#[ORM\EntityListeners(['App\EventListener\ArticleListener'])]

class Articles
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Le libellé est obligatoire')]
    private ?string $libelle = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Le Prix est obligatoire')]
    private ?float $prix = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'La Quantité Stock est obligatoire')]
    private ?float $qteStock = null;

    #[ORM\Column(length: 50)]
    private ?string $etat = null;

    #[ORM\ManyToMany(targetEntity: Dette::class, mappedBy: 'articles')]
    private Collection $dettes;

    /**
     * @var Collection<int, DemandeDette>
     */
    #[ORM\OneToMany(targetEntity: DemandeDette::class, mappedBy: 'articles')]
    private Collection $demandeDettes;

    public function __construct()
    {
        $this->dettes = new ArrayCollection();
        $this->demandeDettes = new ArrayCollection();
    }

    public function updateEtat(): void
    {
        $this->etat = $this->qteStock > 0 ? 'Disponible' : 'En rupture';
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

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

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getQteStock(): ?float
    {
        return $this->qteStock;
    }

    public function setQteStock(float $qteStock): static
    {
        $this->qteStock = $qteStock;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public function getDettes(): Collection
    {
        return $this->dettes;
    }

    public function addDette(Dette $dette): self
    {
        if (!$this->dettes->contains($dette)) {
            $this->dettes->add($dette);
        }
        return $this;
    }

    public function removeDette(Dette $dette): self
    {
        $this->dettes->removeElement($dette);
        return $this;
    }

    /**
     * @return Collection<int, DemandeDette>
     */
    public function getDemandeDettes(): Collection
    {
        return $this->demandeDettes;
    }

    public function addDemandeDette(DemandeDette $demandeDette): static
    {
        if (!$this->demandeDettes->contains($demandeDette)) {
            $this->demandeDettes->add($demandeDette);
            $demandeDette->setArticles($this);
        }

        return $this;
    }

    public function removeDemandeDette(DemandeDette $demandeDette): static
    {
        if ($this->demandeDettes->removeElement($demandeDette)) {
            // set the owning side to null (unless already changed)
            if ($demandeDette->getArticles() === $this) {
                $demandeDette->setArticles(null);
            }
        }

        return $this;
    }
}
