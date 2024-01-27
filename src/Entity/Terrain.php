<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Length;

#[ORM\Table(name: 'terrain')]
#[ORM\Entity]
#[UniqueEntity(
    fields: ['nomterrain'],
    message: 'Ce nom de terrain est déjà utilisé.'
)]
class Terrain
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(name: 'idTerrain', type: 'integer', nullable: false)]
    private int $idterrain;

    #[Assert\NotBlank(message: "Le nom du terrain ne peut pas être vide.")]
    #[Assert\Regex(
        pattern: "/^[a-zA-Z]+$/",
        message: "Le nom du terrain doit commencer par des lettres."
    )]
    #[ORM\Column(name: 'nomTerrain', type: 'string', length: 255, nullable: false)]
    private string $nomterrain;

    #[Assert\NotBlank(message: "La localisation du terrain ne peut pas être vide.")]
    #[Length(
        min: 3,
        minMessage: "La localisation doit contenir au moins 3 caractères."
    )]
    #[ORM\Column(name: 'localisation', type: 'string', length: 255, nullable: false)]
    private string $localisation;
    #[Assert\NotBlank(message: "La superficie ne peut pas être vide.")]
    #[Assert\GreaterThan(value: 0, message: "La superficie doit être supérieure à 0.")]
    #[ORM\Column(name: 'superficie', type: 'float', precision: 10, scale: 0, nullable: false)]
    private float $superficie;

    #[ORM\OneToMany(mappedBy: 'idterrain', targetEntity: Ressource::class)]
    private Collection $ressources;

    public function __construct()
    {
        $this->ressources = new ArrayCollection();
    }
    public function getIdterrain(): ?int
    {
        return $this->idterrain;
    }

    public function getNomterrain(): ?string
    {
        return $this->nomterrain;
    }

    public function setNomterrain(string $nomterrain): static
    {
        $this->nomterrain = $nomterrain;

        return $this;
    }

    public function getLocalisation(): ?string
    {
        return $this->localisation;
    }

    public function setLocalisation(string $localisation): static
    {
        $this->localisation = $localisation;

        return $this;
    }

    public function getSuperficie(): ?float
    {
        return $this->superficie;
    }

    public function setSuperficie(float $superficie): static
    {
        $this->superficie = $superficie;

        return $this;
    }
    #[ORM\OneToMany(mappedBy: 'idterrain', targetEntity: Ressource::class)]
    public function getRessources(): Collection
    {
        return $this->ressources;
    }

    public function __toString()
    {
        return $this->nomterrain; // Adjust this based on the property you want to display as a string
    }
}
