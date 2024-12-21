<?php

namespace App\Dto;

class DemandeFormSearch
{
    private ?string $surname = null;
 

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(?string $surname): self
    {
        $this->surname = $surname;
        return $this;
    }

}
