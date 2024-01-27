<?php

// src/Repository/TransactionRepository.php

namespace App\Repository;

use App\Entity\Transaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    public function findBySearchCriteria(array $criteria): array
    {
        $queryBuilder = $this->createQueryBuilder('t');
    
        if (!empty($criteria['category'])) {
            $queryBuilder->andWhere('t.categTra LIKE :category')
                ->setParameter('category', '%' . $criteria['category'] . '%');
        }
    
        if (!empty($criteria['type'])) {
            $queryBuilder->andWhere('t.typeTra LIKE :type')
                ->setParameter('type', '%' . $criteria['type'] . '%');
        }
    
        if (!empty($criteria['startDate'])) {
            $queryBuilder->andWhere('t.dateTra >= :startDate')
                ->setParameter('startDate', $criteria['startDate']);
        }
    
        if (!empty($criteria['endDate'])) {
            $queryBuilder->andWhere('t.dateTra <= :endDate')
                ->setParameter('endDate', $criteria['endDate']);
        }
        return $queryBuilder->getQuery()->getResult();

    }
    
    // Dans votre TransactionRepository

public function calculateSumByType($type)
{
    return $this->createQueryBuilder('t')
    ->select('SUM(t.montant)')
    ->where('t.typeTra = :type')
    ->setParameter('type', $type)
    ->getQuery()
    ->getSingleScalarResult();
}
}


