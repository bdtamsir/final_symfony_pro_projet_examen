<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints\Unique;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
#[UniqueEntity('surname', message: 'Ce Surname existe déjà')]
#[UniqueEntity('telephone', message: 'Ce Numéro de téléphone existe déjà')]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    #[Assert\NotBlank(message: 'Le Surname est obligatoire')]
    private ?string $surname = null;

    #[ORM\Column(length: 50, unique: true)]
    #[Assert\NotBlank(message: 'Le Numéro de téléphone est obligatoire')]
    private ?string $telephone = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Adresse obligatoire')]
    private ?string $adresse = null;

    #[ORM\OneToOne(mappedBy: 'client', cascade: ['persist', 'remove'])]
    private ?User $user = null;

    /**
     * @var Collection<int, Dette>
     */
    #[ORM\OneToMany(targetEntity: Dette::class, mappedBy: 'client',cascade: ['persist', 'remove'])]
    private Collection $dettes;

    /**
     * @var Collection<int, DemandeDette>
     */
    #[ORM\OneToMany(targetEntity: DemandeDette::class, mappedBy: 'client')]
    private Collection $demandeDettes;

    public function __construct()
    {
        $this->dettes = new ArrayCollection();
        $this->demandeDettes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): static
    {
        $this->surname = $surname;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        // unset the owning side of the relation if necessary
        if ($user === null && $this->user !== null) {
            $this->user->setClient(null);
        }

        // set the owning side of the relation if necessary
        if ($user !== null && $user->getClient() !== $this) {
            $user->setClient($this);
        }

        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Dette>
     */
    public function getDettes(): Collection
    {
        return $this->dettes;
    }

    public function addDette(Dette $dette): static
    {
        if (!$this->dettes->contains($dette)) {
            $this->dettes->add($dette);
            $dette->setClient($this);
        }

        return $this;
    }

    public function removeDette(Dette $dette): static
    {
        if ($this->dettes->removeElement($dette)) {
            // set the owning side to null (unless already changed)
            if ($dette->getClient() === $this) {
                $dette->setClient(null);
            }
        }

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
            $demandeDette->setClient($this);
        }

        return $this;
    }

    public function removeDemandeDette(DemandeDette $demandeDette): static
    {
        if ($this->demandeDettes->removeElement($demandeDette)) {
            // set the owning side to null (unless already changed)
            if ($demandeDette->getClient() === $this) {
                $demandeDette->setClient(null);
            }
        }

        return $this;
    }
}
