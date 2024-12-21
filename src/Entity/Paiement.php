<?php

namespace App\Entity;

use App\Repository\PaiementRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\DateImmutableType;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PaiementRepository::class)]
class Paiement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Le montant est obligatoire')]
    private ?float $montant = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateAt = null;

    #[ORM\ManyToOne(inversedBy: 'paiements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Dette $dette = null;

    public function __construct()
    {
        $this->dateAt= new DateTimeImmutable();
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

    public function getDateAt(): ?\DateTimeImmutable
    {
        return $this->dateAt;
    }

    public function setDateAt(\DateTimeImmutable $dateAt): static
    {
        $this->dateAt = $dateAt;

        return $this;
    }

    public function getDette(): ?Dette
    {
        return $this->dette;
    }

    public function setDette(?Dette $dette): static
    {
        $this->dette = $dette;

        return $this;
    }
}
