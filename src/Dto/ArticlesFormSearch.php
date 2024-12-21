<?php

namespace App\Dto;

class ArticlesFormSearch
{
    private ?string $libelle = null;
    private ?string $etat = 'Tout'; //Disponible, En Rupture ou Tout

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

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

}


?>