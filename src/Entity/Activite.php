<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'activite')]
#[ORM\Entity]
class Activite
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(name: 'idAct', type: 'integer', nullable: false)]
    private int $idact;
    #[Assert\NotBlank(message: "L'activite ne peut pas être vide.")]
    #[Assert\Length(min: 5, minMessage: "L'objet de l'activité doit avoir au moins {{ limit }} caractères.")]
    #[ORM\Column(name: 'objetAct', type: 'string', length: 255, nullable: false)]
    private string $objetact;
    #[Assert\NotBlank(message: "La description ne peut pas être vide.")]
    #[Assert\Length(min: 10, minMessage: "La description de l'activité doit avoir au moins {{ limit }} caractères.")]
    #[ORM\Column(name: 'descriptionAct', type: 'string', length: 255, nullable: false)]
    private string $descriptionact;
    #[Assert\NotBlank(message: "Le distinataire ne peut pas être vide.")]
    #[Assert\Regex(
        pattern: "/^[a-zA-Z]/",
        message: "Le destinataire doit commencer par une lettre."
    )]
    #[ORM\Column(name: 'distAct', type: 'string', length: 255, nullable: false)]
    private string $distact;
    #[Assert\NotBlank(message: "L'email ne peut pas être vide.")]
    #[Assert\Email(message: "Le format de l'email destinataire n'est pas valide.")]
    #[ORM\Column(name: 'emailDist', type: 'string', length: 255, nullable: false)]
    private string $emaildist;
    #[Assert\NotBlank(message: "Le species ne peut pas être vide.")]
    #[ORM\Column(name: 'speciesRES', type: 'string', length: 255, nullable: false)]
    private string $speciesres;
    #[Assert\NotBlank(message: "L'etat ne peut pas être vide.")]
    #[Assert\Choice(choices: ['en attente', 'terminé'], message: "L'état de l'activité doit être 'en attente' ou 'terminé'.")]
    #[ORM\Column(name: 'etatAct', type: 'string', length: 255, nullable: false)]
    private string $etatact;

    // Getters and setters...

    public function getIdact(): ?int
    {
        return $this->idact;
    }

    public function getObjetact(): ?string
    {
        return $this->objetact;
    }

    public function setObjetact(string $objetact): static
    {
        $this->objetact = $objetact;

        return $this;
    }

    public function getDescriptionact(): ?string
    {
        return $this->descriptionact;
    }

    public function setDescriptionact(string $descriptionact): static
    {
        $this->descriptionact = $descriptionact;

        return $this;
    }

    public function getDistact(): ?string
    {
        return $this->distact;
    }

    public function setDistact(string $distact): static
    {
        $this->distact = $distact;

        return $this;
    }

    public function getEmaildist(): ?string
    {
        return $this->emaildist;
    }

    public function setEmaildist(string $emaildist): static
    {
        $this->emaildist = $emaildist;

        return $this;
    }

    public function getSpeciesres(): ?string
    {
        return $this->speciesres;
    }

    public function setSpeciesres(string $speciesres): static
    {
        $this->speciesres = $speciesres;

        return $this;
    }

    public function getEtatact(): ?string
    {
        return $this->etatact;
    }

    public function setEtatact(string $etatact): static
    {
        $this->etatact = $etatact;

        return $this;
    }

}
