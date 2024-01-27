<?php

namespace App\Repository;

use App\Entity\Categtrans;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CategTransRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Categtrans::class);
    }

    /**
     * Récupère les noms des catégories.
     *
     * @return array
     */
    public function findAllNomCateg()
    {
        return $this->createQueryBuilder('c')
            ->select('c.nomCatTra')
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }
}