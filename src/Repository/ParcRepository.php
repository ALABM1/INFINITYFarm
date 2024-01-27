<?php
namespace App\Repository;

use App\Entity\Materiel;
use App\Form\SearchParcType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Parc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ParcRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Parc::class);
    }

    public function getAllParcNames()
    {
        $parcs = $this->findAll();

        // Extrayez uniquement les noms de parc dans une liste
        $parcNames = array_map(function($parc) {
            return $parc->getNomParc();
        }, $parcs);

        return $parcNames;

    }
    public function getIdParcByName($nomParc): int
    {
        $parc = $this->findOneBy(['nomParc' => $nomParc]);
            return $parc->getIdParc();

    }
    public function findAllSortedByNameAsc()
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.nomparc', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAllSortedBySuperficieDesc()
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.superficieparc', 'DESC')
            ->getQuery()
            ->getResult();
    }


    /**
     * Récupère les matériels associés à un parc avec l'idParc donné.
     *
     * @param int $idParc
     * @return array
     */
    /*
    public function findMaterielsByParcId(int $idParc): array
    {
        return $this->createQueryBuilder('parc')
            ->select('materiel')
            ->join('parc.materiels', 'materiel')
            ->where('parc.idParc = :idParc')
            ->setParameter('idParc', $idParc)
            ->getQuery()
            ->getResult();
    }

*/


    public function searchByCriteria(array $criteria): array
    {
        $queryBuilder = $this->createQueryBuilder('p');

        if (!empty($criteria['nomparc'])) {
            $queryBuilder->andWhere('p.nomparc LIKE :nomparc')
                ->setParameter('nomparc', '%' . $criteria['nomparc'] . '%');
        }

        if (!empty($criteria['adresseparc'])) {
            $queryBuilder->andWhere('p.adresseparc LIKE :adresseparc')
                ->setParameter('adresseparc', '%' . $criteria['adresseparc'] . '%');
        }

        if (!empty($criteria['superficieparc'])) {
            $queryBuilder->andWhere('p.superficieparc = :superficieparc')
                ->setParameter('superficieparc', $criteria['superficieparc']);
        }

        return $queryBuilder->getQuery()->getResult();
    }



}
