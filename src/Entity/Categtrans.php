<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use Symfony\Component\Validator\Constraints as Assert;

#[Table(name: "Categtrans")]
#[ORM\Entity]
class Categtrans
{
    #[ORM\Column]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    private ?int $idCatTra;

    #[ORM\Column(length: 150)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 3,
        max: 50,
        minMessage: 'Le nom de catégorie doit avoir au moins 3 caractères.',
        maxMessage: 'Le nom de catégorie ne peut pas dépasser 50 caractères.',
    )]

    private ?string $nomCatTra = null;

    #[ORM\Column(length: 250)]
    #[Assert\NotBlank]
    private ?string $descripCatTra = null;


    public function getIdCatTra(): ?int
    {
        return $this->idCatTra;
    }

    public function getNomCatTra(): ?string
    {
        return $this->nomCatTra;
    }

    public function setNomCatTra(string $nomCatTra): static
    {
        $this->nomCatTra = $nomCatTra;

        return $this;
    }

    public function getDescripCatTra(): ?string
    {
        return $this->descripCatTra;
    }

    public function setDescripCatTra(string $descripCatTra): static
    {
        $this->descripCatTra = $descripCatTra;

        return $this;
    }


}
