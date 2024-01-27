<?php

// src/Controller/StatisticController.php

namespace App\Controller;

use App\Entity\Ressource;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/statistic')]
class StatisticController extends AbstractController
{
    #[Route('/resources-per-terrain', name: 'app_statistic_resources_per_terrain')]
    public function resourcesPerTerrain(EntityManagerInterface $entityManager): Response
    {
        $query = $entityManager->createQuery((new Ressource())->getCountByTerrainQuery());
        $result = $query->getResult();

        return $this->render('statistic/resources_per_terrain.html.twig', [
            'result' => $result,
        ]);
    }
}

