<?php

namespace App\Entity;
#use App\Repository\TransactionRespository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;

#[Table(name: "Transaction")]
#[ORM\Entity]
class Transaction
{
    #[ORM\Column]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    private ?int $idTra = null;

    #[ORM\Column(length: 150)]
    private ?string $categTra = null;

    #[ORM\Column(length: 150)]
    private ?string $typeTra = null;

    #[ORM\Column]
    private ?\DateTime $dateTra = null;

    #[ORM\Column(type: 'integer')]
    private ?int $montant = null;

    public function getIdTra(): ?int
    {
        return $this->idTra;
    }

    public function getCategTra(): ?string
    {
        return $this->categTra;
    }

    public function setCategTra(string $categTra): static
    {
        $this->categTra = $categTra;

        return $this;
    }

    public function getTypeTra(): ?string
    {
        return $this->typeTra;
    }

    public function setTypeTra(string $typeTra): static
    {
        $this->typeTra = $typeTra;

        return $this;
    }

    public function getDateTra(): ?\DateTimeInterface
    {
        return $this->dateTra;
    }

    public function setDateTra(\DateTimeInterface $dateTra): static
    {
        $this->dateTra = $dateTra;

        return $this;
    }

    public function getMontant(): ?int
    {
        return $this->montant;
    }

    public function setMontant(int $montant): static
    {
        $this->montant = $montant;

        return $this;
    }


}
