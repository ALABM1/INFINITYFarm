<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;

#[Table(name: "Users")]
#[ORM\Entity]
class Users
{
    public function __toString()
    {
        return $this->nom;
    }

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    private ?int $id;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $nom;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $prenom;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $mail;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $role;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $ville;

    #[ORM\Column(type: "string", length: 10, nullable: true)]
    private ?string $sexe;

    #[ORM\Column(type: "string", length: 100, nullable: true)]
    private ?string $specialite;

    #[ORM\Column(type: "blob", nullable: true)]
    private ?string $certification;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): static
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(?string $mail): static
    {
        $this->mail = $mail;
        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(?string $role): static
    {
        $this->role = $role;
        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(?string $ville): static
    {
        $this->ville = $ville;
        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(?string $sexe): static
    {
        $this->sexe = $sexe;
        return $this;
    }

    public function getSpecialite(): ?string
    {
        return $this->specialite;
    }

    public function setSpecialite(?string $specialite): static
    {
        $this->specialite = $specialite;
        return $this;
    }

    public function getCertification(): ?string
    {
        return $this->certification;
    }

    public function setCertification(?string $certification): static
    {
        $this->certification = $certification;
        return $this;
    }
}
