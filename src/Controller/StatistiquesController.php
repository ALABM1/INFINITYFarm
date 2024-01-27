<?php

namespace App\Controller;

use App\Entity\Parc;
use App\Repository\MaterielRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/stat')]
class StatistiquesController extends AbstractController
{

    #[Route('/index', name: 'app_statistiques')]
    public function statistiques(EntityManagerInterface $entityManager,MaterielRepository $repository): Response
    {
        $parcRepository = $entityManager->getRepository(Parc::class);
        $parcs = $parcRepository->findBy([], ['superficieparc' => 'ASC']); // Tri croissant par superficie

        // Formattez les données selon les besoins de votre graphique
        $parcData = [
            'labels' => [], // Les étiquettes pour l'axe des X
            'values' => [], // Les valeurs correspondantes
        ];
        foreach ($parcs as $parc) {
            $parcData['labels'][] = $parc->getNomParc();
            $parcData['values'][] = $parc->getSuperficieparc();
        }
        return $this->render('statistiques/index.html.twig', [
            'parcData' => json_encode($parcData),
        ]);
    }
    #[Route('/index_back', name: 'app_statistiques_back')]
    public function statistiquesBack(EntityManagerInterface $entityManager,MaterielRepository $repository): Response
    {
        $parcRepository = $entityManager->getRepository(Parc::class);
        $parcs = $parcRepository->findBy([], ['superficieparc' => 'ASC']); // Tri croissant par superficie

        // Formattez les données selon les besoins de votre graphique
        $parcData = [
            'labels' => [], // Les étiquettes pour l'axe des X
            'values' => [], // Les valeurs correspondantes
        ];
        foreach ($parcs as $parc) {
            $parcData['labels'][] = $parc->getNomParc();
            $parcData['values'][] = $parc->getSuperficieparc();
        }

        return $this->render('back_parc/stat.html.twig', [
            'parcData' => json_encode($parcData),
        ]);
    }
    #[Route('/materiel', name: 'app_statistiques_mat')]
    public function statistiqueMateriels(Request $request, MaterielRepository $materielRepository): Response
    {
        $nomParc = $request->query->get('nomparc');


        // Récupérer les données nécessaires depuis le repository
        $parcs = $materielRepository->getParcsWithMaterialCount($nomParc);

        // Format des données pour le graphique
        $parcLabels = [];
        $materialCounts = [];

        foreach ($parcs as $parc) {
            // Assurez-vous que les clés existent avant de les utiliser
            if (isset($parc['nomparc'], $parc['material_count'])) {
                $parcLabels[] = $parc['nomparc'];
                $materialCounts[] = $parc['material_count'];
            }
        }

        // Rendre la vue Twig avec les données du graphique
        return $this->render('statistiques/materiel.html.twig', [
            'parcLabels' => json_encode($parcLabels),
            'materialCounts' => json_encode($materialCounts),
        ]);
    }
}
