<?php

namespace App\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueEntity('nomparc', message: 'Ce nom de parc est déjà existé.')]
#[UniqueEntity('adresseparc', message: 'Cette adresse de parc est déjà existé.')]
#[Table(name: "parc")]
#[Entity]
class Parc
{
    #[Id]
    #[Column(name: "idparc", type: "integer", nullable: false)]
    #[GeneratedValue(strategy: "IDENTITY")]
    private int $idparc;


    #[Column(name: "nomparc", type: "string", length: 255, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 3,
        max: 50,
        minMessage: 'Le nom du parc doit avoir au moins 3 caractères.',
        maxMessage: 'Le nom du parc ne peut pas dépasser 50 caractères.',
    )]
    private string $nomparc;

    #[Column(name: "adresseParc", type: "string", length: 255, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 6,
        max: 70,
        minMessage: 'adresse de parc au moin de 6 carecteres ',
        maxMessage: 'adressse de parc au max de 70 carecteres',
    )]
    private string $adresseparc;

    #[Column(name: "superficieParc", type: "float", precision: 10, scale: 0, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'float', message: 'La superficie doit être un nombre réel.')]
    private float $superficieparc;

    /**
     * @return int
     */
    public function getIdparc(): int
    {
        return $this->idparc;
    }

    /**
     * @return string
     */
    public function getNomparc(): string
    {
        return $this->nomparc;
    }

    /**
     * @return string
     */
    public function getAdresseparc(): string
    {
        return $this->adresseparc;
    }

    /**
     * @return float
     */
    public function getSuperficieparc(): float
    {
        return $this->superficieparc;
    }

    /**
     * @param int $idparc
     */
    public function setIdparc(int $idparc): void
    {
        $this->idparc = $idparc;
    }

    /**
     * @param string $nomparc
     */
    public function setNomparc(string $nomparc): void
    {
        $this->nomparc = $nomparc;
    }

    /**
     * @param string $adresseparc
     */
    public function setAdresseparc(string $adresseparc): void
    {
        $this->adresseparc = $adresseparc;
    }

    /**
     * @param float $superficieparc
     */
    public function setSuperficieparc(float $superficieparc): void
    {
        $this->superficieparc = $superficieparc;
    }
    public function validateNomUnique(ExecutionContextInterface $context)
    {
        $parcRepository = $this->entityManager->getRepository(Parc::class);
        $existingParc = $parcRepository->findOneBy(['nomparc' => $this->nomparc]);

        if ($existingParc) {
            $context->buildViolation($context->getConstraint()->payload['uniqueNomMessage'])
                ->atPath('nomparc')
                ->addViolation('Ce nom de parc est déjà utilisé.');
        }
    }



}