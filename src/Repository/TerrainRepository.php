<?php

namespace App\Repository;

use App\Entity\Terrain;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TerrainRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Terrain::class);
    }

    public function searchByCriteria(array $criteria): array
    {
        $queryBuilder = $this->createQueryBuilder('t');

        if (!empty($criteria['nomterrain'])) {
            $queryBuilder->andWhere('t.nomterrain LIKE :nomterrain')
                ->setParameter('nomterrain', '%' . $criteria['nomterrain'] . '%');
        } else {
            // Ajouter ce bloc si le champ est vide
            $queryBuilder->andWhere('1=1');
        }

        if (!empty($criteria['localisation'])) {
            $queryBuilder->andWhere('t.localisation LIKE :localisation')
                ->setParameter('localisation', '%' . $criteria['localisation'] . '%');
        }

        if (!empty($criteria['superficie'])) {
            $queryBuilder->andWhere('t.superficie = :superficie')
                ->setParameter('superficie', $criteria['superficie']);
        }

        return $queryBuilder->getQuery()->getResult();
    }

}
