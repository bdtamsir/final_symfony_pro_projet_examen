<?php

namespace App\Dto;

class PaiementFormSearch
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
