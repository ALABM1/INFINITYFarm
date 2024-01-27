<?php

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;


use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumns;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\Table;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;


#[UniqueEntity('nommat', message: 'Ce nom de matériel est déjà existé')]
#[ORM\Table(name: 'materiel', indexes: [new ORM\Index(name: 'idparc', columns: ['idparc'])])]
#[Entity]
class Materiel
{
    #[Id]
    #[Column(name: "idMat", type: "integer", nullable: false)]
    #[GeneratedValue(strategy: "IDENTITY")]
    private int $idmat;

    #[Column(name: "nommat", type: "string", length: 255, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 3,
        max: 50,
        minMessage: 'Le nom du parc doit avoir au moins 3 caractères.',
        maxMessage: 'Le nom du parc ne peut pas dépasser 50 caractères.',
    )]
    private string $nommat;

    #[Column(name: "etatMat", type: "string", length: 255, nullable: false)]
    private string $etatmat;

    #[Column(name: "QuantiteMat", type: "float", precision: 10, scale: 0, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Type(
        type: 'float',
        message: 'la quantite doit etre un nombre reel',
    )]
    private float $quantitemat;

    #[Column(name: "dateAjout", type: "date", nullable: false)]
    private \DateTimeInterface $dateajout;

    #[Column(name: "nomparc", type: "string", length: 255, nullable: false)]
    private string $nomparc;
    #[ManyToOne(targetEntity: Parc::class)]
    #[JoinColumn(name: "idparc", referencedColumnName: "idparc")]
    private Parc $idparc;

    /**
     * @return int
     */
    public function getIdmat(): int
    {
        return $this->idmat;
    }

    /**
     * @return string
     */
    public function getNommat(): string
    {
        return $this->nommat;
    }

    /**
     * @return string
     */
    public function getEtatmat(): string
    {
        return $this->etatmat;
    }

    /**
     * @return float
     */
    public function getQuantitemat(): float
    {
        return $this->quantitemat;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getDateajout(): \DateTimeInterface
    {
        return $this->dateajout;
    }

    /**
     * @return string
     */
    public function getNomparc(): string
    {
        return $this->nomparc;
    }

    /**
     * @return Parc
     */
    public function getIdparc(): Parc
    {
        return $this->idparc;
    }

    /**
     * @param int $idmat
     */
    public function setIdmat(int $idmat): void
    {
        $this->idmat = $idmat;
    }

    /**
     * @param string $nommat
     */
    public function setNommat(string $nommat): void
    {
        $this->nommat = $nommat;
    }

    /**
     * @param string $etatmat
     */
    public function setEtatmat(string $etatmat): void
    {
        $this->etatmat = $etatmat;
    }

    /**
     * @param float $quantitemat
     */
    public function setQuantitemat(float $quantitemat): void
    {
        $this->quantitemat = $quantitemat;
    }

    /**
     * @param \DateTimeInterface $dateajout
     */
    public function setDateajout(\DateTimeInterface $dateajout): void
    {
        $this->dateajout = $dateajout;
    }

    /**
     * @param string $nomparc
     */
    public function setNomparc(string $nomparc): void
    {
        $this->nomparc = $nomparc;
    }

    /**
     * @param Parc $idparc
     */
    public function setIdparc(Parc $idparc): void
    {
        $this->idparc = $idparc;
    }

}