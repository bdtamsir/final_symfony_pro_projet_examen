<?php

namespace App\Entity;

use App\Repository\DemandeDetteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DemandeDetteRepository::class)]
class DemandeDette
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $montant = null;

    #[ORM\Column]
    private ?float $qte = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateAt = null;

    #[ORM\ManyToOne(inversedBy: 'demandeDettes')]
    private ?Client $client = null;

    #[ORM\ManyToOne(inversedBy: 'demandeDettes')]
    private ?Articles $articles = null;

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

    public function getQte(): ?float
    {
        return $this->qte;
    }

    public function setQte(float $qte): static
    {
        $this->qte = $qte;

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

    public function getArticles(): ?Articles
    {
        return $this->articles;
    }

    public function setArticles(?Articles $articles): static
    {
        $this->articles = $articles;

        return $this;
    }
}
